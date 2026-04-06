<?php 
require_once '../config/database.php';
include '../includes/header.php'; 

// Fetch and group tickets by event name, count availability, and get the lowest price
$query = "SELECT 
            event_name, 
            event_location, 
            category, 
            MIN(selling_price) as min_price, 
            COUNT(*) as ticket_count 
          FROM tickets 
          WHERE status = 'active' AND event_date >= NOW() 
          GROUP BY event_name, event_location, category 
          ORDER BY event_date ASC";

$result = $conn->query($query);

// Organize events into arrays by category
$events = [
    'sports' => [],
    'club' => [],
    'society' => [],
    'academic' => []
];

while ($row = $result->fetch_assoc()) {
    // Map database categories to section IDs
    $cat = strtolower($row['category']);
    if (strpos($cat, 'club') !== false) $events['club'][] = $row;
    elseif (strpos($cat, 'sports') !== false) $events['sports'][] = $row;
    elseif (strpos($cat, 'society') !== false) $events['society'][] = $row;
    elseif (strpos($cat, 'academic') !== false) $events['academic'][] = $row;
}

// Helper function to render a row (prevents repeating HTML)
function renderEventRow($eventList, $icon, $rowId) {
    if (empty($eventList)) {
        echo '<p class="text-[#64748B] font-bold uppercase text-[10px] tracking-widest ml-2">No active events in this category.</p>';
        return;
    }
    foreach ($eventList as $event) {
        ?>
        <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] bg-white border border-[#E2E8F0] rounded-[2rem] p-6 snap-start group transition-all hover:border-[#0052FF]/30">
            <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                <i data-lucide="<?php echo $icon; ?>" class="w-10 h-10 text-[#CBD5E1] group-hover:text-[#0052FF]/20 group-hover:scale-110 transition-all"></i>
            </div>
            <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate tracking-tight"><?php echo htmlspecialchars($event['event_name']); ?></div>
            <div class="text-xs font-bold text-[#64748B] mb-8 truncate uppercase"><?php echo htmlspecialchars($event['event_location']); ?></div>
            <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-[#94A3B8] uppercase">From £<?php echo number_format($event['min_price'], 2); ?></span>
                    <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo $event['ticket_count']; ?> Tickets</span>
                </div>
                <a href="view_event_tickets.php?event=<?php echo urlencode($event['event_name']); ?>" class="h-11 px-5 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-colors shadow-lg">View</a>
            </div>
        </div>
        <?php
    }
}
?>

<div class="bg-[#F5F8FA] min-h-screen pb-24">
    <div class="pt-20 pb-12 px-6 lg:px-[60px]">
        <div class="max-w-4xl">
            <h1 class="text-6xl md:text-7xl font-black text-[#0A192F] tracking-tighter uppercase leading-[0.9] mb-6">
                Discover <br> Events
            </h1>
            <p class="text-lg text-[#64748B] font-bold max-w-xl">
                Find official student union events, society socials, and verified ticket resales.
            </p>
        </div>
    </div>
    
    <div class="px-6 lg:px-[60px] flex gap-3 overflow-x-auto pb-16 scrollbar-hide">
        <button onclick="filterEvents('all', this)" class="filter-btn px-8 py-4 bg-[#0052FF] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl transition-all">All Events</button>
        <button onclick="filterEvents('club', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all">Club Nights</button>
        <button onclick="filterEvents('sports', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all">Sports</button>
        <button onclick="filterEvents('society', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all">Societies</button>
        <button onclick="filterEvents('academic', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all">Academic & Careers</button>
    </div>

    <section id="section-sports" class="event-section mb-20 px-6 lg:px-[60px]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-[#0A192F] tracking-tighter uppercase">Sports & Varsity</h2>
            <div class="flex gap-2">
                <button onclick="scrollRow('row-sports', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                <button onclick="scrollRow('row-sports', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center hover:text-[#0052FF] shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
            </div>
        </div>
        <div id="row-sports" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
            <?php renderEventRow($events['sports'], 'trophy', 'row-sports'); ?>
        </div>
    </section>

    <section id="section-club" class="event-section mb-20 px-6 lg:px-[60px]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-[#0A192F] tracking-tighter uppercase">Club Nights</h2>
            <div class="flex gap-2">
                <button onclick="scrollRow('row-club', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                <button onclick="scrollRow('row-club', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
            </div>
        </div>
        <div id="row-club" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
            <?php renderEventRow($events['club'], 'music', 'row-club'); ?>
        </div>
    </section>

    <section id="section-society" class="event-section mb-20 px-6 lg:px-[60px]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-[#0A192F] tracking-tighter uppercase">Society Events</h2>
            <div class="flex gap-2">
                <button onclick="scrollRow('row-society', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                <button onclick="scrollRow('row-society', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
            </div>
        </div>
        <div id="row-society" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
            <?php renderEventRow($events['society'], 'users', 'row-society'); ?>
        </div>
    </section>

    <section id="section-academic" class="event-section mb-20 px-6 lg:px-[60px]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-[#0A192F] tracking-tighter uppercase">Academic & Careers</h2>
            <div class="flex gap-2">
                <button onclick="scrollRow('row-academic', 'left')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4"></i></button>
                <button onclick="scrollRow('row-academic', 'right')" class="w-10 h-10 rounded-full border border-[#E2E8F0] bg-white flex items-center justify-center shadow-sm"><i data-lucide="arrow-right" class="w-4 h-4"></i></button>
            </div>
        </div>
        <div id="row-academic" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 scrollbar-hide snap-x snap-mandatory">
            <?php renderEventRow($events['academic'], 'graduation-cap', 'row-academic'); ?>
        </div>
    </section>
</div>

<script>
function filterEvents(category, btn) {
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.classList.remove('bg-[#0052FF]', 'text-white', 'shadow-xl');
        button.classList.add('bg-white', 'text-[#64748B]');
    });
    btn.classList.add('bg-[#0052FF]', 'text-white', 'shadow-xl');
    btn.classList.remove('bg-white', 'text-[#64748B]');

    const sections = {
        sports: document.getElementById('section-sports'),
        club: document.getElementById('section-club'),
        society: document.getElementById('section-society'),
        academic: document.getElementById('section-academic')
    };

    Object.keys(sections).forEach(key => {
        if (category === 'all' || category === key) {
            sections[key].style.display = 'block';
        } else {
            sections[key].style.display = 'none';
        }
    });
}

function scrollRow(rowId, direction) {
    const row = document.getElementById(rowId);
    const scrollAmount = row.clientWidth * 0.8; 
    row.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
}

lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>