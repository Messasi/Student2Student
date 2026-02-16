<?php
include './includes/header.php';
?>

<div class="bg-white min-h-screen font-sans flex items-center justify-center">
    <div class="mx-auto px-6 py-12 max-w-2xl text-center">
        
        <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 border border-green-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-5xl font-extrabold text-[#0A192F] tracking-tight mb-6">
            Order Confirmed!
        </h1>
        
        <div class="h-1.5 w-20 bg-[#0052FF] mx-auto rounded-full mb-10"></div>

        <p class="text-lg text-[#64748B] font-medium mb-12 leading-relaxed">
            Thank you for your purchase. We have sent the ticket PDF to your <span class="text-[#0A192F] font-bold">personal email address</span>. Please check your inbox (and spam folder) shortly.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="index.php" class="bg-[#0A192F] text-white px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-[#1a2e4d] transition-all">
                Back to Browse
            </a>
            <a href="/student2student/dashboard/dashboard.php" class="bg-white border-2 border-[#E2E8F0] text-[#0A192F] px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:border-[#0052FF] transition-all">
                View My Tickets
            </a>
        </div>

        <p class="mt-16 text-xs text-[#64748B] font-bold uppercase tracking-widest opacity-60">
            Transaction ID: CORE-<?php echo strtoupper(bin2hex(random_bytes(4))); ?>
        </p>

    </div>
</div>

<?php include './includes/footer.php'; ?>