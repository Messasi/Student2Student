<?php 
require_once 'admin_layout.php';

// Handle User Deletion
if (isset($_GET['delete_user'])) {
    $uid = (int)$_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $uid AND is_admin != 1");
    header("Location: users.php?status=deleted");
}

renderAdminHeader("User Directory");
$all_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<div class="admin-card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
            <tr>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">User</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Points</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Joined</th>
                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-[#64748B]">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($u = $all_users->fetch_assoc()): ?>
            <tr class="border-b border-[#F1F5F9] hover:bg-[#F8FAFC] transition-all">
                <td class="p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#0A192F] text-white flex items-center justify-center text-[10px] font-black">
                            <?= substr($u['username'], 0, 1) ?>
                        </div>
                        <span class="text-sm font-black text-[#0A192F]">@<?= htmlspecialchars($u['username']) ?></span>
                    </div>
                </td>
                <td class="p-6"><span class="text-xs font-bold text-[#0052FF]"><?= $u['points'] ?> pts</span></td>
                <td class="p-6 text-xs text-[#64748B] font-bold"><?= date('M Y', strtotime($u['created_at'])) ?></td>
                <td class="p-6">
                    <a href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Permanently remove this student?')" class="text-red-500 hover:text-red-700 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php renderAdminFooter(); ?>