<?php
// Connect to the database and grab the header file
require_once '../config/database.php';
include '../includes/header.php';

// Get the user ID from the URL or use the logged-in session ID
$profile_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];

// Get the specific user details like name and points from the database
$stmt = $conn->prepare("SELECT username, profile_picture, created_at, points FROM users WHERE id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Stop the script and show an error if the user does not exist
if (!$user) {
    echo "<script>alert('User not found'); window.location.href='../index.php';</script>";
    exit;
}

// Store the user points in a variable to use for the badge logic
$points = $user['points'] ?? 0;

// Check if the user has enough points to earn the Gold badge
if ($points >= 500) {
    $badge_label = "Gold Seller";
    $badge_color = "bg-yellow-100 text-yellow-700 border-yellow-200";
// Check if the user has enough points to earn the Silver badge
} elseif ($points >= 100) {
    $badge_label = "Silver Seller";
    $badge_color = "bg-slate-100 text-slate-700 border-slate-200";
// Check if the user has enough points to earn the Bronze badge
} elseif ($points >= 10) {
    $badge_label = "Bronze Seller";
    $badge_color = "bg-orange-100 text-orange-700 border-orange-200";
// Default to the New Seller badge if they have very few points
} else {
    $badge_label = "New Seller";
    $badge_color = "bg-blue-100 text-blue-700 border-blue-200";
}

// Count how many tickets this specific user has successfully sold
$sold_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tickets WHERE seller_id = ? AND status = 'sold'");
$sold_stmt->bind_param("i", $profile_id);
$sold_stmt->execute();
$sold_count = $sold_stmt->get_result()->fetch_assoc()['total'];

// Get all currently active tickets that this user is selling right now
$listings_stmt = $conn->prepare("SELECT * FROM tickets WHERE seller_id = ? AND status = 'active' AND event_date >= NOW() ORDER BY created_at DESC");
$listings_stmt->bind_param("i", $profile_id);
$listings_stmt->execute();
$active_listings = $listings_stmt->get_result();
?>

<div class="bg-white min-h-screen text-[#0A192F]">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="bg-[#0A192F] rounded-[1rem] p-8 lg:p-12 mb-12 text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-20"></div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-10 relative z-10">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-28 h-28 bg-white text-[#0A192F] rounded-full uppercase flex items-center justify-center text-4xl font-black shadow-lg border-4 border-white/10 overflow-hidden">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_picture']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= substr($user['username'], 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3">
                            <h1 class="text-4xl lg:text-5xl font-black tracking-tighter uppercase">
                                <?= htmlspecialchars($user['username']); ?>
                            </h1>
                            <div class="flex items-center gap-2 px-3 py-1 rounded-full border <?= $badge_color; ?> w-fit mx-auto md:mx-0">
                                <span class="text-[10px] font-black uppercase tracking-widest"><?= $badge_label; ?></span>
                            </div>
                        </div>
                        <div class="text-white/50 font-bold text-xs uppercase tracking-widest">
                            Member Since <?= date('M Y', strtotime($user['created_at'])); ?>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="bg-white/5 border border-white/10 backdrop-blur-md px-8 py-5 rounded-2xl text-center min-w-[120px]">
                        <p class="text-3xl font-black"><?= $sold_count; ?></p>
                        <p class="text-xs font-bold text-white/40 uppercase mt-1">Total Sales</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-3xl font-black uppercase tracking-tighter mb-10">Active Listings</h2>

        <div id="listingsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if ($active_listings->num_rows > 0): ?>
                <?php while ($listing = $active_listings->fetch_assoc()): ?>
                    <div class="listing-card bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden hover:border-[#0052FF]/30 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                        <div class="aspect-[4/3] bg-[#F8FAFC] flex items-center justify-center relative border-b border-[#F1F5F9]">
                            <i data-lucide="ticket" class="w-10 h-10 text-[#CBD5E1]"></i>
                            <span class="absolute top-3 left-3 bg-white px-2 py-1 rounded-md text-[9px] font-black uppercase shadow-sm border border-[#E2E8F0]">
                                <?= htmlspecialchars($listing['category']); ?>
                            </span>
                        </div>
                        
                        <div class="p-5 flex flex-col h-full">
                            <h3 class="text-sm font-black uppercase mb-4 h-10 line-clamp-2 leading-tight">
                                <?= htmlspecialchars($listing['event_name']); ?>
                            </h3>
                            
                            <div class="flex items-center gap-2 text-[10px] text-[#64748B] font-bold uppercase mb-5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <?= date('d M Y', strtotime($listing['event_date'])); ?>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-[#F1F5F9] mt-auto">
                                <div class="text-xl font-black">£<?= number_format($listing['selling_price'], 2); ?></div>
                                <a href="checkout.php?id=<?= $listing['id'] ?>" class="bg-[#0052FF] text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-[#0041CC] transition-colors">
                                    Buy
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-[#64748B] font-bold uppercase text-xs tracking-widest col-span-full">This user has no active listings.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
<?php include '../includes/footer.php'; ?>