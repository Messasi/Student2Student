<?php 
require_once '../config/database.php';
include '../includes/header.php'; 

// Example variables usually passed from Step 2
$user_display = "StudentUser"; 
$event_name = $_POST['event_name'] ?? "Warehouse Project";
$event_info = $_POST['venue'] ?? "Depot Mayfield, Manchester";
$price = $_POST['selling_price'] ?? "0.00";
?>

<div class="min-h-screen bg-[#F5F8FA] font-sans text-[#0A192F] pb-24">
    <div class="max-w-xl mx-auto px-6 pt-12 mb-8">
        <div class="flex items-center justify-between mb-8">
            <a href="javascript:history.back()" class="flex items-center text-[#64748B] hover:text-[#0052FF] transition-all group">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-[#E2E8F0] mr-2 group-hover:border-[#0052FF] transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4 text-[#0A192F]"></i>
                </div>
                <span class="text-xs font-black uppercase tracking-widest">Back</span>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
            </div>
            
            <div class="w-10"></div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-6">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <div class="p-10">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black tracking-tighter text-[#0A192F]">Preview your listing</h1>
                </div>

                <div class="max-w-sm mx-auto bg-white border border-[#E2E8F0] rounded-[2rem] p-6 hover:border-[#0052FF]/30 transition-colors shadow-sm">
                    
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase">
                            <?php echo substr($user_display, 0, 1); ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-[#0A192F] font-sans">@<?php echo $user_display; ?></span>
                            <span class="text-[9px] font-black text-[#0052FF] uppercase tracking-tighter">Verified Price</span>
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                        <i data-lucide="ticket" class="w-10 h-10 text-[#CBD5E1]"></i>
                    </div>

                    <div class="text-md font-black text-[#0A192F] font-sans mb-1 uppercase truncate"><?php echo $event_name; ?></div>
                    <div class="text-xs font-bold text-[#64748B] font-sans mb-6"><?php echo $event_info; ?></div>
                    
                    <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                        <span class="text-xl font-black text-[#0A192F] font-sans">£<?php echo $price; ?></span>
                        <div class="h-8 px-4 bg-[#0052FF]/5 text-[#0052FF] text-[9px] flex items-center rounded-lg font-black font-sans uppercase tracking-widest border border-[#0052FF]/10">
                            Preview
                        </div>
                    </div>
                </div>

                <form action="../actions/finalise_listing.php" method="POST" class="mt-10 max-w-sm mx-auto">
                    <input type="hidden" name="final_confirm" value="1">
                    <button type="submit" class="w-full py-5 bg-[#0052FF] text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs hover:bg-[#0A192F] transition-all shadow-xl shadow-[#0052FF]/20">
                        Publish Ticket 
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>



