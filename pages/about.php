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
            
            <div class="bg-white border border-[#E2E8F0] p-10 rounded-[2rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
                <div class="w-14 h-14 bg-[#F8FAFC] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#0052FF] transition-colors">
                    <svg class="w-8 h-8 text-[#0052FF] group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-[#0A192F] mb-4">The Price Scraper</h3>
                <p class="text-[#64748B] leading-relaxed">
                    We cross-reference every listing against live data from Fatsoma and Fixr. If the price is more than 30% above face value, it is automatically blocked.
                </p>
            </div>

            <div class="bg-white border border-[#E2E8F0] p-10 rounded-[2rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
                <div class="w-14 h-14 bg-[#F8FAFC] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#0052FF] transition-colors">
                    <svg class="w-8 h-8 text-[#0052FF] group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.14c1.89-2.14 3.193-4.823 3.193-7.431V7a2 2 0 012-2h3.911a2 2 0 011.85 2.821M9.913 5c1.251-1.036 2.742-1.667 4.087-1.667 4.605 0 8.339 3.85 8.339 8.59 0 2.278-.88 4.351-2.313 5.923m-7.205 1.417A21.182 21.182 0 013 13V11c0-4.74 3.734-8.59 8.339-8.59 1.345 0 2.836.63 4.087 1.667m-4.087 1.667V11a2 2 0 002 2h3.911a2 2 0 001.85-2.821"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-[#0A192F] mb-4">Digital Fingerprinting</h3>
                <p class="text-[#64748B] leading-relaxed">
                    Using Perceptual Hashing (pHash), we scan every PDF uploaded. This ensures that no ticket is sold twice, even if the filename is changed.
                </p>
            </div>

            <div class="bg-white border border-[#E2E8F0] p-10 rounded-[2rem] shadow-xl hover:border-[#0052FF]/30 transition-all group">
                <div class="w-14 h-14 bg-[#F8FAFC] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#0052FF] transition-colors">
                    <svg class="w-8 h-8 text-[#0052FF] group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-[#0A192F] mb-4">Secure Escrow</h3>
                <p class="text-[#64748B] leading-relaxed">
                    Money is held in our virtual vault until 24 hours after the event. If the ticket doesn't work, the seller doesn't get paid. It's that simple.
                </p>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>