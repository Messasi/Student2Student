<?php 
require_once '../config/database.php';
include '../includes/header.php'; 
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
        <button onclick="filterEvents('all', this)" class="filter-btn px-8 py-4 bg-[#0052FF] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-[#0052FF]/20 transition-all">
            All Events
        </button>
        <button onclick="filterEvents('club', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all ">
            Club Nights
        </button>
        <button onclick="filterEvents('sports', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all">
            Sports
        </button>
        <button onclick="filterEvents('society', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all ">
            Societies
        </button>
        <button onclick="filterEvents('academic', this)" class="filter-btn px-8 py-4 bg-white border border-[#E2E8F0] text-[#64748B] rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-[#0052FF] transition-all ">
            Academic & Careers
        </button>
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
            <?php for($i=1; $i<=5; $i++): ?>
            <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] bg-white border border-[#E2E8F0] rounded-[2rem] p-6 snap-start group transition-all hover:border-[#0052FF]/30">
                <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                    <i data-lucide="trophy" class="w-10 h-10 text-[#CBD5E1] group-hover:text-[#0052FF]/20 group-hover:scale-110 transition-all"></i>
                </div>
                <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate tracking-tight">Varsity Match <?php echo $i; ?></div>
                <div class="text-xs font-bold text-[#64748B] mb-8 truncate">Main Stadium Arena</div>
                <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-[#94A3B8] uppercase">Available</span>
                        <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo rand(2, 12); ?> Tickets</span>
                    </div>
                    <a href="view_event_tickets.php" class="h-11 px-5 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-colors shadow-lg shadow-[#0052FF]/10">View</a>
                </div>
            </div>
            <?php endfor; ?>
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
            <?php for($i=1; $i<=5; $i++): ?>
            <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] bg-white border border-[#E2E8F0] rounded-[2rem] p-6 snap-start group transition-all hover:border-[#0052FF]/30">
                <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                    <i data-lucide="music" class="w-10 h-10 text-[#CBD5E1] group-hover:text-[#0052FF]/20 group-hover:scale-110 transition-all"></i>
                </div>
                <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate tracking-tight">Student Rave <?php echo $i; ?></div>
                <div class="text-xs font-bold text-[#64748B] mb-8 truncate">The Union Nightclub</div>
                <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-[#94A3B8] uppercase">Available</span>
                        <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo rand(1, 15); ?> Tickets</span>
                    </div>
                    <a href="view_event_tickets.php" class="h-11 px-5 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-colors shadow-lg shadow-[#0052FF]/10">View</a>
                </div>
            </div>
            <?php endfor; ?>
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
            <?php for($i=1; $i<=5; $i++): ?>
            <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] bg-white border border-[#E2E8F0] rounded-[2rem] p-6 snap-start group transition-all hover:border-[#0052FF]/30">
                <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                    <i data-lucide="users" class="w-10 h-10 text-[#CBD5E1] group-hover:text-[#0052FF]/20 group-hover:scale-110 transition-all"></i>
                </div>
                <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate tracking-tight">Society Social <?php echo $i; ?></div>
                <div class="text-xs font-bold text-[#64748B] mb-8 truncate">Student Common Room</div>
                <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-[#94A3B8] uppercase">Available</span>
                        <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo rand(5, 20); ?> Tickets</span>
                    </div>
                    <a href="view_event_tickets.php" class="h-11 px-5 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-colors shadow-lg shadow-[#0052FF]/10">View</a>
                </div>
            </div>
            <?php endfor; ?>
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
            <?php for($i=1; $i<=5; $i++): ?>
            <div class="min-w-[85%] md:min-w-[45%] lg:min-w-[calc(25%-18px)] bg-white border border-[#E2E8F0] rounded-[2rem] p-6 snap-start group transition-all hover:border-[#0052FF]/30">
                <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                    <i data-lucide="graduation-cap" class="w-10 h-10 text-[#CBD5E1] group-hover:text-[#0052FF]/20 group-hover:scale-110 transition-all"></i>
                </div>
                <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate tracking-tight">Career Workshop <?php echo $i; ?></div>
                <div class="text-xs font-bold text-[#64748B] mb-8 truncate">Lecture Hall B</div>
                <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-[#94A3B8] uppercase">Available</span>
                        <span class="text-xl font-black text-[#0A192F] tracking-tighter"><?php echo rand(2, 10); ?> Tickets</span>
                    </div>
                    <a href="view_event_tickets.php" class="h-11 px-5 bg-[#0052FF] text-white text-[10px] flex items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-colors shadow-lg shadow-[#0052FF]/10">View</a>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </section>
</div>

<script>
function filterEvents(category, btn) {
    // Reset all buttons, highlight current one in blue
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.classList.remove('bg-[#0052FF]', 'text-white', 'shadow-xl', 'shadow-[#0052FF]/20');
        button.classList.add('bg-white', 'text-[#64748B]', 'shadow-sm');
    });
    btn.classList.add('bg-[#0052FF]', 'text-white', 'shadow-xl', 'shadow-[#0052FF]/20');
    btn.classList.remove('bg-white', 'text-[#64748B]', 'shadow-sm');

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