<?php
// 1. MUST start the session first to check login status


require_once '../config/database.php';
include '../includes/header.php';#


// 2. Check if user is logged in BEFORE any HTML is sent
if (!isset($_SESSION['user_id'])) {
    header('Location: /student2student/auth/login.php');
    exit;
}

// Include header (it's okay if this also has session_start)


// Get user details - Updated to match your DB (first_name, last_name instead of full_name)
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT email, first_name, last_name, is_verified FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// --- Financial Logic (Remains mostly the same, ensure tables 'transactions' exist) ---
$stmt_earnings = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) as total_earnings FROM transactions WHERE seller_id = ? AND transaction_status = 'completed'");
mysqli_stmt_bind_param($stmt_earnings, "i", $user_id);
mysqli_stmt_execute($stmt_earnings);
$earnings_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_earnings));
$total_earnings = $earnings_data['total_earnings'];
mysqli_stmt_close($stmt_earnings);

$stmt_pending = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) as pending_balance FROM transactions WHERE seller_id = ? AND transaction_status = 'pending'");
mysqli_stmt_bind_param($stmt_pending, "i", $user_id);
mysqli_stmt_execute($stmt_pending);
$pending_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pending));
$pending_balance = $pending_data['pending_balance'];
mysqli_stmt_close($stmt_pending);

$available_balance = $total_earnings - $pending_balance;

