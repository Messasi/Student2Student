<?php
// connect to database file
require_once 'config/database.php';
// add header file
include 'includes/header.php';

// current date and time variable
$today = date('Y-m-d H:i:s');
// retrieve session user id
$user_id = $_SESSION['user_id'] ?? null;
// empty array for suggestions
$recommended_tickets = [];
// standard category list
$default_cats = ['Club Night', 'Society', 'Sports', 'Academic and Careers', 'Other'];

if ($user_id) {
    // prepare sql to find user data
    $user_stmt = $conn->prepare("SELECT points, pref_club_clicks, pref_sports_clicks, pref_society_clicks, pref_gig_clicks FROM users WHERE id = ?");
    // bind user id to query
    $user_stmt->bind_param("i", $user_id);
    // run the query
    $user_stmt->execute();
    // fetch results into array
    $u_prof = $user_stmt->get_result()->fetch_assoc();

    // map database columns to categories
    $user_prefs = [
        'Club Night' => (int)$u_prof['pref_club_clicks'],
        'Sports' => (int)$u_prof['pref_sports_clicks'],
        'Society' => (int)$u_prof['pref_society_clicks'],
        'Academic and Careers' => (int)$u_prof['pref_gig_clicks']
    ];

    // calculate total interactions
    $total_clicks = array_sum($user_prefs);

    if ($total_clicks == 0) {
        // loop through categories for new users
        foreach (['Club Night', 'Society', 'Sports'] as $c) {
            // prepare query for newest tickets
            $stmt = $conn->prepare("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 2");
            // bind parameters for new users
            $stmt->bind_param("ss", $c, $today);
            // run query
            $stmt->execute();
            // get result set
            $res = $stmt->get_result();
            // add rows to recommendations
            while ($row = $res->fetch_assoc()) { $recommended_tickets[] = $row; }
        }
    } else {
        arsort($user_prefs);
        $top_categories = array_slice(array_keys($user_prefs), 0, 2); 

        $all_tickets = [];

         //Step 1: get more tickets per category
            foreach ($top_categories as $fav_cat) {
                $stmt = $conn->prepare("
                    SELECT t.*, u.username, u.points, u.profile_picture 
                    FROM tickets t 
                    JOIN users u ON t.seller_id = u.id 
                    WHERE t.category = ? 
                    AND t.status = 'active' 
                    AND t.event_date >= ?
                    ORDER BY t.created_at DESC
                    LIMIT 10
                ");
                $stmt->bind_param("ss", $fav_cat, $today);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    $all_tickets[] = $row;
                }

            }

    // Step 2: score each ticket
    foreach ($all_tickets as &$t) {
        $category_weight = $user_prefs[$t['category']] ?? 0;

        $seller_score = $t['points'] * 0.3;

        $recency_score = strtotime($t['created_at']) / 100000;

        $t['score'] = ($category_weight * 5) + $seller_score + $recency_score;
    }
    unset($t);

    // Step 3: sort by score
    usort($all_tickets, fn($a, $b) => $b['score'] <=> $a['score']);

    // Step 4: take top 8
    $recommended_tickets = array_slice($all_tickets, 0, 8);
    } 
}

// function to get tickets by category
function getCategoryTickets($conn, $cat_name) {
    // timestamp for current time
    $now = date('Y-m-d H:i:s');
    // prepare query for category listings
    $stmt = $conn->prepare("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.category = ? AND t.status = 'active' AND t.event_date >= ? ORDER BY t.created_at DESC LIMIT 20");
    // bind category and time
    $stmt->bind_param("ss", $cat_name, $now);
    // run query
    $stmt->execute();
    // return findings
    return $stmt->get_result();
}

