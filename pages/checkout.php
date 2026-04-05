<?php
// Connect to the database and grab the header file
require_once 'config/database.php';
include './includes/header.php';

// Get the specific ticket ID from the URL link
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get ticket details and seller info from the database using a join
$query = "SELECT t.*, u.username, u.points 
          FROM tickets t 
          JOIN users u ON t.seller_id = u.id 
          WHERE t.id = ? AND t.status = 'active' 
          LIMIT 1";

// Prepare the sql statement to prevent any database errors
$stmt = $conn->prepare($query);
// Bind the ticket id variable to the prepared sql query
$stmt->bind_param("i", $ticket_id);
// Run the query against the database
$stmt->execute();
// Get the result back from the database execution
$result = $stmt->get_result();
// Store the fetched ticket data into a simple variable array
$ticket = $result->fetch_assoc();

// Stop the script and show an alert if the ticket is missing
if (!$ticket) {
    echo "<script>alert('Ticket not found or no longer available.'); window.location.href='index.php';</script>";
    exit;
}

// Store the selling price of the ticket in a variable
$price = $ticket['selling_price'];
// Set a fixed small fee for processing the student payment
$fee = 1.50;
// Add the price and fee together to get the total cost
$total = number_format((float)$price + (float)$fee, 2);

// Helper function to decide which medal badge the seller has earned
function getSellerTier($pts) {
    if ($pts >= 500) return ['text' => 'Gold', 'css' => 'text-yellow-600'];
    if ($pts >= 100) return ['text' => 'Silver', 'css' => 'text-slate-500'];
    if ($pts >= 10)  return ['text' => 'Bronze', 'css' => 'text-orange-600'];
    return ['text' => 'New', 'css' => 'text-blue-500'];
}

// Calculate the seller tier using the points from the database
$tier = getSellerTier($ticket['points']);
?>

<div class="bg-white min-h-screen font-sans">
    <div class="mx-auto px-6 py-16 max-w-6xl">
        
        <div class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-[#0A192F] tracking-tight uppercase">
                Review Purchase
            </h1>
            <div class="h-1.5 w-24 bg-[#0052FF] mt-6 mx-auto rounded-full"></div>
        </div>

        <div class="flex flex-col items-center">
            <div class="w-full flex flex-col md:flex-row gap-10 items-stretch justify-center">
                
                <div class="w-full max-w-[370px] bg-white border border-[#E2E8F0] rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-xs font-bold uppercase">
                            <?= substr($ticket['username'], 0, 1) ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-[#0A192F]">@<?= $ticket['username'] ?></span>
                            <span class="text-[10px] font-bold <?= $tier['css'] ?> uppercase tracking-widest">
                                <?= $tier['text'] ?> Seller
                            </span>
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-6 flex items-center justify-center border border-[#F1F5F9]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#CBD5E1]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>

                    <div class="text-lg font-black text-[#0A192F] mb-1 uppercase truncate">
                        <?= $ticket['event_name'] ?>
                    </div>
                    <div class="text-sm font-bold text-[#64748B] mb-8 uppercase tracking-tight">
                        <?= $ticket['event_location'] ?>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-[#F1F5F9]">
                        <span class="text-2xl font-black text-[#0A192F]">£<?= number_format($price, 2) ?></span>
                    </div>
                </div>

                <div class="w-full max-w-[420px] bg-white border border-[#E2E8F0] rounded-[2rem] p-10 shadow-sm flex flex-col">
                    <h2 class="text-2xl font-black text-[#0A192F] mb-8 uppercase tracking-tight">Order Summary</h2>
                    
                    <div class="space-y-5 mb-10">
                        <div class="flex justify-between text-base font-bold text-[#64748B] uppercase tracking-tighter">
                            <span>Ticket Listing</span>
                            <span class="text-[#0A192F]">£<?= number_format($price, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-[#64748B] uppercase tracking-tighter">
                            <span>Processing Fee</span>
                            <span class="text-[#0A192F]">£<?= number_format($fee, 2) ?></span>
                        </div>
                        <div class="border-t border-[#F1F5F9] pt-6 flex justify-between items-center">
                            <span class="text-lg font-black text-[#0A192F] uppercase">Total</span>
                            <span class="text-4xl font-black text-[#0052FF]">£<?= $total ?></span>
                        </div>
                    </div>

                    <form action="success.php" method="POST" class="mt-auto">
                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                        <button type="submit" class="w-full bg-[#0052FF] text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#0041CC] transition-all shadow-lg shadow-[#0052FF]/20">
                            Confirm Purchase
                        </button>
                    </form>

                    <p class="mt-8 text-[10px] text-[#64748B] text-center font-black uppercase tracking-[0.3em]">
                        Verified Student Transaction
                    </p>
                </div>
            </div>

            <div class="mt-16 max-w-xl text-center">
                <p class="text-xs text-[#64748B] font-bold leading-relaxed uppercase tracking-widest opacity-60">
                    The ticket will be available in your dashboard immediately after payment.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?>