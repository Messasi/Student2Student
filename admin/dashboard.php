<?php 
require_once 'admin_layout.php';
renderAdminHeader("Dashboard");

// Database Stats
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$active_listings = $conn->query("SELECT COUNT(*) FROM tickets WHERE status='active'")->fetch_row()[0];
$sold_tickets = $conn->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetch_row()[0];

// Today's Stats
$today_start = date('Y-m-d 00:00:00');
$sold_today = $conn->query("SELECT COUNT(*) FROM orders WHERE status='completed' AND created_at >= '$today_start'")->fetch_row()[0];
$disputed_today = $conn->query("SELECT COUNT(*) FROM orders WHERE status='disputed' AND created_at >= '$today_start'")->fetch_row()[0];

$cat_stats = $conn->query("SELECT category, COUNT(*) as count FROM tickets WHERE status='active' GROUP BY category");
$recent_activity = $conn->query("SELECT event_name, created_at, category FROM tickets ORDER BY created_at DESC LIMIT 5");
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="admin-card p-8">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#0A192F] mb-2">Total Users</p>
        <p class="text-4xl font-black tracking-tighter text-black"><?= $total_users ?></p>
    </div>
    <div class="admin-card p-8">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#0A192F] mb-2">Active Tickets</p>
        <p class="text-4xl font-black tracking-tighter text-black"><?= $active_listings ?></p>
    </div>
    <div class="admin-card p-8">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#0A192F] mb-2">Sold Tickets</p>
        <p class="text-4xl font-black tracking-tighter text-black"><?= $sold_tickets ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <div class="admin-card p-8">
        <h3 class="text-xs font-black uppercase tracking-widest text-[#0A192F] mb-8">Tickets per Category</h3>
        <div class="space-y-6">
            <?php if($cat_stats->num_rows > 0): ?>
                <?php while($cat = $cat_stats->fetch_assoc()): 
                    $percent = $active_listings > 0 ? ($cat['count'] / $active_listings) * 100 : 0; ?>
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase mb-2">
                            <span><?= htmlspecialchars($cat['category']) ?></span>
                            <span class="text-[#64748B]"><?= $cat['count'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-[#F1F5F9] rounded-full overflow-hidden">
                            <div class="h-full bg-[#0052FF]" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-xs font-bold text-[#64748B] uppercase italic">No active listing data</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-card p-8">
        <h3 class="text-xs font-black uppercase tracking-widest text-[#0A192F] mb-8">Daily Activity</h3>
        <div class="grid grid-cols-1 gap-4">
            <div class="p-6 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl flex justify-between items-center">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-[#0A192F] mb-1">Tickets Sold Today</p>
                    <p class="text-2xl font-black text-black"><?= $sold_today ?></p>
                </div>
                <div class="w-10 h-10 bg-white border border-[#E2E8F0] rounded-full flex items-center justify-center text-[#0A192F]">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="p-6 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl flex justify-between items-center">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-[#0A192F] mb-1">Disputed Tickets Today</p>
                    <p class="text-2xl font-black text-black"><?= $disputed_today ?></p>
                </div>
                <div class="w-10 h-10 bg-white border border-[#E2E8F0] rounded-full flex items-center justify-center text-[#0A192F]">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card overflow-hidden">
    <div class="p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]">
        <h3 class="text-xs font-black uppercase tracking-widest text-[#0A192F]">Recent System Activity</h3>
    </div>
    <div class="p-0">
        <?php while($act = $recent_activity->fetch_assoc()): ?>
        <div class="p-6 border-b border-[#F1F5F9] flex items-center justify-between hover:bg-[#F8FAFC] transition-all">
            <div class="text-sm font-black text-[#0A192F] uppercase truncate mr-4"><?= htmlspecialchars($act['event_name']) ?></div>
            <span class="text-[10px] font-bold text-[#94A3B8] whitespace-nowrap"><?= date('H:i', strtotime($act['created_at'])) ?></span>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php renderAdminFooter(); ?>