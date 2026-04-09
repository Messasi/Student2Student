<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}

function renderAdminHeader($title) {
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - <?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
       
        .admin-card { background: white; border: 1px solid #E2E8F0; border-radius: 1.5rem; }
    </style>
</head>
<body class="text-[#0A192F]">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-[#0A192F] text-white p-8 flex flex-col fixed h-full">
            <div class="mb-12">
                <h1 class="text-xl font-bold uppercase tracking-tight text-white">Admin Panel</h1>
            </div>
            <nav class="space-y-4 flex-grow">
                <a href="dashboard.php" class="flex items-center gap-3 text-sm font-bold opacity-70 hover:opacity-100 no-underline transition-all"><i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard</a>
                <a href="users.php" class="flex items-center gap-3 text-sm font-bold opacity-70 hover:opacity-100 no-underline transition-all"><i data-lucide="users" class="w-4 h-4"></i> User Management</a>
                <a href="listings.php" class="flex items-center gap-3 text-sm font-bold opacity-70 hover:opacity-100 no-underline transition-all"><i data-lucide="ticket" class="w-4 h-4"></i> Listing Review</a>
                <a href="orders.php" class="flex items-center gap-3 text-sm font-bold opacity-70 hover:opacity-100 no-underline transition-all"><i data-lucide="shopping-bag" class="w-4 h-4"></i> Order History</a>
            </nav>
            <div class="pt-8 border-t border-white/10">
                <a href="../index.php" class="text-xs font-black uppercase tracking-widest text-[#64748B] hover:text-white no-underline transition-all">Exit to Site</a>
            </div>
        </aside>

        <main class="flex-grow ml-64 p-12">
            <header class="flex justify-between items-center mb-12">
                <h2 class="text-4xl font-black uppercase tracking-tighter"><?php echo $title; ?></h2>
                </header>
<?php } 

function renderAdminFooter() {
    echo '</main></div><script>lucide.createIcons();</script></body></html>';
} ?>