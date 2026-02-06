<?php

include '../includes/header.php';
?>

<div class="bg-white min-h-screen flex items-center justify-center py-20">
    <div class="max-w-xl w-full px-6">
        <div class="bg-[#0A192F] rounded-[2rem] p-10 lg:p-16 text-white shadow-2xl relative overflow-hidden text-center">
            
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-20"></div>
            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-10"></div>

            <div class="relative z-10">
                
                <h1 class="text-4xl font-black uppercase tracking-tighter mb-4">Check Your Inbox</h1>
                
                <p class="text-white font-medium mb-10 tracking-tight leading-relaxed">
                    We have sent a verification link to your university email address. 
                    Please click the link in the email to activate your account and access the marketplace.
                </p>
                
                <div class="space-y-4">
                    <p class="text-xs text-white uppercase tracking-[0.2em] font-bold">
                        Didn't receive an email?
                    </p>
                    <div class="flex flex-col gap-3">
                        <a href="/student2student/auth/login.php" class="inline-block w-full bg-white/10 border border-white/20 text-white py-4 rounded-xl font-bold uppercase text-sm tracking-widest hover:bg-white/20 transition-all">
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialise Lucide icons
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>