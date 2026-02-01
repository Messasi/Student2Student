</main>

        <footer class="bg-white border-t border-[#E2E8F0] mt-24">
            <div class="mx-auto px-6 lg:px-[60px] py-10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col items-center md:items-start">
                        <a href="/student2student/index.php" class="text-[1.2rem] font-extrabold tracking-tight text-[#0A192F] no-underline">
                            Student<span class="text-[#0052FF]">2</span>Student
                        </a>
                        <p class="text-[0.85rem] text-[#64748B] mt-2 font-medium">The secure marketplace for student event tickets.</p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-x-10 gap-y-4">
                        <a href="/student2student/index.php" class="text-[0.9rem] font-semibold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline">Discovery</a>
                        <a href="/student2student/listings/create.php" class="text-[0.9rem] font-semibold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline">Sell Tickets</a>
                        <a href="/student2student/listings/search.php" class="text-[0.9rem] font-semibold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline">Search</a>
                        <a href="/student2student/pages/faqs.php" class="text-[0.9rem] font-semibold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline">FAQs</a>
                    </div>

                    <div class="text-[0.85rem] text-[#64748B] font-bold uppercase tracking-wider">
                        &copy; <?php echo date('Y'); ?> S2S
                    </div>
                </div>
            </div>
        </footer>
    </div> 

    <div class="drawer-side z-[50]">
        <label for="mobile-drawer" class="drawer-overlay"></label>
        <div class="w-full min-h-full bg-white flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-[#E2E8F0]">
                <div class="text-[1.4rem] font-extrabold text-[#0A192F]">
                    Student<span class="text-[#0052FF]">2</span>Student
                </div>
                <label for="mobile-drawer" class="w-10 h-10 bg-[#F4F7FA] rounded-full text-2xl flex items-center justify-center cursor-pointer hover:bg-[#E2E8F0] transition-colors">
                    &times;
                </label>
            </div>

            <div class="flex-grow flex flex-col justify-center px-10 py-12 space-y-12">
                <nav class="flex flex-col space-y-8">
                    <a href="/student2student/index.php" class="block text-4xl font-extrabold text-[#0A192F] no-underline hover:text-[#0052FF] transition-colors">Discovery</a>
                    <a href="/student2student/listings/create.php" class="block text-4xl font-extrabold text-[#0A192F] no-underline hover:text-[#0052FF] transition-colors">Sell Tickets</a>
                    <a href="/student2student/pages/faqs.php" class="block text-4xl font-extrabold text-[#0A192F] no-underline hover:text-[#0052FF] transition-colors">FAQs</a>
                </nav>
                
                <div class="h-px bg-[#E2E8F0] w-full"></div>

                <nav class="flex flex-col space-y-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/student2student/dashboard/index.php" class="block text-xl font-bold text-[#0052FF] no-underline">Dashboard</a>
                        <a href="/student2student/auth/logout.php" class="block text-xl font-bold text-red-500 no-underline">Logout</a>
                    <?php else: ?>
                        <a href="/student2student/auth/login.php" class="block text-xl font-bold text-[#0A192F] no-underline hover:text-[#0052FF] transition-colors">Login</a>
                        <a href="/student2student/auth/register.php" class="block text-xl font-bold text-[#0052FF] no-underline">Register Account</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="/student2student/assets/js/main.js"></script>
</body>
</html>