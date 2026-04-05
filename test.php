<?php
include 'includes/header.php'; 
require 'vendor/autoload.php'; 

$auditStatus = ""; // Stores: Verified, Manual, or Failed
$displayMessage = "";

if (isset($_POST['submit_listing'])) {
    $url = $_POST['event_url'];
    $isFatsoma = (strpos($url, 'fatsoma.com') !== false);

    if ($isFatsoma) {
        // --- AUTOMATED AUDIT FOR FATSOMA ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
        $html = curl_exec($ch);
        curl_close($ch);

        $scrapedEventName = "";
        $scrapedTiers = [];

        if ($html) {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            $scripts = $xpath->query('//script[@type="application/ld+json"]');

            foreach ($scripts as $script) {
                $json = json_decode($script->nodeValue, true);
                if (isset($json['@type']) && $json['@type'] === 'Event') {
                    $scrapedEventName = $json['name'];
                    if (isset($json['offers'])) {
                        $offers = isset($json['offers'][0]) ? $json['offers'] : [$json['offers']];
                        foreach ($offers as $o) if (isset($o['name'])) $scrapedTiers[] = $o['name'];
                    }
                }
            }
        }

        // PDF Check
        if (isset($_FILES['ticket_pdf']) && !empty($scrapedEventName)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($_FILES['ticket_pdf']['tmp_name']);
                $pdfText = preg_replace('/\s+/', ' ', $pdf->getText());
                
                $eventFound = (strpos($pdfText, $scrapedEventName) !== false);
                $tierFound = false;
                foreach ($scrapedTiers as $tier) {
                    if (strpos($pdfText, $tier) !== false) { $tierFound = true; break; }
                }

                if ($eventFound && $tierFound) {
                    $auditStatus = "Verified";
                    $displayMessage = "<span class='badge bg-success'>✅ Verified Fatsoma Ticket</span>";
                } else {
                    $auditStatus = "Failed";
                    $displayMessage = "<span class='badge bg-danger'>❌ Verification Mismatch</span>";
                }
            } catch (Exception $e) { $auditStatus = "Error"; }
        }
    } else {
        // --- MANUAL ENTRY FOR NON-FATSOMA ---
        $auditStatus = "Manual";
        $displayMessage = "<span class='badge bg-warning text-dark'>⚠️ Manual Entry Ticket</span>";
    }
}
?>

<div class="container mt-5">
    <h3>List a Ticket</h3>
    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <input type="url" name="event_url" class="form-control mb-3" placeholder="Paste Fatsoma or Event Link" required>
        <input type="file" name="ticket_pdf" class="form-control mb-3">
        <button type="submit" name="submit_listing" class="btn btn-primary">Create Listing</button>
    </form>

    <div class="mt-4">
        <h4>Listing Preview:</h4>
        <div class="border p-3 rounded">
            <p><strong>Status:</strong> <?php echo $displayMessage ?: "Enter details above"; ?></p>
            <p class="text-muted" style="font-size: 0.85em;">
                <?php 
                if ($auditStatus == "Verified") echo "This ticket has been digitally audited against the primary vendor.";
                if ($auditStatus == "Manual") echo "This is a manual entry. Our system cannot verify this file automatically.";
                ?>
            </p>
        </div>
    </div>
</div>