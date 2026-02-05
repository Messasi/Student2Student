<?php
// MOCK DATA FOR TESTING UI
require_once '../config/database.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// 1. DYNAMIC DATA: Fetch User Profile from Database
$stmt = $conn->prepare("SELECT first_name, last_name, created_at FROM users WHERE id = ?");
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
        
        <div class="bg-gradient-to-br from-[#0A192F] to-[#1E3A5F] rounded-3xl p-8 lg:p-12 mb-10 text-white shadow-xl">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 bg-white/10 rounded-full border-4 border-white/20 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-16 h-16 text-white/80"></i>
                </div>
                
                <div class="text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 mb-2">
                        <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight">
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </h1>
                        <div class="inline-flex items-center gap-1.5 bg-green-500/20 border border-green-500/30 px-2 py-1 rounded-full self-center md:self-auto">
                            <i  class="h-4 text-green-400"></i>
                            <span class="text-green-400 text-sm font-black  tracking-wider"><?php echo $sold_count; ?> Sold</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-center md:justify-start gap-2 text-white/60 font-medium">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h2 class="text-3xl font-extrabold text-[#0A192F] tracking-tight flex items-center gap-2">
                <i  class=" h-6 text-[#0052FF]"></i>
                Active Listings
            </h2>
            
            <div class="flex items-center gap-3">
                <select id="categoryFilter" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-bold text-[#0A192F] focus:outline-none focus:border-[#0052FF]">
                    <option value="all">All Categories</option>
                    <option value="social">Social Events</option>
                    <option value="academic">Academic</option>
                </select>

                <select id="priceSort" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-bold text-[#0A192F] focus:outline-none focus:border-[#0052FF]">
                    <option value="newest">Newest First</option>
                    <option value="low">Price: Low to High</option>
                    <option value="high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <div id="listingsGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($active_listings as $listing): ?>
                <div class="listing-card bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden hover:border-[#0052FF]/30 hover:shadow-lg transition-all group" 
                     data-category="<?php echo $listing['category']; ?>" 
                     data-price="<?php echo $listing['selling_price']; ?>">
                    
                    <div class="aspect-video bg-gradient-to-br from-[#F8FAFC] to-[#E2E8F0] flex items-center justify-center border-b border-[#E2E8F0] relative">
                        <i data-lucide="ticket" class="w-12 h-12 text-[#CBD5E1]"></i>
                        <span class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-black uppercase text-[#0A192F] tracking-tighter shadow-sm">
                            <?php echo htmlspecialchars($listing['category']); ?>
                        </span>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-[#0A192F] mb-2 group-hover:text-[#0052FF] transition-colors">
                            <?php echo htmlspecialchars($listing['event_name']); ?>
                        </h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-sm text-[#64748B] font-medium">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <?php echo date('d M Y', strtotime($listing['event_date'])); ?>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-[#64748B] font-medium">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <?php echo htmlspecialchars($listing['event_location']); ?>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-[#E2E8F0]">
                            <div>
                                <div class="text-2xl font-extrabold text-[#0A192F]">£<?php echo number_format($listing['selling_price'], 2); ?></div>
                                <?php if ($listing['original_price'] > $listing['selling_price']): ?>
                                    <div class="text-xs text-red-500 font-bold uppercase tracking-tighter">
                                        Save £<?php echo number_format($listing['original_price'] - $listing['selling_price'], 2); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="#" class="bg-[#0052FF] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#0041CC] transition-all no-underline shadow-lg shadow-blue-500/10">
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