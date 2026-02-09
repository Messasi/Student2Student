
<?php 
require_once '../config/database.php';
include '../includes/header.php'; 
?>

<div class="min-h-screen bg-[#F5F8FA] font-sans text-[#0A192F] pb-24">
    <div class="max-w-xl mx-auto px-6 pt-8 mb-12">
        <div class="flex items-center justify-between mb-8">
            <a href="javascript:history.back()" class="flex items-center text-[#64748B] hover:text-[#0052FF] transition-all group">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-[#E2E8F0] mr-2 group-hover:border-[#0052FF] transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-black uppercase tracking-widest">Back</span>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#0052FF]"></div>
            </div>
            
            <div class="w-10"></div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <form action="ticket_review.php" method="POST" class="p-12 space-y-10">
                
                <div class="border-b border-[#F1F5F9] pb-8">
                    <h2 class="text-3xl font-black tracking-tighter text-[#0A192F]">Ticket Details</h2>
                    <p class="text-sm text-[#64748B] font-medium mt-1">Please confirm the information extracted from your upload.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Event Name</label>
                        <input type="text" name="event_name" placeholder="Event name" required
                            class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold focus:bg-white focus:border-[#0052FF] outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Venue</label>
                        <input type="text" name="venue" placeholder="Venue name" required
                            class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold focus:bg-white focus:border-[#0052FF] outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Category</label>
                        <div class="relative">
                            <select name="category" class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold appearance-none focus:bg-white focus:border-[#0052FF] outline-none cursor-pointer pr-10">
                                <option value="club">Club Night</option>
                                <option value="sports">Sports</option>
                                <option value="society">Society</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#64748B]">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-[#94A3B8]">Event Date</label>
                        <div class="relative">
                            <input type="date" name="event_date" required
                                class="w-full p-4 bg-[#F8FAFC] border-2 border-transparent rounded-xl font-bold focus:bg-white focus:border-[#0052FF] outline-none transition-all appearance-none">
                        </div>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-12 p-8 bg-[#F0F7FF] rounded-2xl border border-[#0052FF]/5 mt-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-[#0052FF]">Retail Price (£)</label>
                            <input type="number" step="0.01" name="retail_price" placeholder="0.00"
                                class="w-full p-4 bg-white border-2 border-[#E2E8F0] rounded-xl font-bold focus:border-[#0052FF] outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#0052FF]">Your Price (£)</label>
                                <span class="text-[9px] font-bold text-[#10B981] uppercase">Max +40%</span>
                            </div>
                            <input type="number" step="0.01" name="selling_price" placeholder="0.00"
                                class="w-full p-4 bg-white border-2 border-[#0052FF] rounded-xl font-black text-[#0052FF] focus:ring-4 focus:ring-[#0052FF]/10 outline-none transition-all shadow-sm">
                        </div>
                    </div>

                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-5 bg-[#0052FF] text-white rounded-xl font-black uppercase tracking-[0.2em] text-xs hover:bg-[#0A192F] transition-all shadow-lg shadow-[#0052FF]/20">
                        Continue to Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialise Lucide icons
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>

