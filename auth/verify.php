<?php
require_once '../config/database.php';
include '../includes/header.php';

$message = '';
$message_type = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and is not expired
    $stmt = mysqli_prepare($conn, "SELECT user_id, expires_at FROM verification_tokens WHERE token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $user_id = $row['user_id'];
        $expires_at = $row['expires_at'];
        
        // Check if token has expired
        if (strtotime($expires_at) < time()) {
            $message = 'Verification link has expired. Please register again.';
            $message_type = 'error';
        } else {
            // Update user as verified
            $stmt_update = mysqli_prepare($conn, "UPDATE users SET is_verified = TRUE WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt_update)) {
                // Delete used token
                $stmt_delete = mysqli_prepare($conn, "DELETE FROM verification_tokens WHERE token = ?");
                mysqli_stmt_bind_param($stmt_delete, "s", $token);
                mysqli_stmt_execute($stmt_delete);
                mysqli_stmt_close($stmt_delete);
                
                $message = 'Email verified successfully! You can now login.';
                $message_type = 'success';
            } else {
                $message = 'Verification failed. Please try again.';
                $message_type = 'error';
            }
            mysqli_stmt_close($stmt_update);
        }
    } else {
        $message = 'Invalid verification token.';
        $message_type = 'error';
    }
    mysqli_stmt_close($stmt);
} else {
    $message = 'No verification token provided.';
    $message_type = 'error';
}
?>

<div class="bg-white min-h-screen flex items-center justify-center py-20">
    <div class="max-w-xl w-full px-6">
        <div class="bg-[#0A192F] rounded-[2rem] p-10 lg:p-16 text-white shadow-2xl relative overflow-hidden text-center">
            
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-20"></div>
            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-[#0052FF] rounded-full blur-[100px] opacity-10"></div>

            <div class="relative z-10">
                <?php if ($message_type === 'success'): ?>
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 rounded-full border-2 border-white/20 mb-8 backdrop-blur-sm">
                        <i data-lucide="shield-check" class="w-12 h-12 text-[#00FF85]"></i>
                    </div>
                    
                    <h1 class="text-4xl font-black uppercase tracking-tighter mb-4">Email Verified</h1>
                    <p class="text-white/60 font-medium mb-10 tracking-tight leading-relaxed">
                        Your university credentials have been confirmed. Your account is now active and ready for secure trading.
                    </p>
                    
                    <a href="/student2student/auth/login.php" class="inline-block w-full bg-[#0052FF] text-white py-4 rounded-xl font-bold uppercase text-sm tracking-widest hover:bg-[#0041CC] transition-all shadow-lg shadow-blue-500/20">
                        Access Dashboard
                    </a>

                <?php else: ?>
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 rounded-full border-2 border-white/20 mb-8 backdrop-blur-sm">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-red-400"></i>
                    </div>
                    
                    <h1 class="text-4xl font-black uppercase tracking-tighter mb-4">Verification Error</h1>
                    <p class="text-white/60 font-medium mb-10 tracking-tight">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                    
                    <a href="/student2student/auth/register.php" class="inline-block w-full bg-white/10 border border-white/20 text-white py-4 rounded-xl font-bold uppercase text-sm tracking-widest hover:bg-white/20 transition-all">
                        Restart Registration
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>lucide.createIcons();</script>

<?php include '../includes/footer.php'; ?>