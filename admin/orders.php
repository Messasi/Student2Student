<?php 
require_once 'admin_layout.php';

renderAdminHeader("Order Management");

// Fetch orders with buyer and seller names
$orders_query = "SELECT o.*, b.username as buyer_name, s.username as seller_name 
                 FROM orders o 
                 JOIN users b ON o.buyer_id = b.id 
                 JOIN users s ON o.seller_id = s.id 
                 ORDER BY o.created_at DESC";
$orders = $conn->query($orders_query);
?>

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                <tr>
                    <th class="p-4 lg:p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Order Information</th>
                    <th class="p-4 lg:p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Participants</th>
                    <th class="p-4 lg:p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Price </th>
                    <th class="p-4 lg:p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders && $orders->num_rows > 0): ?>
                    <?php while($o = $orders->fetch_assoc()): ?>
                    <tr class="border-b border-[#F1F5F9] hover:bg-[#F8FAFC] transition-all">
                        <td class="p-4 lg:p-6">
                            <p class="text-xs lg:text-sm font-black text-[#0A192F] uppercase mb-0.5"><?= htmlspecialchars($o['event_name']) ?></p>
                            <p class="text-[9px] font-mono font-bold text-[#94A3B8]">ID: #<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></p>
                        </td>

                        <td class="p-4 lg:p-6">
                            <div class="flex flex-col gap-1">
                                <p class="text-[10px] font-bold text-[#64748B] uppercase">Seller: <span class="text-[#0A192F]">@<?= htmlspecialchars($o['seller_name']) ?></span></p>
                                <p class="text-[10px] font-bold text-[#64748B] uppercase">Buyer: <span class="text-[#0A192F]">@<?= htmlspecialchars($o['buyer_name']) ?></span></p>
                            </div>
                        </td>

                        <td class="p-4 lg:p-6">
                            <p class="text-sm font-black text-[#0A192F]">£<?= number_format($o['price'], 2) ?></p>
                            <p class="text-[9px] font-bold text-[#64748B] uppercase tracking-tight"><?= date('d M Y', strtotime($o['created_at'])) ?></p>
                        </td>

                        <td class="p-4 lg:p-6">
                            <?php 
                            $status = $o['status'] ?? 'held'; 
                            
                            if ($status === 'held') {
                                echo '<span class="px-2 py-1 rounded text-[12px] font-black uppercase  text-blue-600 border-blue-100 ">Pending in Escrow</span>';
                            } elseif ($status === 'completed') {
                                echo '<span class="px-2 py-1 rounded text-[12px] font-black uppercase  text-green-600 border-green-100">Sold</span>';
                            } elseif ($status === 'disputed') {
                                echo '<span class="px-2 py-1 rounded text-[12px] font-black uppercase  text-red-600 border-red-100">Disputed</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-12 text-center text-xs font-bold text-[#64748B] uppercase italic">No transaction history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php renderAdminFooter(); ?>