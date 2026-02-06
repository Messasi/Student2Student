<?php include '../includes/header.php'; ?>

<div class="bg-white min-h-screen font-sans">
    <section class="relative bg-[#0A192F] rounded-b-[3rem] px-6 lg:px-[60px] py-24 overflow-hidden text-center">
        <div class="relative z-10 max-w-3xl mx-auto">
            <h1 class="text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-8 tracking-tight">
                What Makes Us Different?
            </h1>
            <p class="text-lg text-white/70 font-medium leading-relaxed mb-10">
                The student ticket market is broken and unsafe. We built Student2Student to fix that. Our platform is designed to protect both buyers and sellers, creating a secure environment for students to exchange tickets without fear of fraud.
            </p>
        </div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#0052FF]/10 rounded-full blur-[100px]"></div>
    </section>

    <section class="max-w-7xl mx-auto px-6 lg:px-[60px] -mt-16 relative z-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white border border-[#E2E8F0] p-10 rounded-[1.5rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
            <div class="w-2 h-2 flex items-center justify-center mb-6 transition-colors">
                </div>
            <h3 class="text-3xl font-bold text-[#0A192F] mb-4 tracking-tighter">The Price Scraper</h3>
            <p class="text-[#64748B] text-lg leading-relaxed font-medium">
                We cross-reference every listing against live data from ticketing platforms. If a ticket is listed for more than 30% above face value, we flag it. This keeps prices fair and prevents scalping.
            </p>
        </div>

        <div class="bg-white border border-[#E2E8F0] p-10 rounded-[1.5rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
            <div class="w-2 h-2 flex items-center justify-center mb-6 transition-colors">
            </div>
            <h3 class="text-3xl font-bold text-[#0A192F] mb-4 tracking-tighter">Digital Fingerprinting</h3>
            <p class="text-[#64748B] text-lg leading-relaxed font-medium">
                Using Perceptual Hashing (pHash), we scan every PDF uploaded. This ensures that no ticket is sold twice, even if the filename is changed.
            </p>
        </div>

        <div class="bg-white border border-[#E2E8F0] p-10 rounded-[1.5rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
            <div class="w-2 h-2 flex items-center justify-center mb-6 transition-colors">
                </div>
            <h3 class="text-3xl font-bold text-[#0A192F] mb-4 tracking-tighter">Secure Escrow</h3>
            <p class="text-[#64748B] text-lg leading-relaxed font-medium">
                Money is held in our virtual vault until 24 hours after the event. If the ticket doesn't work, the seller doesn't get paid. It's that simple.
            </p>
        </div>

    </div>
</section>
</div>

<?php include '../includes/footer.php'; ?>