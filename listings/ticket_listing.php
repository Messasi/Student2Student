<?php 
require_once '../config/database.php';
include '../includes/header.php'; 


//user has to be logged in to access this page

if (!isset($_SESSION['user_id'])) {
    
    // Redirect to login page with an alert message
    echo "<script>alert('Please log in to access the ticket listing page.'); window.location.href = '/student2student/auth/login.php';</script>";
    exit;
}

?>



<div class="min-h-screen bg-[#F5F8FA] font-sans text-[#0A192F] pb-24">
   <div class="max-w-4xl mx-auto px-6 pt-12 mb-8">
        <div class="flex items-center justify-between mb-8">
            <a href="javascript:history.back()" class="flex items-center text-[#64748B] hover:text-[#0052FF] transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-xs font-black uppercase tracking-widest">Back</span>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#0052FF]"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#0052FF]"></div>
            </div>
            
            <div class="w-10"></div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <div class="p-12">
                <div class="border-b border-[#F1F5F9] pb-8 mb-10">
                    <h1 class="text-3xl font-black tracking-tighter text-[#0A192F]">Add your ticket</h1>
                    <p class="text-sm text-[#64748B] font-medium mt-1">Start by providing the event link and your ticket file.</p>
                </div>

                <form action="ticket_details.php" method="POST" enctype="multipart/form-data" class="space-y-10">
                    
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-[#0A192F]">Event Link</label>
                        <input type="url" name="event_url" required 
                            class="w-full p-5 bg-[#F8FAFC] border-2 border-[#E2E8F0] rounded-2xl font-bold focus:bg-white focus:border-[#0052FF] outline-none transition-all placeholder:text-[#94A3B8]"
                            placeholder="Paste Fatsoma or Fixr URL">
                        <p class="text-[10px] text-[#64748B] font-medium italic px-1">We use this to verify event details and prevent fraud.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[11px] font-black uppercase tracking-widest text-[#0A192F]">Ticket File (PDF Only)</label>
                        <div class="relative group">
                            <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-[#E2E8F0] rounded-[2rem] bg-[#F8FAFC] cursor-pointer hover:border-[#0052FF] hover:bg-[#F0F7FF] transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-[#0052FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-black text-[#0A192F] uppercase tracking-tighter">Upload Ticket</p>
                                    <div class="mt-3 flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-[#0052FF] px-3 py-1 bg-[#0052FF]/10 rounded-full uppercase">Only PDF files accepted</span>
                                        <span class="text-[10px] font-black text-[#64748B] uppercase tracking-wider">Maximum 5MB</span>
                                    </div>
                                </div>
                                <input type="file" name="ticket_pdf" class="hidden" accept="application/pdf" required />
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-6 bg-[#0052FF] text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs hover:bg-[#0A192F] transition-all shadow-lg shadow-[#0052FF]/20">
                            Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>