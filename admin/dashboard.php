<?php 
require_once 'admin_layout.php';
renderAdminHeader("Dashboard");

$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_tickets = $conn->query("SELECT COUNT(*) FROM tickets WHERE status='active'")->fetch_row()[0];
$total_sales = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];

$recent_activity = $conn->query("SELECT event_name, created_at, category FROM tickets ORDER BY created_at DESC LIMIT 5");
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
    <div class="admin-card p-8">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#64748B] mb-2">Total Users</p>
        <p class="text-5xl font-black tracking-tighter text-[#0A192F]"><?= $total_users ?></p>
    </div>
    <div class="admin-card p-8 "> <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#64748B] mb-2">Active Tickets</p>
        <p class="text-5xl font-black tracking-tighter text-[#0A192F]"><?= $total_tickets ?></p>
    </div>
    <div class="admin-card p-8">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#64748B] mb-2">Successful Sales</p>
        <p class="text-5xl font-black tracking-tighter text-[#0A192F]"><?= $total_sales ?></p>
    </div>
</div>

<div class="admin-card overflow-hidden">
    <div class="p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]">
        <h3 class="text-xs font-black uppercase tracking-widest text-[#0A192F]">Recent System Activity</h3>
    </div>
    <div class="p-0">
        <?php while($act = $recent_activity->fetch_assoc()): ?>
        <div class="p-6 border-b border-[#F1F5F9] flex items-center justify-between hover:bg-[#F8FAFC] transition-all">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-sm font-black text-[#0A192F] uppercase mb-0.5"><?= htmlspecialchars($act['event_name']) ?></p>
                    <p class="text-[10px] font-bold text-[#64748B] uppercase tracking-tight">New listing in <?= $act['category'] ?></p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-[#94A3B8]"><?= date('H:i', strtotime($act['created_at'])) ?></span>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php renderAdminFooter(); ?>