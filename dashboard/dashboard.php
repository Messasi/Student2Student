<?php
// require_once '../config/database.php'; // Commented out to prevent connection hangs
include '../includes/header.php';

// Static Data - No loops, no logic, no session start
$total_earnings = "145.50";
$withdrawable = "145.50";
$pending = "32.00";
?>

<div class="bg-white min-h-screen text-[#0A192F]">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold mb-6 tracking-tight">Financial Hub</h1>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Total Earnings</p>
                    <p class="text-5xl font-extrabold mt-4">£<?php echo $total_earnings; ?></p>
                </div>

                <div class="bg-green-50 border border-green-100 rounded-2xl p-12">
                    <p class="text-sm font-bold uppercase text-gray-500 tracking-wider">Withdrawable Amount</p>
                    <p class="text-5xl font-extrabold mt-4">£<?php echo $withdrawable; ?></p>
                    <p class="mt-4 font-bold">Pending: £<?php echo $pending; ?></p>
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">Tickets Sold</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Buyer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold">Summer Ball 2026</td>
                            <td class="px-6 py-4">Alex</td>
                            <td class="px-6 py-4 text-blue-600 font-bold">£45.00</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold">Tech Conference</td>
                            <td class="px-6 py-4">Jordan</td>
                            <td class="px-6 py-4 text-blue-600 font-bold">£100.50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">My Active Listings</h2>
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Remove</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 font-bold">Winter Gala</td>
                            <td class="px-6 py-4">£25.00</td>
                            <td class="px-6 py-4">
                                <button class="px-3 py-1 bg-red-90 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                    <i data-lucide="trash" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-extrabold mb-6">Purchase History</h2>
            <div class="space-y-4">
                
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-1">Freshers Finale</h3>
                            <p class="text-sm text-gray-500 font-medium">Main Arena | 30 Sep 2026 | Sold by Sarah</p>
                        </div>
                       <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-xl font-extrabold">£30.00</p>
                                <span class="text-[10px] font-bold text-green-700 uppercase bg-green-100 px-2 py-0.5 rounded tracking-tight">Completed</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="download_ticket.php?id=1" 
                                title="Download Ticket" 
                                class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                </a>

                                <button onclick="confirmReport()" 
                                        title="Report Issue" 
                                        class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                    <i data-lucide="flag" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-1">Reading Festival Day 1</h3>
                            <p class="text-sm text-gray-500 font-medium">Richfield Avenue | 25 Aug 2026 | Sold by James</p>
                        </div>
                       <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-xl font-extrabold">£30.00</p>
                                <span class="text-[10px] font-bold text-green-700 uppercase bg-green-100 px-2 py-0.5 rounded tracking-tight">Completed</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="download_ticket.php?id=1" 
                                title="Download Ticket" 
                                class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                </a>

                                <button onclick="confirmReport()" 
                                        title="Report Issue" 
                                        class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                    <i data-lucide="flag" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-1">Comedy Night</h3>
                            <p class="text-sm text-gray-500 font-medium">Student Union | 12 Mar 2026 | Sold by Emma</p>
                        </div>
                       <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-xl font-extrabold">£30.00</p>
                            <span class="text-[10px] font-bold text-green-700 uppercase bg-green-100 px-2 py-0.5 rounded tracking-tight">Completed</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="download_ticket.php?id=1" 
                            title="Download Ticket" 
                            class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                <i data-lucide="download" class="w-5 h-5"></i>
                            </a>

                            <button onclick="confirmReport()" 
                                    title="Report Issue" 
                                    class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                <i data-lucide="flag" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>