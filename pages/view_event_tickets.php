<?php 
require_once '../config/database.php';
include '../includes/header.php'; 

// 1. CATCH THE EVENT NAME FROM THE URL
// This matches the link: view_event_tickets.php?event=Networking+Dinner+2026
$event_name = isset($_GET['event']) ? $_GET['event'] : '';

// 2. HANDLE SORTING LOGIC
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_low';
$orderBy = "t.selling_price ASC"; // Default

switch ($sort) {
    case 'recent':
        $orderBy = "t.created_at DESC";
        break;
    case 'rating':
        $orderBy = "u.points DESC";
        break;
}

// 3. FETCH REAL DATA FROM DATABASE
// We JOIN the users table to get the profile picture and points for the badge
$query = "SELECT t.*, u.username, u.profile_picture, u.points 
          FROM tickets t 
          JOIN users u ON t.seller_id = u.id 
          WHERE t.event_name = ? AND t.status = 'active'
          ORDER BY $orderBy";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $event_name);
$stmt->execute();
$result = $stmt->get_result();

// Get Metadata for the Hero Card (Location/Date/Category) from the first available ticket
$meta_query = "SELECT event_location, event_date, category FROM tickets WHERE event_name = ? LIMIT 1";
$m_stmt = $conn->prepare($meta_query);
$m_stmt->bind_param("s", $event_name);
$m_stmt->execute();
$meta = $m_stmt->get_result()->fetch_assoc();

// 4. HELPER FOR SELLER LEVELS
function getSellerLevel($points) {
    if ($points >= 500) return ['label' => 'Gold Seller', 'color' => '#FFD700', 'bg' => 'bg-yellow-400/10'];
    if ($points >= 100) return ['label' => 'Silver Seller', 'color' => '#94A3B8', 'bg' => 'bg-slate-400/10'];
    return ['label' => 'Bronze Seller', 'color' => '#CD7F32', 'bg' => 'bg-orange-400/10'];
}
?>

<div class="mx-auto px-6 lg:px-[60px] py-12 bg-[#F5F8FA] min-h-screen font-sans">
    
    <div class="max-w-5xl mx-auto mb-8">
        <a href="javascript:history.back()" class="flex items-center text-[#64748B] hover:text-[#0052FF] transition-colors group no-underline">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Back</span>
        </a>
    </div>

    <div class="max-w-5xl mx-auto bg-[#0A192F] rounded-[1.25rem] p-12 mb-10 shadow-sm">
        <span class="px-3 py-1 bg-[#0052FF] text-white text-[10px] font-black uppercase rounded-lg mb-5 inline-block tracking-widest">
            <?php echo htmlspecialchars($meta['category'] ?? 'General'); ?>
        </span>
        <h1 class="text-5xl lg:text-6xl font-black text-white tracking-tighter uppercase mb-5 leading-tight">
            <?php echo htmlspecialchars($event_name); ?>
        </h1>
        <div class="flex items-center gap-6 text-[#64748B] font-black uppercase text-[11px] tracking-widest">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-[#0052FF]"></i>
                <span><?php echo htmlspecialchars($meta['event_location'] ?? 'Location TBC'); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-[#0052FF]"></i>
                <span><?php echo isset($meta['event_date']) ? date('D, j M Y', strtotime($meta['event_date'])) : 'Date TBC'; ?></span>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-[1.25rem] border border-[#E2E8F0] shadow-sm overflow-hidden">
        
        <div class="p-8 border-b-2 border-[#F1F5F9]">
            <div class="flex justify-between items-center">
                <h3 class="font-black text-[#0A192F] text-lg uppercase tracking-tight">Available Tickets (<?php echo $result->num_rows; ?>)</h3>
                
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-[#94A3B8] uppercase tracking-wider">Sort by:</span>
                    <select onchange="location = this.value;" class="text-xs font-black text-[#0052FF] bg-transparent outline-none cursor-pointer uppercase tracking-widest">
                        <option value="?event=<?php echo urlencode($event_name); ?>&sort=price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Lowest Price</option>
                        <option value="?event=<?php echo urlencode($event_name); ?>&sort=rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Seller Rating</option>
                        <option value="?event=<?php echo urlencode($event_name); ?>&sort=recent" <?php echo $sort == 'recent' ? 'selected' : ''; ?>>Recently Added</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="divide-y divide-[#F1F5F9]">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $level = getSellerLevel($row['points']);
                ?>
                <div class="flex items-center justify-between p-7 hover:bg-[#FBFDFF] transition-all">
                    
                    <div class="flex items-center gap-5">
                        <div class="w-11 h-11 rounded-full bg-[#0A192F] flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if(!empty($row['profile_picture'])): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($row['profile_picture']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-white font-black text-xs uppercase"><?php echo substr($row['username'], 0, 1); ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-black text-[#0A192F] uppercase tracking-tight">@<?php echo htmlspecialchars($row['username']); ?></p>
                                <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded border <?php echo $level['bg']; ?>" style="color: <?php echo $level['color']; ?>; border-color: <?php echo $level['color']; ?>40;">
                                    <?php echo $level['label']; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-8">
                        <div class="text-right">
                            <p class="text-xl font-black text-[#0A192F] tracking-tighter">£<?php echo number_format($row['selling_price'], 2); ?></p>
                        </div>
                        <a href="checkout.php?id=<?php echo $row['id']; ?>" class="inline-flex h-11 px-8 bg-[#0052FF] text-white text-[10px] items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-all no-underline shadow-lg shadow-[#0052FF]/10">
                            Buy
                        </a>
                    </div>

                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="p-20 text-center text-[#64748B] font-bold uppercase tracking-widest opacity-50">
                    No active listings for this event.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
<?php include '../includes/footer.php'; ?>