</main>

    <footer class="bg-[#F8FAFC] border-t border-[#E2E8F0] mt-auto">
        <div class="mx-auto px-6 lg:px-[60px] py-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <a href="/student2student/index.php" class="text-xl font-extrabold tracking-tight text-[#0A192F] no-underline font-['Inter']">
                        Student<span class="text-[#0052FF]">2</span>Student
                    </a>
                    <p class="text-[0.85rem] text-[#64748B] mt-2 font-medium font-['Inter']">
                        The secure marketplace for student event tickets.
                    </p>
                </div>

                <nav class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                    <a href="/student2student/index.php" class="text-[0.9rem] font-bold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline font-['Inter']">Discovery</a>
                    <a href="/student2student/listings/create.php" class="text-[0.9rem] font-bold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline font-['Inter']">Sell Tickets</a>
                    <a href="/student2student/pages/about.php" class="text-[0.9rem] font-bold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline font-['Inter']">About</a>
                </nav>

                <div class="text-[0.8rem] text-[#94A3B8] font-bold uppercase tracking-widest font-['Inter']">
                    &copy; <?php echo date('Y'); ?> S2S. All Rights Reserved.
                </div>
            </div>
        </div>
    </footer>

    <div id="mobile-menu" class="fixed inset-0 z-[100] bg-white translate-x-full transition-transform duration-300 ease-in-out lg:hidden flex flex-col">
        <div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between">
            <div class="text-xl font-extrabold text-[#0A192F] font-['Inter']">
                Student<span class="text-[#0052FF]">2</span>Student
            </div>
           <button id="close-menu-btn" class="w-10 h-10 bg-[#F8FAFC] rounded-full flex items-center justify-center text-[#0A192F] cursor-pointer border-none">
                <i data-lucide="x" class="w-6 h-6"></i>
           </button>

          
        </div>

        <div class="flex-grow flex flex-col justify-between px-8 py-12">
            <nav class="flex flex-col space-y-8">
                <a href="/student2student/index.php" class="text-4xl font-extrabold text-[#0A192F] no-underline font-['Inter'] tracking-tight">Discovery</a>
                <a href="/student2student/listings/ticket_listing.php " class="text-4xl font-extrabold text-[#0A192F] no-underline font-['Inter'] tracking-tight">Sell Tickets</a>
                <a href="/student2student/pages/about.php" class="text-4xl font-extrabold text-[#0A192F] no-underline font-['Inter'] tracking-tight">Events</a>
            </nav>

            <div class="space-y-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/student2student/auth/login.php" class="block w-full text-center py-4 bg-[#F1F5F9] text-[#64748B] font-bold no-underline font-['Inter'] text-lg rounded-2xl">Login</a>
                    <a href="/student2student/auth/register.php" class="block w-full text-center py-5 bg-[#0052FF] text-white rounded-2xl font-bold no-underline font-['Inter'] text-lg shadow-xl shadow-blue-500/20">Create Account</a>
                 <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="/student2student/assests/js/main.js"></script>
</div>
</body>
</html>