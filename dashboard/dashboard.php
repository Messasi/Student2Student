<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. HANDLE ESCROW ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $oid = (int)$_POST['order_id'];
    if ($_POST['action'] === 'confirm') {
        $conn->begin_transaction();
        try {
            $conn->query("UPDATE orders SET status = 'completed' WHERE id = $oid AND buyer_id = $user_id AND status = 'held'");
            $s_res = $conn->query("SELECT seller_id FROM orders WHERE id = $oid");
            if ($s_res->num_rows > 0) {
                $sid = $s_res->fetch_assoc()['seller_id'];
                $conn->query("UPDATE users SET points = points + 20 WHERE id = $sid");
            }
            $conn->commit();
            header("Location: dashboard.php?msg=confirmed");
        } catch (Exception $e) { $conn->rollback(); }
    } 
    elseif ($_POST['action'] === 'dispute') {
        $conn->begin_transaction();
        try {
            $s_res = $conn->query("SELECT seller_id FROM orders WHERE id = $oid");
            if ($s_res->num_rows > 0) {
                $sid = $s_res->fetch_assoc()['seller_id'];
                $conn->query("UPDATE orders SET status = 'disputed' WHERE id = $oid AND buyer_id = $user_id");
                $conn->query("UPDATE users SET points = points - 50 WHERE id = $sid");
            }
            $conn->commit();
            header("Location: dashboard.php?msg=disputed");
        } catch (Exception $e) { $conn->rollback(); }
    }
    exit();
}

// --- 2. FINANCIAL CALCULATIONS ---
$total_earnings = $conn->query("SELECT SUM(price) as total FROM orders WHERE seller_id = $user_id AND status = 'completed'")->fetch_assoc()['total'] ?? 0.00;
$listings_val = $conn->query("SELECT SUM(selling_price) as total FROM tickets WHERE seller_id = $user_id AND status = 'active'")->fetch_assoc()['total'] ?? 0.00;
$held_funds = $conn->query("SELECT SUM(price) as total FROM orders WHERE seller_id = $user_id AND status = 'held'")->fetch_assoc()['total'] ?? 0.00;
$pending_total = $listings_val + $held_funds;

// --- 3. DATA FETCHING ---
$sold_tickets = $conn->query("SELECT o.*, u.username as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id WHERE o.seller_id = $user_id ORDER BY o.created_at DESC");
$active_listings = $conn->query("SELECT * FROM tickets WHERE seller_id = $user_id AND status = 'active' ORDER BY created_at DESC");
$purchase_history = $conn->query("SELECT o.*, u.username as seller_name FROM orders o JOIN users u ON o.seller_id = u.id WHERE o.buyer_id = $user_id ORDER BY o.created_at DESC");

include '../includes/header.php';
?>

<div class="bg-white min-h-screen text-[#0A192F] font-sans">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold mb-6 tracking-tight uppercase leading-none">Financial Hub</h1>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Total Earnings</p>
                    <p class="text-5xl font-extrabold mt-4">£<?= number_format($total_earnings, 2); ?></p>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Withdrawable Amount</p>
                    <p class="text-5xl font-extrabold mt-4">£<?= number_format($total_earnings, 2); ?></p>
                    <p class="mt-4 font-bold">Pending: £<?= number_format($pending_total, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6 uppercase tracking-tight">Tickets Sold</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Buyer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while($sale = $sold_tickets->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold uppercase text-sm"><?= htmlspecialchars($sale['event_name']); ?></td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-600">@<?= htmlspecialchars($sale['buyer_name']); ?></td>
                            <td class="px-6 py-4 text-blue-600 font-bold">£<?= number_format($sale['price'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6 uppercase tracking-tight">Active Listings</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Price</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Remove</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while($listing = $active_listings->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold uppercase text-sm"><?= htmlspecialchars($listing['event_name']); ?></td>
                            <td class="px-6 py-4 text-xs font-bold">£<?= number_format($listing['selling_price'], 2); ?></td>
                            <td class="px-6 py-4">
                                <button onclick="removeListing(<?= $listing['id']; ?>)" class="text-red-500 hover:text-red-700 transition-colors"><i data-lucide="trash" class="w-5 h-5"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6 uppercase tracking-tight">Purchase History</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Price</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if($purchase_history->num_rows > 0): ?>
                            <?php while($p = $purchase_history->fetch_assoc()): $status = $p['status']; ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold uppercase text-sm"><?= htmlspecialchars($p['event_name']); ?></p>
                                    <p class="text-[9px] font-black uppercase tracking-tighter opacity-50">Seller: @<?= htmlspecialchars($p['seller_name']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-[#0A192F]">£<?= number_format($p['price'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($status === 'held'): ?>
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="order_id" value="<?= $p['id']; ?>">
                                            <button type="submit" name="action" value="confirm" class="bg-[#10B981] text-white text-[9px] font-black uppercase px-3 py-1.5 rounded-lg hover:bg-green-600 transition-all">Confirm</button>
                                            <button type="submit" name="action" value="dispute" onclick="return confirm('Proceed?')" class="bg-red-50 text-red-600 text-[9px] font-black uppercase px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition-all">Dispute</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-[9px] font-black uppercase opacity-40 italic"><?= $status ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 font-medium">No purchases found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
lucide.createIcons();
function removeListing(id) { if(confirm('Remove this listing?')) window.location.href = 'remove_listing.php?id=' + id; }
</script>
<?php include '../includes/footer.php'; ?>