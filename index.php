<?php
// index.php
require_once 'config/database.php';
include 'includes/header.php';

// Store the current time to filter out past events
$today = date('Y-m-d H:i:s');
// Get the current user ID from the session if they are logged in
$user_id = $_SESSION['user_id'] ?? null;

// Create an empty array to hold our final recommended tickets
$recommended_tickets = [];

// Check if the user is logged in before finding recommendations
if ($user_id) {
    // Get the user click data and points from the database
    $user_stmt = $conn->prepare("SELECT points, pref_club_clicks, pref_sports_clicks, pref_society_clicks, pref_gig_clicks FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $u_prof = $user_stmt->get_result()->fetch_assoc();

    // Calculate total clicks to see if the user has a browsing history
    $total_clicks = (int)$u_prof['pref_club_clicks'] + (int)$u_prof['pref_sports_clicks'] + (int)$u_prof['pref_society_clicks'] + (int)$u_prof['pref_gig_clicks'];

    // Show a mix of categories if the user has never clicked a ticket
    if ($total_clicks == 0) {
        $cats = ['Club Night', 'Society', 'Sports', 'Academic and Careers', 'Other'];
        foreach ($cats as $c) {
            // Updated query to include profile_picture
            $stmt = $conn->prepare("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 3");
            $stmt->bind_param("ss", $c, $today);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) { $recommended_tickets[] = $row; }
        }
    } else {
        // Find tickets based on seller ranks for users with click history
        $slots = ["gold" => "u.points >= 500", "silver" => "u.points BETWEEN 100 AND 499", "bronze" => "u.points BETWEEN 10 AND 99", "new" => "u.points < 10"];
        foreach ($slots as $type => $condition) {
            $order = ($type == 'new') ? "t.created_at DESC" : "u.points DESC";
            // Updated query to include profile_picture
            $res = $conn->query("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE $condition AND t.status = 'active' AND t.event_date >= '$today' ORDER BY $order LIMIT 3");
            while ($row = $res->fetch_assoc()) { $recommended_tickets[] = $row; }
        }
    }
}

// Function to fetch up to 20 tickets for each specific category row
function getCategoryTickets($conn, $cat_name) {
    $now = date('Y-m-d H:i:s');
    // Updated query to include profile_picture
    $stmt = $conn->prepare("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 20");
    $stmt->bind_param("ss", $cat_name, $now);
    $stmt->execute();
    return $stmt->get_result();
}

// Logic to choose the correct colour and text for the seller tier
function getTierLabel($pts) {
    if ($pts >= 500) return ['text' => 'Gold', 'css' => 'text-yellow-600'];
    if ($pts >= 100) return ['text' => 'Silver', 'css' => 'text-slate-500'];
    if ($pts >= 10) return ['text' => 'Bronze', 'css' => 'text-orange-600'];
    return ['text' => 'New', 'css' => 'text-blue-500'];
}
?>

