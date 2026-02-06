<?php

require_once '../config/database.php';
include '../includes/header.php';



$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, personal_email, email, profile_picture, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>

<div class="bg-[#F8FAFC] min-h-screen">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <aside class="lg:w-1/4">
                <div class="sticky top-8 space-y-2">
                    <h2 class="text-2xl font-extrabold text-[#0A192F] mb-6">Settings</h2>
                    <nav class="flex flex-col space-y-1">
                        <a href="#profile" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#0052FF] bg-blue-50 rounded-xl transition-all">
                            <i data-lucide="user" class="w-5 h-5"></i> Profile Customisation
                        </a>
                        <a href="#account" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#64748B] hover:bg-gray-100 rounded-xl transition-all">
                            <i data-lucide="settings" class="w-5 h-5"></i> Account Details
                        </a>
                        <a href="#bank" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#64748B] hover:bg-gray-100 rounded-xl transition-all">
                            <i data-lucide="landmark" class="w-5 h-5"></i> Bank Details
                        </a>
                        <a href="#security" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#64748B] hover:bg-gray-100 rounded-xl transition-all">
                            <i data-lucide="shield-check" class="w-5 h-5"></i> Password & Security
                        </a>
                        <a href="#faq" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-bold text-[#64748B] hover:bg-gray-100 rounded-xl transition-all">
                            <i data-lucide="help-circle" class="w-5 h-5"></i> FAQ & Support
                        </a>
                    </nav>
                </div>
            </aside>

            <main class="lg:w-3/4 space-y-12">

                <section id="profile" class="bg-white rounded-3xl p-8 shadow-sm border border-[#E2E8F0]">
                    <h3 class="text-xl font-bold text-[#0A192F] mb-6">Profile Customisation</h3>
                    <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center border-2 border-[#E2E8F0] relative overflow-hidden group">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-[#0A192F] flex items-center justify-center">
                                        <span class="text-3xl font-black text-white uppercase">
                                            <?php echo substr($user['username'], 0, 1); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                    <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                                </div>
                                <input type="file" name="profile_picture" class="absolute inset-0 opacity-0 cursor-pointer" onchange="this.form.submit()">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#0A192F]">Profile Picture</p>
                                <p class="text-xs text-[#64748B]">Click the circle to upload. Max 2MB.</p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">Username</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 focus:outline-none focus:border-[#0052FF]">
                            </div>
                        </div>
                        <button type="submit" class="bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all">Save Profile</button>
                    </form>
                </section>

                <section id="account" class="bg-white rounded-3xl p-8 shadow-sm border border-[#E2E8F0]">
                    <h3 class="text-xl font-bold text-[#0A192F] mb-6">Account Details</h3>
                    <form action="update_account.php" method="POST" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">Personal Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['personal_email']); ?>" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">University Email</label>
                                <input type="email" name="uni_email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                        </div>
                        <button type="submit" class="bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all">Update Emails</button>
                    </form>
                </section>

                <section id="bank" class="bg-white rounded-3xl p-8 shadow-sm border border-[#E2E8F0]">
                    <div class="flex items-center gap-3 mb-6">
                        <h3 class="text-xl font-bold text-[#0A192F]">Bank Details</h3>
                    </div>
                    <form action="update_bank.php" method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-[#0A192F]">Account Holder Name</label>
                            <input type="text" name="acc_name" placeholder="John Smith" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 focus:outline-none">
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">Sort Code</label>
                                <input type="text" name="sort_code" placeholder="00-00-00" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">Account Number</label>
                                <input type="text" name="acc_num" placeholder="12345678" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                        </div>
                        <button type="submit" class="bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all">Save Bank Details</button>
                    </form>
                </section>

                <section id="security" class="bg-white rounded-3xl p-8 shadow-sm border border-[#E2E8F0]">
                    <h3 class="text-xl font-bold text-[#0A192F] mb-6">Change Password</h3>
                    <form action="update_password.php" method="POST" class="space-y-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">Current Password</label>
                                <input type="password" name="current_password" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-[#0A192F]">New Password</label>
                                <input type="password" name="new_password" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3">
                            </div>
                        </div>
                        <button type="submit" class="bg-[#0052FF] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#0041CC] transition-all">Update Password</button>
                    </form>
                    <div class= "pt-4 pb-4text-sm text-[#64748B]">
                        For security reasons, we recommend changing your password every 3-6 months.
                    <?php if (isset($_GET['success']) && $_GET['success'] == 'password'): ?>
                        <div class="pt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 font-bold flex items-center gap-2">
                         <i data-lucide="check-circle" class="w-4 h-4"></i> Password updated successfully!
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'wrong_pass'): ?>
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6 font-bold flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i> The current password you entered is incorrect.
                        </div>
                    <?php endif; ?>
                </section>

                <section id="faq" class="bg-white rounded-3xl p-8 shadow-sm border border-[#E2E8F0]">
                    <div class="flex items-center gap-3 mb-6">
                      
                        <h3 class="text-xl font-bold text-[#0A192F]">Frequently Asked Questions</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="group border-b border-[#E2E8F0] pb-4">
                            <button class="w-full flex justify-between items-center text-left py-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                <span class="font-bold text-[#0A192F]">When will I receive my money?</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#64748B]"></i>
                            </button>
                            <div class="hidden mt-2 text-sm text-[#64748B] leading-relaxed">
                                Funds are typically released 24 hours after the event has taken place. This delay is a security measure to protect buyers from fraud.
                            </div>
                        </div>

                        <div class="group border-b border-[#E2E8F0] pb-4">
                            <button class="w-full flex justify-between items-center text-left py-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                <span class="font-bold text-[#0A192F]">How do I verify my account?</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#64748B]"></i>
                            </button>
                            <div class="hidden mt-2 text-sm text-[#64748B] leading-relaxed">
                                Verification requires a valid university email address. Ensure your University Email field under "Account Details" is correct and click the link sent to your inbox.
                            </div>
                        </div>

                        <div class="group border-b border-[#E2E8F0] pb-4">
                            <button class="w-full flex justify-between items-center text-left py-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                <span class="font-bold text-[#0A192F]">What are the selling fees?</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#64748B]"></i>
                            </button>
                            <div class="hidden mt-2 text-sm text-[#64748B] leading-relaxed">
                                Student2Student is a community platform. We charge a minimal 5% service fee on successful sales to maintain the platform and secure payments.
                            </div>
                        </div>

                        <div class="group border-b border-[#E2E8F0] pb-4">
                            <button class="w-full flex justify-between items-center text-left py-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                <span class="font-bold text-[#0A192F]">Is my bank information secure?</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#64748B]"></i>
                            </button>
                            <div class="hidden mt-2 text-sm text-[#64748B] leading-relaxed">
                                Yes. We do not store your full bank details on our local servers. All financial data is handled by our encrypted payment processor to ensure maximum security.
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-6 bg-[#F8FAFC] rounded-2xl border border-[#E2E8F0]">
                        <p class="text-sm font-bold text-[#0A192F] mb-2">Still need help?</p>
                        <p class="text-xs text-[#64748B] mb-4">Our support team is available for any specific issues regarding transactions.</p>
                        <a href="mailto:support@student2student.ac.uk" class="inline-flex items-center gap-2 text-[#0052FF] text-sm font-bold hover:underline">
                            <i data-lucide="mail" class="w-4 h-4"></i> Contact Support
                        </a>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>



<?php include '../includes/footer.php'; ?>