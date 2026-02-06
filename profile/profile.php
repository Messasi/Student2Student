<?php
// MOCK DATA FOR TESTING UI
require_once '../config/database.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// 1. DYNAMIC DATA: Fetch User Profile from Database
$stmt = $conn->prepare("SELECT  username, profile_picture, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$sold_count = 12;

$active_listings = [
    [
        'id' => 101,
        'event_name' => 'Summer Ball 2026',
        'event_date' => '2026-06-20',
        'event_location' => 'University Main Hall',
        'selling_price' => 45.00,
        'original_price' => 50.00,
        'ticket_type' => 'Ball',
        'category' => 'social'
    ],
    [
        'id' => 102,
        'event_name' => 'Tech Conference',
        'event_date' => '2026-03-10',
        'event_location' => 'Engineering Hub',
        'selling_price' => 15.00,
        'original_price' => 15.00,
        'ticket_type' => 'Workshop',
        'category' => 'academic'
    ],
    [
        'id' => 103,
        'event_name' => 'Final Year Boat Party',
        'event_date' => '2026-07-01',
        'event_location' => 'River Pier',
        'selling_price' => 65.00,
        'original_price' => 75.00,
        'ticket_type' => 'VIP',
        'category' => 'social'
    ]
];


?>

<div class="bg-white min-h-screen">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="bg-[#0A192F] rounded-[1rem] p-8 lg:p-12 mb-12 text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-20"></div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-10 relative z-10">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-28 h-28 bg-white text-[#0A192F] rounded-full uppercase flex items-center justify-center text-4xl font-black shadow-[0_0_40px_rgba(255,255,255,0.1)] border-4 border-white/10 overflow-hidden">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?php echo substr($user['username'], 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-3 mb-3">
                            <h1 class="text-4xl lg:text-5xl font-black tracking-tighter ">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </h1>
                        </div>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-6">
                            <div class="flex items-center  text-white/50 font-bold text-xs uppercase tracking-widest">
                                <i  class="h-4 text-[#0052FF]"></i>
                                Member Since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="bg-white/5 border border-white/10 backdrop-blur-md px-8 py-5 rounded-2xl text-center min-w-[120px]">
                        <p class="text-3xl font-black"><?php echo $sold_count; ?></p>
                        <p class="text-medium font-bold text-white/40 uppercase mt-1">Sales</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <h2 class="text-3xl font-black text-[#0A192F] uppercase tracking-tighter">Active Listings</h2>
            
            <div class="flex items-center gap-3">
                <select id="categoryFilter" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-xs font-black uppercase text-[#0A192F] focus:outline-none focus:border-[#0052FF]">
                    <option value="all">All Categories</option>
                    <option value="social">Social</option>
                    <option value="academic">Academic</option>
                </select>
                <select id="priceSort" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-xs font-black uppercase text-[#0A192F] focus:outline-none focus:border-[#0052FF]">
                    <option value="newest">Newest</option>
                    <option value="low">Price: Low</option>
                    <option value="high">Price: High</option>
                </select>
            </div>
        </div>

        <div id="listingsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($active_listings as $listing): ?>
                <div class="listing-card bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden hover:border-[#0052FF]/30 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl" 
                     data-category="<?php echo $listing['category']; ?>" 
                     data-price="<?php echo $listing['selling_price']; ?>">
                    
                    <div class="aspect-[4/3] bg-[#F8FAFC] flex items-center justify-center relative border-b border-[#F1F5F9]">
                        <i data-lucide="ticket" class="w-10 h-10 text-[#CBD5E1]"></i>
                        <span class="absolute top-3 left-3 bg-white px-2 py-1 rounded-md text-[9px] font-black uppercase text-[#0A192F] shadow-sm border border-[#E2E8F0]">
                            <?php echo htmlspecialchars($listing['category']); ?>
                        </span>
                    </div>
                    
                    <div class="p-5">
                        <h3 class="text-sm font-black text-[#0A192F] uppercase mb-4 h-10 line-clamp-2 leading-tight">
                            <?php echo htmlspecialchars($listing['event_name']); ?>
                        </h3>
                        
                        <div class="space-y-1.5 mb-5">
                            <div class="flex items-center gap-2 text-[10px] text-[#64748B] font-bold uppercase">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <?php echo date('d M Y', strtotime($listing['event_date'])); ?>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-[#F1F5F9]">
                            <div class="text-xl font-black text-[#0A192F]">£<?php echo number_format($listing['selling_price'], 2); ?></div>
                            <a href="#" class="bg-[#0052FF] text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-[#0041CC] transition-colors">
                                Buy
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    const categoryFilter = document.getElementById('categoryFilter');
    const priceSort = document.getElementById('priceSort');
    const listingsGrid = document.getElementById('listingsGrid');
    const cards = Array.from(document.getElementsByClassName('listing-card'));

    function updateGrid() {
        const cat = categoryFilter.value;
        const sort = priceSort.value;

        cards.forEach(card => {
            card.style.display = (cat === 'all' || card.dataset.category === cat) ? 'block' : 'none';
        });

        const visibleCards = cards.filter(c => c.style.display !== 'none');
        visibleCards.sort((a, b) => {
            const priceA = parseFloat(a.dataset.price);
            const priceB = parseFloat(b.dataset.price);
            if (sort === 'low') return priceA - priceB;
            if (sort === 'high') return priceB - priceA;
            return 0;
        });

        visibleCards.forEach(card => listingsGrid.appendChild(card));
    }

    categoryFilter.addEventListener('change', updateGrid);
    priceSort.addEventListener('change', updateGrid);
</script>

<?php include '../includes/footer.php'; ?>