<?php 
session_start();

require_once '../config/database.php';
require_once '../vendor/autoload.php'; 

if (!isset($_SESSION['user_id'])) {
    include '../includes/header.php';
    ?>
    <div class="min-h-[60vh] flex items-center justify-center bg-[#F8FAFC] px-6">
        <div class="max-w-md w-full bg-white rounded-3xl shadow-sm border border-[#E2E8F0] p-10 text-center">
            <h1 class="text-2xl font-bold text-[#0A192F] mb-3">Sign in required</h1>
            <p class="text-[#64748B] text-sm mb-8">You need to be logged in to your student account to sell tickets.</p>
            <div class="flex flex-col gap-3">
                <a href="/student2student/auth/login.php" class="py-4 bg-[#0052FF] text-white rounded-xl font-bold text-sm no-underline">Login</a>
                <a href="/student2student/auth/register.php" class="py-4 bg-white text-[#0A192F] border border-[#E2E8F0] rounded-xl font-bold text-sm no-underline">Create Account</a>
            </div>
        </div>
    </div>
    <?php
    include '../includes/footer.php';
    exit();
}

$error = null;
unset($_SESSION['scraped_ticket']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ticket_pdf'])) {
    
    $file         = $_FILES['ticket_pdf'];
    $tmpPath      = $file['tmp_name'];
    $originalName = htmlspecialchars($file['name']);
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    
    if ($mime === 'application/pdf') {
        
        // 1. Generate the unique fingerprint for this specific file
        $p_hash   = hash_file('sha256', $tmpPath);
        $fileName = $p_hash . ".pdf"; 
        $uploadDir = "../uploads/tickets/";
        $destPath = $uploadDir . $fileName;

        // --- NEW DUPLICATE CHECK START ---
        // Check if this exact file hash already exists in our database
        $check_stmt = $conn->prepare("SELECT id FROM tickets WHERE pdf_hash = ? OR pdf_hash = ? LIMIT 1");
        $pure_hash = $p_hash; // checking both with and without .pdf just in case
        $check_stmt->bind_param("ss", $pure_hash, $fileName);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "This is a duplicated ticket. It has already been listed on the marketplace.";
        } else {
            // --- NEW DUPLICATE CHECK END ---

            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($tmpPath, $destPath)) {
                
                $eventUrl = $_POST['event_url'] ?? '';
                $isFatsoma = strpos($eventUrl, 'fatsoma.com') !== false;

                if ($isFatsoma) {
                    $scrapedMeta = ['name' => '', 'tiers' => [], 'venue' => '', 'date' => ''];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $eventUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0');
                    $html = curl_exec($ch);
                    curl_close($ch);

                    if ($html) {
                        $dom = new DOMDocument();
                        @$dom->loadHTML($html);
                        $xpath   = new DOMXPath($dom);
                        $scripts = $xpath->query('//script[@type="application/ld+json"]');
                        
                        foreach ($scripts as $script) {
                            $json = json_decode($script->nodeValue, true);
                            if (is_array($json) && isset($json['@type']) && $json['@type'] === 'Event') {
                                $scrapedMeta['name']  = $json['name'] ?? '';
                                $scrapedMeta['venue'] = $json['location']['name'] ?? '';
                                $scrapedMeta['date']  = $json['startDate'] ?? '';
                                
                                if (isset($json['offers'])) {
                                    $offers = isset($json['offers'][0]) ? $json['offers'] : [$json['offers']];
                                    foreach ($offers as $o) {
                                        if (isset($o['name'])) {
                                            $scrapedMeta['tiers'][] = ['name' => $o['name'], 'price' => $o['price'] ?? '0.00'];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    try {
                        $parser  = new \Smalot\PdfParser\Parser();
                        $pdf     = $parser->parseFile($destPath);
                        $pdfText = $pdf->getText();
                        
                        $stripEmojis = function(string $str): string {
                            $str = preg_replace('/[^\x{0020}-\x{007E}\x{00A0}-\x{024F}]/u', '', $str);
                            return trim(preg_replace('/\s+/', ' ', $str));
                        };

                        $cleanPdfText    = $stripEmojis(preg_replace('/\s+/', ' ', $pdfText));
                        $cleanTargetName = $stripEmojis(preg_replace('/\s+/', ' ', $scrapedMeta['name']));

                        $nameMatch = (!empty($cleanTargetName) && strpos($cleanPdfText, $cleanTargetName) !== false);
                        
                        if ($nameMatch) {
                            $matchedPrice = '0.00';
                            foreach ($scrapedMeta['tiers'] as $tier) {
                                if (strpos($cleanPdfText, $tier['name']) !== false) {
                                    $matchedPrice = $tier['price'];
                                    break;
                                }
                            }

                            $_SESSION['scraped_ticket'] = [
                                'event_name'   => $scrapedMeta['name'],
                                'venue'        => $scrapedMeta['venue'],
                                'event_date'   => $scrapedMeta['date'],
                                'retail_price' => $matchedPrice,
                                'is_verified'  => true,
                                'upload_name'  => $originalName,
                                'p_hash'       => $fileName 
                            ];
                            
                            session_write_close();
                            header("Location: ticket_details.php");
                            exit();
                        } else {
                            unlink($destPath);
                            $error = "Verification failed: The PDF and link do not match.";
                        }
                    } catch (Exception $e) {
                        unlink($destPath);
                        $error = "Could not read PDF. Please use the original file.";
                    }
                } else {
                    $_SESSION['scraped_ticket'] = [
                        'event_name'   => '', 'venue' => '', 'event_date' => '', 'retail_price' => '0.00',
                        'is_verified'  => false, 'upload_name' => $originalName, 'p_hash' => $fileName,
                        'manual_msg'   => "Manual entry required for this provider."
                    ];
                    session_write_close();
                    header("Location: ticket_details.php");
                    exit();
                }
            } else {
                $error = "Failed to save the ticket file to the server.";
            }
        } // End of duplicate check else
        $check_stmt->close();
    } else {
        $error = "Please upload a valid PDF file.";
    }
}

include '../includes/header.php'; 
?>
<div class="min-h-screen bg-[#F8FAFC] pb-24">
    <div class="max-w-2xl mx-auto px-6 pt-12">
        <div class="flex items-center justify-between mb-10">
            <a href="javascript:history.back()" class="text-sm font-bold text-[#64748B] no-underline hover:text-[#0052FF]">Back</a>
            <div class="flex gap-2">
                <div class="w-2 h-2 rounded-full bg-[#0052FF]"></div>
                <div class="w-2 h-2 rounded-full bg-[#E2E8F0]"></div>
                <div class="w-2 h-2 rounded-full bg-[#E2E8F0]"></div>
            </div>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 text-sm p-4 rounded-xl mb-6 border border-red-100 font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl border border-[#E2E8F0] p-8 md:p-12">
            <div class="mb-10">
                <h1 class="text-2xl font-bold text-[#0A192F] mb-2">Sell a ticket</h1>
                <p class="text-sm text-[#64748B]">Provide the Fatsoma link and your ticket file to begin.</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#0A192F]">Event Link</label>
                    <input type="url" name="event_url" required 
                        class="w-full p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] focus:bg-white outline-none transition-all text-sm"
                        placeholder="https://www.fatsoma.com/events/...">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#0A192F]">Ticket File (PDF)</label>
                    <div class="flex items-center gap-4">
                        <label class="flex-shrink-0 cursor-pointer py-3 px-6 bg-[#0A192F] text-white rounded-xl text-sm font-bold hover:bg-[#0052FF] transition-all">
                            Choose File
                            <input type="file" name="ticket_pdf" id="ticket_pdf" class="hidden" accept="application/pdf" required onchange="displayFileName()" />
                        </label>
                        <span id="file-name-display" class="text-sm text-[#64748B] truncate">No file selected</span>
                    </div>
                    <p class="text-[10px] text-[#94A3B8] mt-2 italic">Official PDF files only, max 5MB.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-[#0052FF] text-white rounded-xl font-bold text-sm hover:shadow-lg transition-all">
                        Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function displayFileName() {
    const input = document.getElementById('ticket_pdf');
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.innerText = input.files[0].name;
        display.classList.remove('text-[#64748B]');
        display.classList.add('text-[#10B981]', 'font-medium');
    }
}
</script>

<?php include '../includes/footer.php'; ?>