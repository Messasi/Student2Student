<?php 
require_once 'admin_layout.php';
renderAdminHeader("Order History");

$orders = $conn->query("SELECT o.*, b.username as buyer, s.username as seller 
                        FROM orders o 
                        JOIN users b ON o.buyer_id = b.id 
                        JOIN users s ON o.seller_id = s.id 
                        ORDER BY o.created_at DESC");
?>

<div class="admin-card overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
            <tr>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Order #</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Event</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Buyer</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while($o = $orders->fetch_assoc()): ?>
            <tr class="border-b border-[#F1F5F9]">
                <td class="p-6 font-mono text-[10px] font-black">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td class="p-6 text-xs font-black uppercase text-[#0A192F]"><?= htmlspecialchars($o['event_name']) ?></td>
                <td class="p-6 text-xs font-bold text-[#64748B]">@<?= htmlspecialchars($o['buyer']) ?></td>
                <td class="p-6 font-black text-[#10B981]">£<?= number_format($o['price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php renderAdminFooter(); ?>