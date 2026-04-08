<?php
session_start();
require_once '../config/database.php';

// Safety Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. FINANCIAL CALCULATIONS ---

// Total Earnings: Revenue from tickets you have actually sold (from orders table)
$sales_query = "SELECT SUM(price) as total FROM orders WHERE seller_id = ?";
$stmt = $conn->prepare($sales_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_earnings = $stmt->get_result()->fetch_assoc()['total'] ?? 0.00;

// Pending: The combined value of tickets you currently have listed (status 'active')
$pending_query = "SELECT SUM(selling_price) as total FROM tickets WHERE seller_id = ? AND status = 'active'";
$stmt = $conn->prepare($pending_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending = $stmt->get_result()->fetch_assoc()['total'] ?? 0.00;

// Withdrawable Amount
$withdrawable = $total_earnings;

// --- 2. DATA FOR TABLES ---

// Tickets Sold: Sales you've made (Joined with users to get Buyer Name)
$sold_query = "SELECT o.*, u.username as buyer_name 
               FROM orders o 
               JOIN users u ON o.buyer_id = u.id 
               WHERE o.seller_id = ? 
               ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sold_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sold_tickets = $stmt->get_result();

// Active Listings: Tickets you've posted that are still for sale
$listings_query = "SELECT * FROM tickets WHERE seller_id = ? AND status = 'active' ORDER BY created_at DESC";
$stmt = $conn->prepare($listings_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_listings = $stmt->get_result();

// Purchase History: Tickets you bought (Joined with users to get Seller Name)
$purchases_query = "SELECT o.*, u.username as seller_name 
                    FROM orders o 
                    JOIN users u ON o.seller_id = u.id 
                    WHERE o.buyer_id = ? 
                    ORDER BY o.created_at DESC";
$stmt = $conn->prepare($purchases_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchase_history = $stmt->get_result();

include '../includes/header.php';
?>

<div class="bg-white min-h-screen text-[#0A192F] font-sans">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold mb-6 tracking-tight">Financial Hub</h1>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Total Earnings</p>
                    <p class="text-5xl font-extrabold mt-4">£<?php echo number_format($total_earnings, 2); ?></p>
                </div>

                <div class="bg-green-50 border border-green-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Withdrawable Amount</p>
                    <p class="text-5xl font-extrabold mt-4">£<?php echo number_format($withdrawable, 2); ?></p>
                    <p class="mt-4 font-bold">Pending: £<?php echo number_format($pending, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">Tickets Sold</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Buyer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if($sold_tickets->num_rows > 0): ?>
                            <?php while($sale = $sold_tickets->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($sale['event_name']); ?></td>
                                <td class="px-6 py-4">@<?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                                <td class="px-6 py-4 text-blue-600 font-bold">£<?php echo number_format($sale['price'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 font-medium">No tickets sold yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">My Active Listings</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Remove</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if($active_listings->num_rows > 0): ?>
                            <?php while($listing = $active_listings->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($listing['event_name']); ?></td>
                                <td class="px-6 py-4">£<?php echo number_format($listing['selling_price'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <button onclick="removeListing(<?php echo $listing['id']; ?>)" class="px-3 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                        <i data-lucide="trash" class="w-5 h-5"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 font-medium">No active listings.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">Purchase History</h2>
            <div class="space-y-4">
                <?php if($purchase_history->num_rows > 0): ?>
                    <?php while($purchase = $purchase_history->fetch_assoc()): ?>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold mb-1 uppercase tracking-tight"><?php echo htmlspecialchars($purchase['event_name']); ?></h3>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-tighter">
                                    Sold by @<?php echo htmlspecialchars($purchase['seller_name']); ?> | <?php echo date('d M Y', strtotime($purchase['created_at'])); ?>
                                </p>
                            </div>
                           <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xl font-extrabold">£<?php echo number_format($purchase['price'], 2); ?></p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button onclick="reportTicket(<?php echo $purchase['id']; ?>)" 
                                            title="Report Issue" 
                                            class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                        <i data-lucide="flag" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-2xl p-12 text-center border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">You have not purchased any tickets yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
lucide.createIcons();

function removeListing(id) {
    if(confirm('Are you sure you want to remove this listing?')) {
        window.location.href = 'remove_listing.php?id=' + id;
    }
}

function reportTicket(id) {
    if(confirm('Report an issue with this ticket?')) {
        window.location.href = 'report_issue.php?id=' + id;
    }
}
</script>

<?php include '../includes/footer.php'; ?>