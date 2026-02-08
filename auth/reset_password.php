<?php
require_once '../config/database.php';
include '../includes/header.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = false;

// 1. Validate the token immediately
if (empty($token)) {
    $error = "Invalid or missing security token.";
} else {
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expiry > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset_request = $result->fetch_assoc();

    if (!$reset_request) {
        $error = "This link has expired or is invalid. Please request a new one.";
    }
}

// 2. Process the password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $email = $reset_request['email'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        if ($update_stmt->execute()) {
            // Delete the token so it cannot be used again
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete_stmt->bind_param("s", $email);
            $delete_stmt->execute();
            
            $success = true;
        } else {
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<div class="mx-auto px-6 py-12">
    <div class="max-w-md mx-auto bg-white rounded-2xl border border-[#E2E8F0] p-8">
        <h2 class="text-2xl font-bold text-[#0A192F] mb-6 text-center">Create New Password</h2>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl mb-6 font-semibold">
                Your password has been reset successfully!
            </div>
            <a href="login.php" class="block w-full bg-[#0052FF] text-white text-center py-4 rounded-xl font-bold">
                Go to Login
            </a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-800 p-3 rounded-lg mb-4 text-sm font-bold border border-red-200">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!$error || ($_SERVER['REQUEST_METHOD'] === 'POST' && $error)): ?>
                <form method="POST">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-[#0A192F] mb-2">New Password</label>
                        <input type="password" name="password" required placeholder="At least 8 characters"
                            class="w-full px-4 py-3 bg-[#F4F7FA] border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0052FF] transition-all" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-[#0A192F] mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" required placeholder="Repeat password"
                            class="w-full px-4 py-3 bg-[#F4F7FA] border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0052FF] transition-all" />
                    </div>
                    <button type="submit" class="w-full bg-[#0052FF] text-white py-4 rounded-xl font-bold hover:bg-[#0041CC] transition-all">
                        Update Password
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>