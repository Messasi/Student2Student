<?php 
include '../includes/header.php'; 

// Mock data for people selling for this specific event
$sellers = [
    ['user' => 'j_thompson', 'price' => '25.00', 'type' => 'General Admission', 'verified' => true],
    ['user' => 'alice_un', 'price' => '22.50', 'type' => 'Early Bird', 'verified' => true],
    ['user' => 'mark_99', 'price' => '30.00', 'type' => 'VIP Deck', 'verified' => false],
];
?>

<div class="mx-auto px-6 lg:px-[60px] py-12 bg-[#F5F8FA] min-h-screen font-sans">
    <div class="max-w-5xl mx-auto mb-12">
        <a href="event_hub.php" class="flex items-center text-[#64748B] mb-8 hover:text-[#0052FF] transition-colors">
            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Back to Hub</span>
        </a>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="px-3 py-1 bg-[#0052FF]/10 text-[#0052FF] text-[10px] font-black uppercase rounded-lg mb-4 inline-block">Academic & Career</span>
                <h1 class="text-5xl font-black text-[#0A192F] tracking-tighter uppercase">Networking Dinner 2026</h1>
                <p class="text-[#64748B] font-bold mt-2">Whitworth Hall • Friday, 20th Feb</p>
            </div>
            <button class="bg-[#0A192F] text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-[#0052FF] transition-all">
                Sell Your Ticket
            </button>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-[2.5rem] border border-[#E2E8F0] overflow-hidden shadow-sm">
        <div class="p-8 border-b border-[#F1F5F9] flex justify-between items-center">
            <h3 class="font-black text-[#0A192F] uppercase tracking-tight">Available Tickets</h3>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-[#94A3B8] uppercase">Sort by:</span>
                <select class="text-xs font-black text-[#0052FF] bg-transparent outline-none cursor-pointer">
                    <option>Lowest Price</option>
                    <option>Recently Added</option>
                </select>
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F8FAFC]">
                    <th class="p-6 text-[10px] font-black uppercase text-[#94A3B8] tracking-widest">Seller</th>
                    <th class="p-6 text-[10px] font-black uppercase text-[#94A3B8] tracking-widest">Ticket Type</th>
                    <th class="p-6 text-[10px] font-black uppercase text-[#94A3B8] tracking-widest">Price</th>
                    <th class="p-6"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F1F5F9]">
                <?php foreach($sellers as $seller): ?>
                <tr class="hover:bg-[#FBFDFF] transition-colors">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-black">
                                <?php echo strtoupper(substr($seller['user'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="text-xs font-black text-[#0A192F]">@<?php echo $seller['user']; ?></p>
                                <?php if($seller['verified']): ?>
                                    <span class="text-[8px] font-black text-[#10B981] uppercase tracking-tighter">Verified Student</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="p-6">
                        <span class="text-xs font-bold text-[#64748B]"><?php echo $seller['type']; ?></span>
                    </td>
                    <td class="p-6">
                        <span class="text-lg font-black text-[#0A192F]">£<?php echo $seller['price']; ?></span>
                    </td>
                    <td class="p-6 text-right">
                        <a href="checkout.php" class="inline-flex h-10 px-6 bg-[#0052FF] text-white text-[10px] items-center rounded-xl font-black uppercase tracking-widest hover:bg-[#0A192F] transition-all">
                            Buy
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>lucide.createIcons();</script>