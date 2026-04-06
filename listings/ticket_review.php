<?php 

// We also pull the ticket details they just typed in from the previous form.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// We look for the logged-in student's ID in the session.
$user_id = $_SESSION['user_id'] ?? null; 
$username = "Student"; 
$profile_pic = null;

if ($user_id) {
    // We fetch the username and profile picture filename from the users table.
    $stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If we find the student in the database, we use their real details.
    if ($user) {
        $username = $user['username'] ?? "Student";
        $profile_pic = $user['profile_picture'] ?? null;
    } else {
        // If they aren't found, we just show a generic "Student" name as a fallback.
        $username = "Student";
        $profile_pic = null;
    }
    $stmt->close();
}

// We grab the event info and selling price that was sent over from the last page.
$event_name = $_POST['event_name'] ?? "Unknown Event";
$location = $_POST['location'] ?? "Unknown Venue";
$price = $_POST['selling_price'] ?? "0.00";
$category = trim($_POST['category'] ?? 'other');
$event_date = $_POST['event_date'] ?? "";

include '../includes/header.php'; 
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

                <div class="max-w-sm mx-auto bg-white border border-[#E2E8F0] rounded-[2rem] p-6 shadow-sm">
                    
                    <div class="flex items-center gap-3 mb-5">
                        <?php if (!empty($profile_pic)): ?>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_pic); ?>" 
                                 class="w-10 h-10 rounded-full object-cover border border-[#E2E8F0]">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-[#0A192F] flex items-center justify-center text-white text-[10px] font-bold uppercase">
                                <?php echo substr($username, 0, 1); ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-col">
                            <span class="text-xs font-black text-[#0A192F]">@<?php echo htmlspecialchars($username); ?></span>
                            <span class="text-[9px] font-black text-[#0052FF] uppercase tracking-tighter">Verified Student</span>
                        </div>
                    </div>

                    <div class="w-full aspect-video bg-[#F8FAFC] rounded-2xl mb-5 flex items-center justify-center border border-[#F1F5F9]">
                        <i data-lucide="ticket" class="w-10 h-10 text-[#CBD5E1]"></i>
                    </div>

                    <div class="text-md font-black text-[#0A192F] mb-1 uppercase truncate"><?php echo htmlspecialchars($event_name); ?></div>
                    <div class="text-xs font-bold text-[#64748B] mb-6"><?php echo htmlspecialchars($location); ?></div>
                    
                    <div class="flex justify-between items-center pt-5 border-t border-[#F1F5F9]">
                        <span class="text-xl font-black text-[#0A192F]">£<?php echo number_format((float)$price, 2); ?></span>
                        <div class="h-8 px-4 bg-[#0052FF]/5 text-[#0052FF] text-[9px] flex items-center rounded-lg font-black uppercase tracking-widest border border-[#0052FF]/10">
                            Buy     
                        </div>
                    </div>
                </div>

                <form action="../actions/finalise_listing.php" method="POST" class="mt-10 max-w-sm mx-auto">
                    <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>">
                    <input type="hidden" name="retail_price" value="<?php echo htmlspecialchars($_POST['retail_price'] ?? '0.00'); ?>">

                    <button type="submit" class="w-full py-5 bg-[#0052FF] text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs hover:bg-[#0A192F] transition-all shadow-xl">
                        Publish Ticket 
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Just a quick script to make sure the icons look right on the preview card
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>