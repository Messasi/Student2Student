<?php
// Connect to the database and grab the header
require_once '../config/database.php';
include '../includes/header.php';

// Get the ticket ID from the URL (e.g., checkout.php?id=12)
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


// Fetch the specific ticket and the seller's details from the database
$query = "SELECT t.*, u.username, u.profile_picture 
          FROM tickets t 
          JOIN users u ON t.seller_id = u.id 
          WHERE t.id = ? AND t.status = 'active' 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

// Redirect back if the ticket doesn't exist or is already sold
if (!$ticket) {
    echo "<script>alert('Ticket not found or no longer available.'); window.location.href='index.php';</script>";
    exit;
}

// Calculate prices based on the database 'selling_price'
$price = (float)$ticket['selling_price'];
$fee = 1.50;
$total = number_format($price + $fee, 2);
$seller_user = $ticket['username'];
?>

<div class="bg-white min-h-screen font-sans">
    <div class="mx-auto px-6 py-16 max-w-6xl">
        
        <div class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-[#0A192F] tracking-tight">
                Review Purchase
            </h1>
            <div class="h-1.5 w-24 bg-[#0052FF] mt-6 mx-auto rounded-full"></div>
        </div>

        <div class="flex flex-col items-center">
            <div class="w-full flex flex-col md:flex-row gap-10 items-stretch justify-center">
                
                <div class="w-full max-w-[370px] bg-white border border-[#E2E8F0] rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <div class="flex items-center gap-4 mb-6">
                       <div class="w-10 h-10 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-xs font-bold uppercase overflow-hidden border border-[#E2E8F0]">
                            <?php if (!empty($ticket['profile_picture'])): ?>
                                <img src="../uploads/profiles/<?= htmlspecialchars($ticket['profile_picture']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= substr($seller_user, 0, 1); ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-[#0A192F]">@<?php echo htmlspecialchars($seller_user); ?></span>
                            <span class="text-[10px] font-bold text-[#0052FF] uppercase tracking-tighter">Verified Price</span>
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-6 flex items-center justify-center border border-[#F1F5F9]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#CBD5E1]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>

                    <div class="text-lg font-bold text-[#0A192F] mb-1 uppercase truncate">
                        <?php echo htmlspecialchars($ticket['event_name']); ?>
                    </div>
                    <div class="text-sm font-medium text-[#64748B] mb-8 uppercase tracking-tight">
                        <?php echo htmlspecialchars($ticket['event_location']); ?>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-[#F1F5F9]">
                        <span class="text-2xl font-bold text-[#0A192F]">£<?php echo number_format($price, 2); ?></span>
                    </div>
                </div>

                <div class="w-full max-w-[420px] bg-white border border-[#E2E8F0] rounded-[2rem] p-10 shadow-sm flex flex-col">
                    <h2 class="text-2xl font-bold text-[#0A192F] mb-8">Order Summary</h2>
                    
                    <div class="space-y-5 mb-10">
                        <div class="flex justify-between text-base font-medium text-[#64748B]">
                            <span>Ticket Listing</span>
                            <span class="text-[#0A192F]">£<?php echo number_format($price, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-base font-medium text-[#64748B]">
                            <span>Processing Fee</span>
                            <span class="text-[#0A192F]">£<?php echo number_format($fee, 2); ?></span>
                        </div>
                        <div class="border-t border-[#F1F5F9] pt-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-[#0A192F]">Total</span>
                            <span class="text-4xl font-extrabold text-[#0052FF]">£<?php echo $total; ?></span>
                        </div>
                    </div>

                    <form action="success.php" method="POST" class="mt-auto">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <button type="submit" class="w-full bg-[#0052FF] text-white py-5 rounded-2xl font-bold text-sm uppercase tracking-widest hover:bg-[#0041CC] transition-all shadow-lg shadow-[#0052FF]/20">
                            Confirm Purchase
                        </button>
                    </form>

                    <p class="mt-8 text-[11px] text-[#64748B] text-center font-bold uppercase tracking-[0.2em]">
                        Secure Checkout
                    </p>
                </div>
            </div>

            <div class="mt-16 max-w-xl text-center">
                <p class="text-sm text-[#64748B] font-bold leading-relaxed opacity-80 uppercase tracking-tight">
                    The ticket will be available in your dashboard immediately after payment.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>