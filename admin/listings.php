<?php 
require_once 'admin_layout.php';

// Delete Listing
if (isset($_GET['delete_id'])) {
    $tid = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM tickets WHERE id = $tid");
    header("Location: listings.php?status=removed");
}

renderAdminHeader("Ticket Management");

$all_listings = $conn->query("SELECT t.*, u.username FROM tickets t JOIN users u ON t.seller_id = u.id ORDER BY t.created_at DESC");
?>

<div class="admin-card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
            <tr>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Event</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Seller</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Price</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Status</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Manage</th>
            </tr>
        </thead>
        <tbody>
            <?php while($t = $all_listings->fetch_assoc()): ?>
            <tr class="border-b border-[#F1F5F9] hover:bg-[#F8FAFC] transition-all">
                <td class="p-6">
                    <p class="text-sm font-black text-[#0A192F] uppercase truncate max-w-[200px]"><?= htmlspecialchars($t['event_name']) ?></p>
                    <p class="text-[10px] font-bold text-[#64748B] uppercase tracking-tight"><?= htmlspecialchars($t['category']) ?></p>
                </td>
                <td class="p-6 text-xs font-bold text-[#0A192F]">@<?= htmlspecialchars($t['username']) ?></td>
                <td class="p-6 font-black text-[#0052FF]">£<?= number_format($t['selling_price'], 2) ?></td>
                <td class="p-6">
                    <span class="px-2 py-1 rounded text-[8px] font-black uppercase border 
                        <?= $t['status'] === 'active' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' ?>">
                        <?= $t['status'] ?>
                    </span>
                </td>
                <td class="p-6">
                    <a href="?delete_id=<?= $t['id'] ?>" onclick="return confirm('Delete this listing permanently?')" class="text-red-500 hover:text-red-700 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php renderAdminFooter(); ?>