// --- Active Listings Logic ---
$stmt_listings = mysqli_prepare($conn, "SELECT id, event_name, event_date, selling_price, quantity, status, created_at FROM tickets WHERE seller_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt_listings, "i", $user_id);
mysqli_stmt_execute($stmt_listings);
$listings_result = mysqli_stmt_get_result($stmt_listings);
$active_listings = [];
while ($row = mysqli_fetch_assoc($listings_result)) {
    $active_listings[] = $row;
}
mysqli_stmt_close($stmt_listings);

// --- Purchase History Logic ---
$stmt_purchases = mysqli_prepare($conn, "
    SELECT 
        t.id as transaction_id,
        tk.event_name,
        tk.event_date,
        tk.event_location,
        t.amount,
        t.transaction_status,
        t.created_at,
        u.first_name as seller_name,
        tk.seller_id
    FROM transactions t
    JOIN tickets tk ON t.ticket_id = tk.id
    JOIN users u ON t.seller_id = u.id
    WHERE t.buyer_id = ?
    ORDER BY t.created_at DESC
");
mysqli_stmt_bind_param($stmt_purchases, "i", $user_id);
mysqli_stmt_execute($stmt_purchases);
$purchases_result = mysqli_stmt_get_result($stmt_purchases);
$purchase_history = [];
while ($row = mysqli_fetch_assoc($purchases_result)) {
    $event_date = strtotime($row['event_date']);
    $current_time = time();
    $hours_after_event = ($current_time - $event_date) / 3600;
    $row['can_report_fraud'] = ($hours_after_event > 0 && $hours_after_event <= 24);
    $purchase_history[] = $row;
}
mysqli_stmt_close($stmt_purchases);
?>

<div class="bg-white min-h-screen">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <!-- Page Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-[#0A192F] mb-6 tracking-tight flex items-center gap-2">
                <i  class=" h-8 text-[#0052FF]"></i>
                Financial Hub
            </h1>
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 p-12 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[#64748B] font-semibold text-sm uppercase tracking-wider">Total Earnings</span>
                    </div>
                    <div class="text-5xl font-extrabold text-[#0A192F] mb-2">£<?php echo number_format($total_earnings, 2); ?></div>
                    
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-100 p-12 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[#64748B] font-semibold text-sm uppercase tracking-wider">Withdrawable Amount</span>
                    </div>
                    <div class="text-5xl font-extrabold text-[#0A192F] mb-2">£<?php echo number_format($available_balance, 2); ?></div>

                    <div class = "pt-3">
                        <span class="text-sm font-bold pt-14 text-[#0A192F]" >Pending Balance</span>
                    </div>
                     <div class="text-xl font-bold  text-[#0A192F]">
                    £<?php echo number_format($pending_balance, 2); ?>
                </div>

                </div>
            </div>
        </div>

        <!--ticekts solded-->
        <div class="mb-12">
            <h2 class="text-2xl font-extrabold text-[#0A192F] mb-6 tracking-tight flex items-center gap-2">
                
                Tickets Sold
            </h2>

            <?php if (empty($sold_tickets)): ?>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 bg-[#E2E8F0] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="ticket" class="w-8 h-8 text-[#64748B]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0A192F] mb-2">No tickets sold yet</h3>
                    <p class="text-[#64748B] font-medium mb-6">Your sales will appear here once someone buys your tickets.</p>
                    <a href="/student2student/listings/create.php" class="inline-flex items-center gap-2 bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all no-underline">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        List a Ticket for Sale
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-2xl border border-[#E2E8F0] overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-[#64748B] uppercase">Event</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#64748B] uppercase">Buyer</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#64748B] uppercase">Sale Date</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#64748B] uppercase">Revenue</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#64748B] uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                <?php foreach ($sold_tickets as $sale): ?>
                                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-[#0A192F]"><?php echo htmlspecialchars($sale['event_name']); ?></div>
                                            <div class="text-xs text-[#64748B]"><?php echo date('d M Y', strtotime($sale['event_date'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-[#0A192F]"><?php echo htmlspecialchars($sale['buyer_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-[#64748B]"><?php echo date('d/m/Y', strtotime($sale['created_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-[#0052FF]">£<?php echo number_format($sale['amount'], 2); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $status_colors = [
                                                'pending' => 'bg-orange-100 text-orange-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700'
                                            ];
                                            $color = $status_colors[$sale['transaction_status']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?>">
                                                <?php echo ucfirst($sale['transaction_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <!-- Active Listings (Seller View) -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-extrabold text-[#0A192F] tracking-tight flex items-center gap-2">
                    <i  class=" h-6 text-[#0052FF]"></i>
                    My Active Listings
                </h2>
               
            </div>

            <?php if (empty($active_listings)): ?>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 bg-[#E2E8F0] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-[#64748B]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0A192F] mb-2">No active listings</h3>
                    <p class="text-[#64748B] font-medium mb-6">You haven't listed any tickets yet.</p>
                    <a href="/student2student/listings/create.php" class="inline-flex items-center gap-2 bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all no-underline">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        Create Your First Listing
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-2xl border border-[#E2E8F0] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Event</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                <?php foreach ($active_listings as $listing): ?>
                                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-[#0A192F]"><?php echo htmlspecialchars($listing['event_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-[#64748B]">
                                                <?php echo date('d M Y', strtotime($listing['event_date'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-[#0A192F]">£<?php echo number_format($listing['selling_price'], 2); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-[#64748B]"><?php echo $listing['quantity']; ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $status_colors = [
                                                'available' => 'bg-green-100 text-green-700',
                                                'pending' => 'bg-orange-100 text-orange-700',
                                                'sold' => 'bg-blue-100 text-blue-700'
                                            ];
                                            $color = $status_colors[$listing['status']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?>">
                                                <?php echo ucfirst($listing['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($listing['status'] === 'available'): ?>
                                                <button 
                                                    onclick="if(confirm('Are you sure you want to remove this listing?')) { window.location.href='/student2student/listings/delete.php?id=<?php echo $listing['id']; ?>'; }"
                                                    class="text-red-600 hover:text-red-800 font-bold text-sm flex items-center gap-1"
                                                >
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    Remove
                                                </button>
                                            <?php else: ?>
                                                <span class="text-[#94A3B8] text-sm font-semibold flex items-center gap-1">
                                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                                    Protected
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Purchase History (Buyer View) -->
        <div class="mb-12">
            <h2 class="text-2xl font-extrabold text-[#0A192F] mb-6 tracking-tight flex items-center gap-2">
                <i class="h-6 text-[#0052FF]"></i>
                Purchase History
            </h2>

            <?php if (empty($purchase_history)): ?>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 bg-[#E2E8F0] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shopping-cart" class="w-8 h-8 text-[#64748B]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0A192F] mb-2">No purchases yet</h3>
                    <p class="text-[#64748B] font-medium mb-6">You haven't bought any tickets yet.</p>
                    <a href="/student2student/listings/view.php" class="inline-flex items-center gap-2 bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all no-underline">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        Browse Available Tickets
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($purchase_history as $purchase): ?>
                        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 hover:border-[#0052FF]/30 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-[#0A192F] mb-1"><?php echo htmlspecialchars($purchase['event_name']); ?></h3>
                                    <div class="flex flex-wrap gap-4 text-sm text-[#64748B] font-medium">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            <?php echo htmlspecialchars($purchase['event_location']); ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            <?php echo date('d M Y', strtotime($purchase['event_date'])); ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                            Sold by <?php echo htmlspecialchars($purchase['seller_name']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-2xl font-extrabold text-[#0A192F]">£<?php echo number_format($purchase['amount'], 2); ?></div>
                                        <?php
                                        $status_colors = [
                                            'pending' => 'bg-orange-100 text-orange-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700'
                                        ];
                                        $color = $status_colors[$purchase['transaction_status']] ?? 'bg-gray-100 text-gray-700';
                                        ?>
                                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?>">
                                            <?php echo ucfirst($purchase['transaction_status']); ?>
                                        </span>
                                    </div>
                                    <?php if ($purchase['can_report_fraud']): ?>
                                        <button 
                                            onclick="if(confirm('Are you sure you want to report fraud for this transaction? This action cannot be undone.')) { window.location.href='/student2student/transactions/report-fraud.php?id=<?php echo $purchase['transaction_id']; ?>'; }"
                                            class="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-100 transition-all flex items-center gap-2"
                                        >
                                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                            Report Fraud
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>