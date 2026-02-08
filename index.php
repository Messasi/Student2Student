<?php
require_once 'config/database.php';
include 'includes/header.php';

function renderTicketRow($title) {
    // Array of mock data
    $mock_data = [
        ['event' => 'Warehouse Project', 'info' => 'Depot Mayfield', 'price' => '35.00', 'user' => 'j_thompson', 'verified' => true],
        ['event' => 'Tech House Night', 'info' => 'The Invisible Windmill', 'price' => '12.50', 'user' => 'sarah.stu', 'verified' => false],
        ['event' => 'Varsity Afterparty', 'info' => 'O2 Academy', 'price' => '10.00', 'user' => 'mike_v', 'verified' => true],
        ['event' => 'Society Gala 2026', 'info' => 'Grand Hall', 'price' => '45.00', 'user' => 'emma_h', 'verified' => false],
    ];

    echo '
    <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-[#0A192F] tracking-tight font-sans">'.$title.'</h2>
        </div>
        
        <div class="flex lg:grid lg:grid-cols-4 gap-6 overflow-x-auto lg:overflow-x-visible pb-4 lg:pb-0 scrollbar-hide snap-x snap-mandatory">
            ';
            foreach($mock_data as $data) {
                echo '
                <div class="min-w-full lg:min-w-0 bg-white border border-[#E2E8F0] rounded-2xl p-6 hover:border-[#0052FF]/30 transition-colors shadow-sm snap-center lg:snap-align-none">
                    
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase">
                            '.substr($data['user'], 0, 1).'
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-[#0A192F] font-sans">@'.$data['user'].'</span>
                            '.($data['verified'] ? '<span class="text-[9px] font-bold text-[#0052FF] uppercase tracking-tighter">Verified Price</span>' : '').'
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-xl mb-4 flex items-center justify-center border border-[#F1F5F9]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#CBD5E1]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>

                    <div class="text-sm font-bold text-[#0A192F] font-sans mb-1 uppercase truncate">'.$data['event'].'</div>
                    <div class="text-xs font-medium text-[#64748B] font-sans mb-6">'.$data['info'].'</div>
                    
                    <div class="flex justify-between items-center pt-4 border-t border-[#F1F5F9]">
                        <span class="text-lg font-bold text-[#0A192F] font-sans">£'.$data['price'].'</span>
                        <div class="h-8 px-4 bg-[#0052FF] text-white text-xs flex items-center rounded-lg font-bold font-sans uppercase tracking-wide cursor-pointer hover:bg-[#0041CC] transition-colors">Buy</div>
                    </div>
                </div>';
            }
            echo '
        </div>
    </section>';
}
?>  
<style> 

   /*Smooth scrolling */
html {
    scroll-behavior: smooth;
}  
</style>
  

<div class="bg-white min-h-screen font-inter">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <section class="relative bg-[#0A192F] rounded-[2rem] px-8 lg:px-20 py-24 mb-16 overflow-hidden border-2 border-white/10 shadow-xl">
        
        <div class="absolute -bottom-10 -right-10 opacity-5 select-none pointer-events-none">
            <h2 class="text-[18rem] font-black leading-none uppercase">CORE</h2>
        </div>

        <div class="relative z-10 flex flex-col items-start text-left">
            
            <div class="inline-block px-4 py-1 border-2 border-[#0052FF] text-white text-xs font-black uppercase tracking-widest mb-10">
                Student to student only
            </div>
            
            <h1 class="text-6xl lg:text-[110px] font-black text-white leading-[0.8] mb-12 tracking-tighter uppercase">
                Zero <span class="text-[#0052FF]">Fraud.</span> <br> 
                Zero Worries.
            </h1>

            <div class="max-w-xl mb-12">
                <div class="h-1 bg-[#0052FF] w-24 mr-auto mb-6"></div>
                <p class="text-xl text-white font-bold leading-tight opacity-90">
                    The student marketplace hub for 2nd hand tickets. <br>
                    Buy and sell tickets with confidence.
                </p>
            </div>
            
            <div class="flex flex-wrap justify-start gap-4">
                <a href="/student2student/listings/create.php" 
                class="bg-[#0052FF] text-white px-8 py-4 rounded-xl font-bold hover:bg-[#0041CC] transition-all shadow-lg font-sans">
                    Sell Your Ticket
                </a>
                <a href="#listings-anchor" class="bg-transparent text-white px-8 py-4 rounded-xl font-bold border-2 border-white hover:bg-white hover:text-[#0A192F] transition-all font-sans">
                    Browse Store
                </a>
            </div>
        </div>
    </section>

        <div id="listings-anchor" class="w-full mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    
    <div  class="flex-shrink-0">
        <h1 class="text-4xl lg:text-5xl font-extrabold text-[#0A192F] tracking-tight font-sans">
            Available Tickets
        </h1>
        <div class="h-1.5 w-20 bg-[#0052FF] mt-4 rounded-full"></div>
    </div>

    <div class="flex flex-row items-center gap-4 ml-auto">
        
        <div class="relative">
            <select class="appearance-none bg-white border-2 border-[#E2E8F0] text-[#0A192F] py-3 pl-4 pr-10 rounded-xl font-bold font-sans text-sm focus:outline-none focus:border-[#0052FF] focus:ring-2 focus:ring-[#0052FF]/20 transition-all cursor-pointer min-w-[160px]">
                <option value="" class="font-sans font-medium text-[#0A192F]">All Categories</option>
                <option value="club-nights" class="font-sans font-medium text-[#0A192F]">Club Nights</option>
                <option value="sports" class="font-sans font-medium text-[#0A192F]">Sports Matches</option>
                <option value="society" class="font-sans font-medium text-[#0A192F]">Society Events</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#0052FF]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>

        <div class="relative">
            <select class="appearance-none bg-white border-2 border-[#E2E8F0] text-[#0A192F] py-3 pl-10 pr-4 rounded-xl font-bold font-sans text-sm focus:outline-none focus:border-[#0052FF] focus:ring-2 focus:ring-[#0052FF]/20 transition-all cursor-pointer min-w-[160px]">
                <option value="newest" class="font-sans font-medium text-[#0A192F]">Sort: Newest</option>
                <option value="price-low" class="font-sans font-medium text-[#0A192F]">Price: Low-High</option>
                <option value="price-high" class="font-sans font-medium text-[#0A192F]">Price: High-Low</option>
                <option value="date" class="font-sans font-medium text-[#0A192F]">Event Date</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[#0052FF]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
            </div>
        </div>

    </div>
</div>

        <div class="space-y-4">
            <?php 
                renderTicketRow('Recommended For You'); 
                renderTicketRow('Society Events'); 
                renderTicketRow('Sports Matches'); 
                renderTicketRow('Club Nights'); 
            ?>
        </div>

        <section class="bg-[#0A192F] rounded-[2rem] px-8 lg:px-16 py-16 text-center mt-20 relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-5xl font-extrabold text-white mb-4 font-sans tracking-tight">Ready to start?</h1>
                <p class="text-XL text-white/60 mb-8 font-medium max-w-xl mx-auto font-sans">
                    Create an account using your university email to start buying and selling tickets.
                </p>
                <a href="/student2student/auth/register.php" class="inline-block bg-[#0052FF] text-white px-10 py-4 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all shadow-lg font-sans">
                    Create Free Account
                </a>
            </div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-[#0052FF]/5 rounded-full blur-[80px]"></div>
        </section>
    </div>
</div>
<?php include 'includes/footer.php'; ?>