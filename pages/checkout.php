<?php
// Connect to the database and grab the header
require_once '../config/database.php';
include '../includes/header.php';

// Get the ticket ID from the URL
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- CLICK TRACKER LOGIC ---
if (isset($_SESSION['user_id']) && $ticket_id > 0) {
    $user_id = (int)$_SESSION['user_id'];

    $cat_query = $conn->prepare("SELECT category FROM tickets WHERE id = ?");
    $cat_query->bind_param("i", $ticket_id);
    $cat_query->execute();
    $cat_res = $cat_query->get_result()->fetch_assoc();

    if ($cat_res) {
        $category = strtolower($cat_res['category']);
        $column = "";

        if (strpos($category, 'club') !== false) {
            $column = "pref_club_clicks";
        } elseif (strpos($category, 'sport') !== false) {
            $column = "pref_sports_clicks";
        } elseif (strpos($category, 'society') !== false) {
            $column = "pref_society_clicks";
        } elseif (strpos($category, 'gig') !== false || strpos($category, 'academic') !== false) {
            $column = "pref_gig_clicks";
        }

        if (!empty($column)) {
            $update = $conn->prepare("UPDATE users SET $column = $column + 1 WHERE id = ?");
            $update->bind_param("i", $user_id);
            $update->execute();
        }
    }
}

// Fetch ticket and seller details including points
$query = "SELECT t.*, u.username, u.profile_picture, u.points 
          FROM tickets t 
          JOIN users u ON t.seller_id = u.id 
          WHERE t.id = ? AND t.status = 'active' 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo "<script>alert('Ticket not found or no longer available.'); window.location.href='index.php';</script>";
    exit;
}

// Determine Points Bracket
$pts = $ticket['points'] ?? 0;
if ($pts >= 500) {
    $bracket_text = "Gold Seller";
    $bracket_css = "text-yellow-600";
} elseif ($pts >= 100) {
    $bracket_text = "Silver Seller";
    $bracket_css = "text-slate-500";
} elseif ($pts >= 10) {
    $bracket_text = "Bronze Seller";
    $bracket_css = "text-orange-600";
} else {
    $bracket_text = "New Seller";
    $bracket_css = "text-blue-500";
}

$price = (float)$ticket['selling_price'];
$fee = 1.50;
$total = number_format($price + $fee, 2);
$seller_user = $ticket['username'];
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
                       <div class="w-10 h-10 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-xs font-bold uppercase overflow-hidden border border-[#E2E8F0]">
                            <?php if (!empty($ticket['profile_picture'])): ?>
                                <img src="../uploads/profiles/<?= htmlspecialchars($ticket['profile_picture']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= substr($seller_user, 0, 1); ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-[#0A192F]">@<?php echo htmlspecialchars($seller_user); ?></span>
                            <span class="text-[10px] font-black uppercase tracking-widest <?= $bracket_css ?>">
                                <?= $bracket_text ?>
                            </span>
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-6 flex items-center justify-center border border-[#F1F5F9] overflow-hidden">
                        <?php if (!empty($ticket['event_image'])): ?>
                            <img src="<?= htmlspecialchars($ticket['event_image']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i data-lucide="ticket" class="w-12 h-12 text-[#CBD5E1]"></i>
                        <?php endif; ?>
                    </div>

                    <div class="text-lg font-bold text-[#0A192F] mb-1 uppercase truncate tracking-tight">
                        <?php echo htmlspecialchars($ticket['event_name']); ?>
                    </div>
                    <div class="text-sm font-medium text-[#64748B] mb-8 uppercase tracking-tight">
                        <?php echo htmlspecialchars($ticket['event_location']); ?>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-[#F1F5F9]">
                        <span class="text-2xl font-black text-[#0A192F]">£<?php echo number_format($price, 2); ?></span>
                    </div>
                </div>

                <div class="w-full max-w-[420px] bg-white border border-[#E2E8F0] rounded-[2rem] p-10 shadow-sm flex flex-col">
                    <h2 class="text-2xl font-bold text-[#0A192F] mb-8 uppercase tracking-tight">Order Summary</h2>
                    
                    <div class="space-y-5 mb-10">
                        <div class="flex justify-between text-base font-bold text-[#64748B] uppercase tracking-tight">
                            <span>Ticket Listing</span>
                            <span class="text-[#0A192F]">£<?php echo number_format($price, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-[#64748B] uppercase tracking-tight">
                            <span>Processing Fee</span>
                            <span class="text-[#0A192F]">£<?php echo number_format($fee, 2); ?></span>
                        </div>
                        <div class="border-t border-[#F1F5F9] pt-6 flex justify-between items-center">
                            <span class="text-lg font-black text-[#0A192F] uppercase tracking-tight">Total</span>
                            <span class="text-4xl font-black text-[#0052FF] tracking-tighter">£<?php echo $total; ?></span>
                        </div>
                    </div>

                    <form action="success.php" method="POST" class="mt-auto">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <button type="submit" class="w-full bg-[#0052FF] text-white py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#0A192F] transition-all shadow-lg shadow-[#0052FF]/20">
                            Confirm Purchase
                        </button>
                    </form>

                    <p class="mt-8 text-[11px] text-[#64748B] text-center font-black uppercase tracking-[0.2em]">
                        Secure Escrow Checkout
                    </p>
                </div>
            </div>

            <div class="mt-16 max-w-xl text-center">
                <p class="text-sm text-[#64748B] font-bold leading-relaxed opacity-80 uppercase tracking-tight">
                    Funds are held in escrow. Release only when you have successfully used your ticket.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>

<?php include '../includes/footer.php'; ?>