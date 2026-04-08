<?php
// Connect to the database and grab the header file
require_once '../config/database.php';
include '../includes/header.php';

// Get the user ID from the URL link or use the logged-in session ID
// 1. Check if an ID is provided in the URL (e.g., profile.php?id=11)
if (isset($_GET['id'])) {
    $profile_id = (int)$_GET['id'];
} 
// 2. If no ID in URL, check if the user is logged in to show THEIR OWN profile
elseif (isset($_SESSION['user_id'])) {
    $profile_id = $_SESSION['user_id'];
} 
// 3. If neither, send them back to index
else {
    header("Location: ../index.php");
    exit();
}

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

$points = $user['points'] ?? 0;

if ($points >= 500) {
    $badge_label = "Gold Seller";
    $badge_color = "bg-yellow-100 text-yellow-700 border-yellow-200";
} elseif ($points >= 100) {
    $badge_label = "Silver Seller";
    $badge_color = "bg-slate-100 text-slate-700 border-slate-200";
} elseif ($points >= 10) {
    $badge_label = "Bronze Seller";
    $badge_color = "bg-orange-100 text-orange-700 border-orange-200";
} else {
    $badge_label = "New Seller";
    $badge_color = "bg-blue-100 text-blue-700 border-blue-200";
}

$sold_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tickets WHERE seller_id = ? AND status = 'sold'");
$sold_stmt->bind_param("i", $profile_id);
$sold_stmt->execute();
$sold_count = $sold_stmt->get_result()->fetch_assoc()['total'];

$listings_stmt = $conn->prepare("SELECT t.*, u.username, u.points, u.profile_picture FROM tickets t JOIN users u ON t.seller_id = u.id WHERE t.seller_id = ? AND t.status = 'active' AND t.event_date >= NOW() ORDER BY t.created_at DESC");
$listings_stmt->bind_param("i", $profile_id);
$listings_stmt->execute();
$active_listings = $listings_stmt->get_result();
?>

<div class="bg-white min-h-screen text-[#0A192F]">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="bg-[#0A192F] rounded-[1.5rem] p-8 lg:p-12 mb-16 text-white shadow-2xl relative overflow-hidden group/header">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-20"></div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-10 relative z-10">
                <a href="profile.php?id=<?= $profile_id ?>" class="flex flex-col md:flex-row items-center gap-8 group/user transition-transform hover:scale-[1.01]">
                    <div class="w-28 h-28 bg-white text-[#0A192F] rounded-full uppercase flex items-center justify-center text-4xl font-black shadow-lg border-4 border-white/10 overflow-hidden group-hover/user:border-[#0052FF] transition-all">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_picture']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= substr($user['username'], 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3">
                            <h1 class="text-4xl lg:text-5xl font-black tracking-tighter uppercase group-hover/user:text-[#0052FF] transition-colors">
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
                </a>

                <div class="flex gap-4">
                    <div class="bg-white/5 border border-white/10 backdrop-blur-md px-8 py-5 rounded-2xl text-center min-w-[120px]">
                        <p class="text-3xl font-black"><?= $sold_count; ?></p>
                        <p class="text-xs font-bold text-white/40 uppercase mt-1">Total Sales</p>
                    </div>
                </div>
            </div>
        </div>

        <section>
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl font-black uppercase tracking-tighter">Active Listings</h2>
                <div class="flex gap-2">
                    <button onclick="scrollRow('profile-track', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm transition-all"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                    <button onclick="scrollRow('profile-track', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm transition-all"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
                </div>
            </div>

            <div id="profile-track" class="flex gap-6 overflow-x-auto scroll-smooth pb-8 scrollbar-hide snap-x snap-mandatory">
                <?php if ($active_listings->num_rows > 0): ?>
                    <?php while ($listing = $active_listings->fetch_assoc()): ?>
                        <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] snap-start">
                            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 h-full flex flex-col hover:border-[#0052FF]/30 transition-all shadow-sm hover:shadow-xl">
                                <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 relative border border-[#F1F5F9] overflow-hidden">
                                    <?php if (!empty($listing['event_image'])): ?>
                                        <img src="<?= htmlspecialchars($listing['event_image']) ?>" class="absolute inset-0 w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="flex items-center justify-center w-full h-full">
                                            <i data-lucide="ticket" class="w-8 h-8 text-[#CBD5E1]"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="absolute top-3 left-3 bg-white px-2 py-1 rounded-md text-[9px] font-black uppercase shadow-sm border border-[#E2E8F0]">
                                        <?= htmlspecialchars($listing['category']); ?>
                                    </span>
                                </div>
                                <div class="text-sm font-bold text-[#0A192F] uppercase mb-1 truncate"><?= htmlspecialchars($listing['event_name']) ?></div>
                                <div class="text-xs font-medium text-[#64748B] mb-6"><?= htmlspecialchars($listing['event_location']) ?></div>
                                <div class="mt-auto flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                                    <span class="text-lg font-bold text-[#0A192F]">£<?= number_format($listing['selling_price'], 2) ?></span>
                                    <a href="../pages/checkout.php?id=<?= $listing['id'] ?>" class="h-8 px-4 bg-[#0052FF] text-white text-xs flex items-center rounded-lg font-bold uppercase hover:bg-[#0041CC]">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-[#64748B] font-bold uppercase text-xs tracking-widest p-4">This user has no active listings.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>
function scrollRow(rowId, direction) {
    const row = document.getElementById(rowId);
    const scrollAmount = row.clientWidth * 0.8; 
    row.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();
});
</script>

<?php include '../includes/footer.php'; ?>