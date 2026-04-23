<?php
// connect to database file
require_once '../config/database.php';
// add header file
include '../includes/header.php';

// fetch security token from url
$token = $_GET['token'] ?? '';
// initialize empty error string
$error = '';
// set success status to false
$success = false;

// check if security token exists
if (empty($token)) {
    $error = "Invalid or missing security token.";
} else {
    // prepare sql to check for valid non expired token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expiry > NOW() LIMIT 1");
    // bind token parameter
    $stmt->bind_param("s", $token);
    // run token validation query
    $stmt->execute();
    // fetch reset result set
    $result = $stmt->get_result();
    $reset_request = $result->fetch_assoc();

    // check if reset request was found
    if (!$reset_request) {
        $error = "This link has expired or is invalid. Please request a new one.";
    }
}

// process form when user submits new password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    // store password inputs
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // check password length
    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } 
    // check if both passwords match
    elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // fetch user email from reset record
        $email = $reset_request['email'];
        // create secure password hash
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // prepare sql to update user record
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        // bind hash and email
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        // run update query
        if ($update_stmt->execute()) {
            // prepare sql to remove used token
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            // bind email for deletion
            $delete_stmt->bind_param("s", $email);
            // run deletion query
            $delete_stmt->execute();
            
            // set success status to true
            $success = true;
        } else {
            // handle database errors
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