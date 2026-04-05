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
            $stmt = $conn->prepare("SELECT t.*, u.username, u.points FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 3");
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
            $res = $conn->query("SELECT t.*, u.username, u.points FROM tickets t JOIN users u ON t.seller_id = u.id WHERE $condition AND t.status = 'active' AND t.event_date >= '$today' ORDER BY $order LIMIT 3");
            while ($row = $res->fetch_assoc()) { $recommended_tickets[] = $row; }
        }
    }
}

// Function to fetch up to 20 tickets for each specific category row
function getCategoryTickets($conn, $cat_name) {
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT t.*, u.username, u.points FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 20");
    $stmt->bind_param("ss", $cat_name, $now);
    $stmt->execute();
    return $stmt->get_result();
}

// Logic to choose the correct color and text for the seller tier
function getTierLabel($pts) {
    if ($pts >= 500) return ['text' => 'Gold', 'css' => 'text-yellow-600'];
    if ($pts >= 100) return ['text' => 'Silver', 'css' => 'text-slate-500'];
    if ($pts >= 10) return ['text' => 'Bronze', 'css' => 'text-orange-600'];
    return ['text' => 'New', 'css' => 'text-blue-500'];
}
?>

<style>
/* Container for the slider row and the hidden scrollbar */
.slider-container { position: relative; display: flex; align-items: center; width: 100%; }
.slider-track { display: flex; gap: 24px; width: 100%; overflow-x: auto; scroll-behavior: smooth; padding-bottom: 10px; scrollbar-width: none; -ms-overflow-style: none; }
.slider-track::-webkit-scrollbar { display: none; }
.js-card { flex: 0 0 auto; }

/* Styling for the circular navigation arrow buttons */
.nav-btn { position: absolute; z-index: 10; width: 44px; height: 44px; background: white; border: 1px solid #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); cursor: pointer; transition: all 0.2s; }
.nav-btn:hover:not(:disabled) { background: #0052FF; color: white; transform: scale(1.08); border-color: #0052FF; }
.nav-btn:disabled { opacity: 0; pointer-events: none; }
.btn-left { left: -22px; }
.btn-right { right: -22px; }

/* Hide arrows on mobile devices to save space */
@media (max-width: 1024px) { .nav-btn { display: none; } }
</style>

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
            <section class="js-slider-section">
                <h2 class="text-2xl font-bold text-[#0A192F] mb-6 tracking-tight uppercase">Recommended For You</h2>
                <div class="slider-container">
                    <button class="nav-btn btn-left js-prev" disabled><i data-lucide="chevron-left"></i></button>
                    <div class="slider-track js-track">
                        <?php foreach ($recommended_tickets as $ticket): 
                            $tier = getTierLabel($ticket['points'] ?? 0); ?>
                            <div class="js-card">
                                <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 h-full flex flex-col hover:border-[#0052FF]/30 transition-all shadow-sm">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase"><?= substr($ticket['username'] ?? 'S', 0, 1) ?></div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-[#0A192F]">@<?= $ticket['username'] ?></span>
                                            <span class="text-[9px] font-bold <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                        </div>
                                    </div>
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
                    <button class="nav-btn btn-right js-next"><i data-lucide="chevron-right"></i></button>
                </div>
            </section>
            <?php endif; ?>

            <?php
            $cat_list = ['Academic and Careers', 'Society', 'Sports', 'Club Night', 'Other'];
            foreach ($cat_list as $c_title):
                $tickets = getCategoryTickets($conn, $c_title);
                if ($tickets && $tickets->num_rows > 0): ?>
                <section class="js-slider-section">
                    <h2 class="text-2xl font-bold text-[#0A192F] mb-6 tracking-tight uppercase"><?= $c_title ?></h2>
                    <div class="slider-container">
                        <button class="nav-btn btn-left js-prev" disabled><i data-lucide="chevron-left"></i></button>
                        <div class="slider-track js-track">
                            <?php while ($t = $tickets->fetch_assoc()): 
                                $tier = getTierLabel($t['points'] ?? 0); ?>
                                <div class="js-card">
                                    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 h-full flex flex-col hover:border-[#0052FF]/30 transition-all shadow-sm">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase"><?= substr($t['username'] ?? 'S', 0, 1) ?></div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-[#0A192F]">@<?= $t['username'] ?></span>
                                                <span class="text-[9px] font-bold <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                            </div>
                                        </div>
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
                        <button class="nav-btn btn-right js-next"><i data-lucide="chevron-right"></i></button>
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
document.addEventListener('DOMContentLoaded', function () {
    // Initialize the icons from the lucide library
    lucide.createIcons();

    const sections = document.querySelectorAll('.js-slider-section');
    const gap = 24;

    sections.forEach(section => {
        const track = section.querySelector('.js-track');
        const cards = section.querySelectorAll('.js-card');
        const nextBtn = section.querySelector('.js-next');
        const prevBtn = section.querySelector('.js-prev');

        // Check the screen width to decide how many cards to show
        function getVisibleCount() {
            if (window.innerWidth >= 1024) return 4;
            if (window.innerWidth >= 768) return 2;
            return 1;
        }

        // Recalculate card widths so 4 fit perfectly in the row
        function updateLayout() {
            const containerWidth = track.clientWidth;
            const totalCards = cards.length;
            const visibleCount = getVisibleCount();
            let cardWidth;

            if (totalCards <= visibleCount) {
                const totalGap = gap * (totalCards - 1);
                cardWidth = (containerWidth - totalGap) / totalCards;
            } else {
                const totalGap = gap * (visibleCount - 1);
                cardWidth = (containerWidth - totalGap) / visibleCount;
            }

            cards.forEach(card => card.style.width = cardWidth + 'px');
            updateButtons();
        }

        // Hide or show the navigation arrows based on scroll position
        function updateButtons() {
            const maxScroll = track.scrollWidth - track.clientWidth;
            const scrollLeft = track.scrollLeft;

            prevBtn.disabled = scrollLeft <= 5;
            nextBtn.disabled = scrollLeft >= maxScroll - 5;
        }

        // Handle the smooth sliding when an arrow is clicked
        nextBtn.addEventListener('click', () => track.scrollBy({ left: track.clientWidth, behavior: 'smooth' }));
        prevBtn.addEventListener('click', () => track.scrollBy({ left: -track.clientWidth, behavior: 'smooth' }));

        track.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateLayout);
        updateLayout();
    });
});
</script>

<?php include 'includes/footer.php'; ?>