// function for seller ranking
function getTierLabel($pts) {
    // check for gold rank
    if ($pts >= 500) return ['text' => 'Gold', 'css' => 'text-yellow-600'];
    // check for silver rank
    if ($pts >= 100) return ['text' => 'Silver', 'css' => 'text-slate-500'];
    // check for bronze rank
    if ($pts >= 10) return ['text' => 'Bronze', 'css' => 'text-orange-600'];
    // default rank
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
                    <a href="/student2student/listings/ticket_listing.php" class="bg-transparent text-white px-8 py-4 rounded-xl font-bold border-2 border-white hover:bg-white hover:text-[#0A192F] transition-all no-underline">Sell Your Ticket</a>
                </div>
            </div>
        </section>

        <?php if (!empty($recommended_tickets)): ?>
        <section class="mb-24">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black text-[#0A192F] tracking-tight uppercase">Recommended For You</h2>
                </div>
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
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase overflow-hidden">
                                    <?php if (!empty($ticket['profile_picture'])): ?>
                                        <img src="uploads/profiles/<?= htmlspecialchars($ticket['profile_picture']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= substr($ticket['username'] ?? 'S', 0, 1) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-[#0A192F]">@<?= $ticket['username'] ?></span>
                                    <span class="text-[9px] font-black <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                </div>
                            </div>

                            <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 overflow-hidden border border-[#F1F5F9]">
                                <?php if (!empty($ticket['event_image'])): ?>
                                    <img src="<?= htmlspecialchars($ticket['event_image']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-[#CBD5E1]"><i data-lucide="ticket"></i></div>
                                <?php endif; ?>
                            </div>

                            <div class="text-sm font-black text-[#0A192F] uppercase mb-1 truncate"><?= $ticket['event_name'] ?></div>
                            <div class="text-[10px] font-bold text-[#64748B] mb-6 uppercase tracking-tight"><?= $ticket['event_location'] ?></div>
                            
                            <div class="mt-auto flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                                <span class="text-lg font-black text-[#0A192F]">£<?= number_format($ticket['selling_price'], 2) ?></span>
                                <a href="pages/checkout.php?id=<?= $ticket['id'] ?>" class="h-8 px-4 bg-[#0052FF] text-white text-[10px] flex items-center rounded-lg font-black uppercase hover:bg-[#0A192F] transition-all no-underline">Buy</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="space-y-24">
            <?php foreach ($default_cats as $c_title):
                $tickets = getCategoryTickets($conn, $c_title);
                // check for category availability
                if ($tickets && $tickets->num_rows > 0): 
                    $row_id = 'row-' . strtolower(str_replace(' ', '-', $c_title));
            ?>
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-[#0A192F] tracking-tight uppercase"><?= $c_title ?></h2>
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
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase overflow-hidden">
                                        <?php if (!empty($t['profile_picture'])): ?>
                                            <img src="uploads/profiles/<?= htmlspecialchars($t['profile_picture']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= substr($t['username'] ?? 'S', 0, 1) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-[#0A192F]">@<?= $t['username'] ?></span>
                                        <span class="text-[9px] font-black <?= $tier['css'] ?> uppercase tracking-tighter"><?= $tier['text'] ?> Seller</span>
                                    </div>
                                </div>
                                <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 overflow-hidden border border-[#F1F5F9]">
                                    <?php if (!empty($t['event_image'])): ?>
                                        <img src="<?= htmlspecialchars($t['event_image']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-[#CBD5E1]"><i data-lucide="ticket"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-sm font-black text-[#0A192F] uppercase mb-1 truncate"><?= $t['event_name'] ?></div>
                                <div class="text-[10px] font-bold text-[#64748B] mb-6 uppercase tracking-tight"><?= $t['event_location'] ?></div>
                                <div class="mt-auto flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                                    <span class="text-lg font-black text-[#0A192F]">£<?= number_format($t['selling_price'], 2) ?></span>
                                    <a href="pages/checkout.php?id=<?= $t['id'] ?>" class="h-8 px-4 bg-[#0052FF] text-white text-[10px] flex items-center rounded-lg font-black uppercase hover:bg-[#0A192F] transition-all no-underline">Buy</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>

<script>
// function that slides row content
function scrollRow(rowId, direction) {
    // get row element by id
    const row = document.getElementById(rowId);
    // calculate horizontal scroll distance
    const scrollAmount = row.clientWidth * 0.8; 
    // animate horizontal scroll
    row.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
}
// lucide icon init function
document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

<?php include 'includes/footer.php'; ?>