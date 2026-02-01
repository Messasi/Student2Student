<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

/**
 * Function to handle active link styling without @apply
 * Design: Blue text + Light blue bg + Inner border when active
 * Design: Muted grey text + hover effect when inactive
 */
function getNavLinkClass($pageName, $current_page) {
    $base = "px-5 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all no-underline inline-block";
    if ($pageName === $current_page) {
        return "$base text-[#0052FF] bg-[#0052FF]/10 shadow-[inset_0_0_0_1px_rgba(0,82,255,0.1)]";
    }
    return "$base text-[#64748B] hover:text-[#0052FF] hover:bg-[#0052FF]/5";
}
?>
<!DOCTYPE html>
<html lang="en-GB" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Student2Student - Ticket Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="h-full text-[#0A192F] font-['Inter']">

<div class="drawer drawer-end h-full">
    <input id="mobile-drawer" type="checkbox" class="drawer-toggle" /> 
    
    <div class="drawer-content flex flex-col min-h-full">
        <header class="sticky top-0 z-40 w-full bg-white/85 backdrop-blur-md border-b border-[#E2E8F0]">
            <nav class="mx-auto px-6 lg:px-[60px] py-4 flex items-center justify-between min-h-[4.5rem] bg-white">
                
                <div class="flex-1 flex justify-start">
                    <a href="/student2student/index.php" class="text-[1.3rem] font-extrabold tracking-tight text-[#0A192F] no-underline">
                        Student<span class="text-[#0052FF]">2</span>Student
                    </a>
                </div>

                <ul class="hidden lg:flex items-center justify-center gap-2 m-0 p-0 list-none">
                    <li>
                        <a href="/student2student/index.php" class="<?php echo getNavLinkClass('index.php', $current_page); ?>">Discovery</a>
                    </li>
                    <li>
                        <a href="/student2student/listings/create.php" class="<?php echo getNavLinkClass('create.php', $current_page); ?>">Sell Tickets</a>
                    </li>
                    <li>
                        <a href="/student2student/pages/faqs.php" class="<?php echo getNavLinkClass('faqs.php', $current_page); ?>">FAQs</a>
                    </li>
                </ul>

                <div class="flex-1 flex items-center justify-end gap-5">
                    
                    <div class="hidden lg:block relative group">
                        <form action="/student2student/listings/search.php" method="GET">
                            <input type="text" name="query" placeholder="Search..." 
                                   class="w-[240px] pl-11 pr-4 py-[0.6rem] bg-[#f1f5f9] border border-transparent rounded-full text-[0.85rem] transition-all focus:w-[280px] focus:bg-white focus:border-[#0052FF] focus:outline-none focus:ring-0 focus:shadow-sm" />
                            <div class="absolute left-[14px] top-1/2 -translate-y-1/2 text-[#64748B]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                            </div>
                        </form>
                    </div>

                    <a href="/student2student/listings/search.php" class="lg:hidden p-2 text-[#0A192F] hover:text-[#0052FF] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </a>

                    <div class="flex items-center gap-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="cursor-pointer">
                                    <div class="w-10 h-10 rounded-full bg-[#0052FF]/10 flex items-center justify-center text-[#0052FF] transition-transform hover:scale-105">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                </label>
                                <ul tabindex="0" class="dropdown-content mt-4 p-2 shadow-lg bg-white border border-[#E2E8F0] rounded-2xl w-52 list-none">
                                    <li><a href="/student2student/dashboard/index.php" class="block px-4 py-3 text-sm font-medium hover:bg-[#F4F7FA] rounded-lg no-underline text-[#0A192F]">Dashboard</a></li>
                                    <li><a href="/student2student/auth/logout.php" class="block px-4 py-3 text-sm font-medium text-red-500 hover:bg-[#F4F7FA] rounded-lg no-underline">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="hidden lg:flex items-center gap-4">
                                <a href="/student2student/auth/login.php" class="text-[0.95rem] font-semibold text-[#64748B] hover:text-[#0052FF] transition-colors no-underline">Login</a>
                                <a href="/student2student/auth/register.php" class="bg-[#0052FF] text-white px-5 py-2.5 rounded-xl text-[0.95rem] font-bold hover:bg-[#0041CC] transition-all no-underline">Register</a>
                            </div>
                        <?php endif; ?>

                        <label for="mobile-drawer" class="lg:hidden flex flex-col justify-between w-[25px] h-[18px] cursor-pointer">
                            <span class="block w-full h-[2px] bg-[#0A192F] rounded-full"></span>
                            <span class="block w-full h-[2px] bg-[#0A192F] rounded-full"></span>
                            <span class="block w-full h-[2px] bg-[#0A192F] rounded-full"></span>
                        </label>
                    </div>
                </div>
            </nav>
        </header>

        <main class="flex-grow w-full">