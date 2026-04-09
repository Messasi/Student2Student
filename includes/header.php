<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_class = "page-" . basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en-GB" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Student2Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://unpkg.com/lucide@latest"></script>

    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/student2student/assests/css/style.css">
</head>
<body class="h-full bg-[#F4F7FA] text-[#0A192F] font-['Inter'] antialiased <?php echo $page_class; ?>">

<div class="flex flex-col min-h-full">
    <header class="sticky top-0 z-40 w-full bg-white/85 backdrop-blur-md border-b border-[#E2E8F0]">
        <nav class="mx-auto px-6 lg:px-[60px] py-4 grid grid-cols-2 lg:grid-cols-3 items-center min-h-[4.5rem]">
            
            <div class="flex justify-start items-center gap-3">
                <a href="/student2student/index.php" class="font-['Inter'] text-[1.3rem] font-extrabold tracking-tight text-[#0A192F] no-underline">
                    Student<span class="text-[#0052FF]">2</span>Student
                </a>
                <button id="mobile-search-toggle" class="lg:hidden p-2 text-[#64748B] hover:text-[#0052FF] transition-colors">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
            </div>

            <ul class="hidden lg:flex items-center justify-center gap-2 m-0 p-0 list-none">
                <li><a href="/student2student/index.php" class="nav-link nav-discovery">Discovery</a></li>
                <li><a href="/student2student/listings/ticket_listing.php" class="nav-link nav-create">Sell Tickets</a></li>
                
                <li><a href="/student2student/pages/events.php" class="nav-link nav-about">Events</a></li>
            </ul>

            <div class="flex items-center justify-end gap-5">
                <div class="hidden xl:block relative group">
                    <form action="/student2student/listings/search.php" method="GET">
                        <input type="text" name="query" placeholder="Search..." 
                               class="w-[200px] pl-11 pr-4 py-[0.6rem] bg-[#f1f5f9] border border-transparent rounded-full text-[0.85rem] transition-all focus:w-[240px] focus:bg-white focus:border-[#0052FF] focus:outline-none focus:shadow-sm" />
                        <div class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[#64748B]">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?> <!--allow login for testing purposes-->
                        <div class="relative inline-block text-left">
                            <button id="user-menu-button" class="w-10 h-10 rounded-full bg-[#0052FF]/10 flex items-center justify-center text-[#0052FF] border border-[#0052FF]/20 hover:bg-[#0052FF]/20 transition-all focus:outline-none">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </button>

                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 origin-top-right bg-white rounded-2xl border border-[#E2E8F0] shadow-xl z-50 overflow-hidden transition-all">
                                <div class="py-2">
                                    <a href="/student2student/profile/profile.php" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-[#0A192F] hover:bg-[#F8FAFC] transition-colors no-underline">
                                        <i data-lucide="user-round" class="w-4 h-4 text-[#64748B]"></i>
                                        Profile 
                                    </a>
                                    <a href="/student2student/dashboard/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-[#0A192F] hover:bg-[#F8FAFC] transition-colors no-underline">
                                        <i data-lucide="wallet" class="w-4 h-4 text-[#64748B]"></i>
                                        Financial Hub
                                    </a>
                                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>                       
                                        <a href="/student2student/admin/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#0052FF] hover:bg-blue-50 transition-colors no-underline">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            Admin Panel
                                        </a>
                                        
                                        <hr class="border-[#E2E8F0] my-1">
                                    <?php endif; ?>
                                    <a href="/student2student/dashboard/settings.php" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-[#0A192F] hover:bg-[#F8FAFC] transition-colors no-underline">
                                        <i data-lucide="settings" class="w-4 h-4 text-[#64748B]"></i>
                                        Settings
                                    </a>
                                    <hr class="border-[#E2E8F0] my-1">
                                    <a href="/student2student/auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition-colors no-underline">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        Log Out
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden lg:flex items-center gap-4">
                            <a href="/student2student/auth/login.php" class="bg-[#F4F7FA] text-[0.95rem] font-semibold text-[#64748B] hover:text-[#0052FF] no-underline px-4 py-2 rounded-lg font-['Inter']">Login</a>
                            <a href="/student2student/auth/register.php" class="bg-[#0052FF] text-white px-5 py-2.5 rounded-xl text-[0.95rem] font-bold hover:bg-[#0041CC] transition-all no-underline shadow-lg shadow-blue-500/20 font-['Inter']">Register</a>
                        </div>
                    <?php endif; ?>

                        <button id="open-menu-btn"type="button" class="lg:hidden text-[#0A192F] hover:text-[#0052FF] transition-colors bg-transparent border-none p-1">
                            <i data-lucide="menu" class="w-8 h-8"></i>
                        </button>
                </div>
            </div>
        </nav>
    </header>
    <main class="flex-grow w-full font-['Inter']">
  