<div class="bg-white min-h-screen font-inter pb-20">
    <div class="mx-auto px-6 lg:px-[60px] py-12">

        <section class="relative bg-[#0A192F] rounded-[2rem] px-8 lg:px-20 py-24 mb-16 overflow-hidden border-2 border-white/10 shadow-xl">
            <div class="relative z-10 flex flex-col items-start text-left">
                <div class="inline-block px-4 py-1 border-2 border-[#0052FF] text-white text-xs font-black uppercase tracking-widest mb-10">Student to student only</div>
                <h1 class="text-6xl lg:text-[110px] font-black text-white leading-[0.8] mb-12 tracking-tighter uppercase">Zero Fraud. <br> Zero Worries.</h1>
                <div class="max-w-xl mb-12">
                    <div class="h-1 bg-[#0052FF] w-24 mr-auto mb-6"></div>
                    <p class="text-xl text-white font-bold leading-tight opacity-90">The hub for verified 2nd hand tickets. Buy and sell with confidence.</p>
                </div>
                <div class="flex flex-wrap justify-start gap-4">
                    <a href="/student2student/listings/ticket_listing.php" class="bg-transparent text-white px-8 py-4 rounded-xl font-bold border-2 border-white hover:bg-white hover:text-[#0A192F] transition-all">Sell Your Ticket</a>
                </div>
            </div>
        </section>

        <div id="listings-anchor" class="w-full mb-12">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-[#0A192F] tracking-tight uppercase">Available Tickets</h1>
            <div class="h-1.5 w-20 bg-[#0052FF] mt-4 rounded-full"></div>
        </div>

        <div class="space-y-24">
            <?php if (!empty($recommended_tickets)): ?>
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-[#0A192F] tracking-tight uppercase">Recommended For You</h2>
                    <div class="flex gap-2">
                        <button onclick="scrollRow('row-recommended', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                        <button onclick="scrollRow('row-recommended', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
                    </div>
                </div>
                <div id="row-recommended" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
                    <?php foreach ($recommended_tickets as $ticket): 
                        $tier = getTierLabel($ticket['points'] ?? 0); ?>
                        <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] snap-start">
                            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 h-full flex flex-col hover:border-[#0052FF]/30 transition-all shadow-sm">
                                <a href="pages/profile.php?id=<?= $ticket['seller_id'] ?>" class="flex items-center gap-3 mb-4 group/user">
                                    <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase overflow-hidden border border-transparent group-hover/user:border-[#0052FF] transition-all">
                                        <?php if (!empty($ticket['profile_picture'])): ?>
                                            <img src="uploads/profiles/<?= htmlspecialchars($ticket['profile_picture']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= substr($ticket['username'] ?? 'S', 0, 1) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-[#0A192F] group-hover/user:text-[#0052FF] transition-colors">@<?= $ticket['username'] ?></span>
                                        <span class="text-[9px] font-bold <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                    </div>
                                </a>
                                <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 flex items-center justify-center border border-[#F1F5F9]"><i data-lucide="ticket" class="w-8 h-8 text-[#CBD5E1]"></i></div>
                                <div class="text-sm font-bold text-[#0A192F] uppercase mb-1 truncate"><?= $ticket['event_name'] ?></div>
                                <div class="text-xs font-medium text-[#64748B] mb-6"><?= $ticket['event_location'] ?></div>
                                <div class="mt-auto flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                                    <span class="text-lg font-bold">£<?= number_format($ticket['selling_price'], 2) ?></span>
                                    <a href="pages/checkout.php?id=<?= $ticket['id'] ?>" class="h-8 px-4 bg-[#0052FF] text-white text-xs flex items-center rounded-lg font-bold uppercase hover:bg-[#0041CC]">Buy</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php
            $cat_list = ['Academic and Careers', 'Society', 'Sports', 'Club Night', 'Other'];
            foreach ($cat_list as $c_title):
                $tickets = getCategoryTickets($conn, $c_title);
                if ($tickets && $tickets->num_rows > 0): 
                    $row_id = 'row-' . strtolower(str_replace(' ', '-', $c_title));
            ?>
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-[#0A192F] tracking-tight uppercase"><?= $c_title ?></h2>
                    <div class="flex gap-2">
                        <button onclick="scrollRow('<?= $row_id ?>', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                        <button onclick="scrollRow('<?= $row_id ?>', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
                    </div>
                </div>
                <div id="<?= $row_id ?>" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
                    <?php while ($t = $tickets->fetch_assoc()): 
                        $tier = getTierLabel($t['points'] ?? 0); ?>
                        <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] snap-start">
                            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 h-full flex flex-col hover:border-[#0052FF]/30 transition-all shadow-sm">
                                <a href="profile/profile.php?id=<?= $t['seller_id'] ?>" class="flex items-center gap-3 mb-4 group/user">
                                    <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase overflow-hidden border border-transparent group-hover/user:border-[#0052FF] transition-all">
                                        <?php if (!empty($t['profile_picture'])): ?>
                                            <img src="uploads/profiles/<?= htmlspecialchars($t['profile_picture']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= substr($t['username'] ?? 'S', 0, 1) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-[#0A192F] group-hover/user:text-[#0052FF] transition-colors">@<?= $t['username'] ?></span>
                                        <span class="text-[9px] font-bold <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                    </div>
                                </a>
                                <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 flex items-center justify-center border border-[#F1F5F9]"><i data-lucide="ticket" class="w-8 h-8 text-[#CBD5E1]"></i></div>
                                <div class="text-sm font-bold text-[#0A192F] uppercase mb-1 truncate"><?= $t['event_name'] ?></div>
                                <div class="text-xs font-medium text-[#64748B] mb-6"><?= $t['event_location'] ?></div>
                                <div class="mt-auto flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                                    <span class="text-lg font-bold">£<?= number_format($t['selling_price'], 2) ?></span>
                                    <a href="pages/checkout.php?id=<?= $t['id'] ?>" class="h-8 px-4 bg-[#0052FF] text-white text-xs flex items-center rounded-lg font-bold uppercase hover:bg-[#0041CC]">Buy</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php endif; endforeach; ?>
        </div>

        <section class="bg-[#0A192F] rounded-[2rem] px-8 lg:px-16 py-16 text-center mt-20 relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-5xl font-extrabold text-white mb-4 tracking-tight uppercase leading-none">Ready to start?</h1>
                <p class="text-xl text-white/60 mb-8 font-medium max-w-xl mx-auto">Create an account using your university email to start buying and selling tickets.</p>
                <a href="/student2student/auth/register.php" class="inline-block bg-[#0052FF] text-white px-10 py-4 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all shadow-lg">Create Free Account</a>
            </div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-[#0052FF]/5 rounded-full blur-[80px]"></div>
        </section>
    </div>
</div>

<script>
function scrollRow(rowId, direction) {
    const row = document.getElementById(rowId);
    const scrollAmount = row.clientWidth * 0.8; 
    row.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();
});
</script>

<?php include 'includes/footer.php'; ?>