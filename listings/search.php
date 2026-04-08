<?php
require_once '../config/database.php';
include '../includes/header.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_term = "%$query%";

// 1. SEARCH TICKETS / EVENTS (Using the tickets table)
// We group by event_name to show unique event cards
$ticket_stmt = $conn->prepare("SELECT event_name, event_location, category, MIN(selling_price) as min_price, COUNT(*) as ticket_count 
                                FROM tickets 
                                WHERE (event_name LIKE ? OR event_location LIKE ? OR category LIKE ?) 
                                AND status = 'active' 
                                GROUP BY event_name");
$ticket_stmt->bind_param("sss", $search_term, $search_term, $search_term);
$ticket_stmt->execute();
$ticket_results = $ticket_stmt->get_result();

// 2. SEARCH PROFILES (Using the users table)
$user_stmt = $conn->prepare("SELECT id, username, profile_picture, points FROM users WHERE username LIKE ? LIMIT 10");
$user_stmt->bind_param("s", $search_term);
$user_stmt->execute();
$user_results = $user_stmt->get_result();

// Helper for Seller Levels
function getSellerLevel($points) {
    if ($points >= 500) return ['label' => 'Gold', 'color' => '#FFD700'];
    if ($points >= 100) return ['label' => 'Silver', 'color' => '#94A3B8'];
    return ['label' => 'Bronze', 'color' => '#CD7F32'];
}
?>

<div class="mx-auto px-6 lg:px-[60px] py-12 bg-[#F5F8FA] min-h-screen">
    
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-black text-[#0A192F] uppercase tracking-tighter mb-2">
            Search Results for: <span class="text-[#0052FF]">"<?php echo htmlspecialchars($query); ?>"</span>
        </h1>
        <p class="text-sm text-[#64748B] font-bold uppercase tracking-widest mb-12">
            Found results across events and profiles
        </p>

        <section class="mb-16">
            <h2 class="text-xs font-black text-[#94A3B8] uppercase tracking-[0.3em] mb-6 border-b border-[#E2E8F0] pb-4">Events & Tickets</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($ticket_results->num_rows > 0): ?>
                    <?php while ($event = $ticket_results->fetch_assoc()): ?>
                        <div class="bg-white border border-[#E2E8F0] rounded-[2rem] p-6 transition-all hover:border-[#0052FF]/30 group">
                            <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate"><?php echo htmlspecialchars($event['event_name']); ?></div>
                            <div class="text-xs font-bold text-[#64748B] mb-8 uppercase"><?php echo htmlspecialchars($event['event_location']); ?></div>
                            <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-[#94A3B8] uppercase tracking-widest">From £<?php echo number_format($event['min_price'], 2); ?></span>
                                    <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo $event['ticket_count']; ?> Tickets</span>
                                </div>
                                <a href="../pages/view_event_tickets.php?event=<?php echo urlencode($event['event_name']); ?>" class="h-10 px-6 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-all no-underline">View</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-full text-sm font-bold text-[#64748B] italic">No events or tickets found matching your search.</p>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2 class="text-xs font-black text-[#94A3B8] uppercase tracking-[0.3em] mb-6 border-b border-[#E2E8F0] pb-4">User Profiles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php if ($user_results->num_rows > 0): ?>
                    <?php while ($user = $user_results->fetch_assoc()): 
                        $level = getSellerLevel($user['points']);
                    ?>
                        <div class="bg-white border border-[#E2E8F0] rounded-[1.5rem] p-5 flex items-center gap-4 transition-all hover:shadow-md">
                            <div class="w-12 h-12 rounded-full bg-[#0A192F] flex items-center justify-center overflow-hidden flex-shrink-0">
                                <?php if ($user['profile_picture']): ?>
                                    <img src="../uploads/profiles/<?php echo $user['profile_picture']; ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-white font-black text-xs uppercase"><?php echo substr($user['username'], 0, 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-black text-[#0A192F] truncate mb-0.5">@<?php echo htmlspecialchars($user['username']); ?></p>
                                <span class="text-[8px] font-black uppercase tracking-widest" style="color: <?php echo $level['color']; ?>">
                                    <?php echo $level['label']; ?> Seller
                                </span>
                            </div>
                            <a href="../profile/profile.php?id=<?php echo $user['id']; ?>" class="ml-auto p-2 text-[#CBD5E1] hover:text-[#0052FF]">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-full text-sm font-bold text-[#64748B] italic">No user profiles found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>lucide.createIcons();</script>
<?php include '../includes/footer.php'; ?>