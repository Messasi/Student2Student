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

<div class="mx-auto px-6 lg:px-[60px] py-12">
    <div class="max-w-md mx-auto text-center">
        
        <?php if ($message_type === 'success'): ?>
            <div class="bg-green-50 border border-green-200 rounded-2xl p-12 mb-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-green-800 mb-3">Email Verified!</h1>
                <p class="text-green-700 font-semibold mb-6"><?php echo htmlspecialchars($message); ?></p>
                <a href="/student2student/auth/login.php" class="inline-block bg-[#0052FF] text-white px-8 py-3 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all no-underline">
                    Go to Login
                </a>
            </div>
        <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-12 mb-8">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2.5">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-red-800 mb-3">Verification Failed</h1>
                <p class="text-red-700 font-semibold mb-6"><?php echo htmlspecialchars($message); ?></p>
                <a href="/student2student/auth/register.php" class="inline-block bg-[#0052FF] text-white px-8 py-3 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all no-underline">
                    Register Again
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>