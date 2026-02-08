<?php
require_once '../config/database.php';
include '../includes/header.php';
require_once '../vendor/autoload.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Note: Ensure you have your vendor/autoload.php or manual PHPMailer requires here
// require '../vendor/autoload.php'; 

$display_message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT email, first_name FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 2. Store token in database
        $token_stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
        $token_stmt->bind_param("sss", $email, $token, $expiry);
        
        if ($token_stmt->execute()) {
            try {
                
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'leonnupa8@gmail.com';
                $mail->Password   = 'obtefwnbeelihkjg'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('leonnupa8@gmail.com', 'Student2Student');
                $mail->addAddress($email);

                $reset_link = "http://localhost/student2student/auth/reset_password.php?token=$token";

                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Student2Student Password';
                $mail->Body    = "
                    <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
                        <h2 style='color: #0A192F;'>Password Reset Request</h2>
                        <p style='color: #64748B;'>Hi " . htmlspecialchars($user['first_name']) . ",</p>
                        <p style='color: #64748B;'>We received a request to reset your password. Click the button below to choose a new one. This link expires in 1 hour.</p>
                        <a href='$reset_link' 
                           style='display: inline-block; background: #0052FF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px;'>
                           Reset Password
                        </a>
                        <p style='font-size: 12px; color: #94a3b8; margin-top: 30px;'>If you did not request this, please ignore this email.</p>
                    </div>";

                $mail->send();

                // Mask the email for the success message
                $parts = explode("@", $email);
                $name = $parts[0];
                $domain = $parts[1];
                $masked_email = substr($name, 0, 1) . str_repeat('*', max(0, strlen($name) - 2)) . substr($name, -1) . '@' . $domain;

                $display_message = "A password reset link was sent to: <strong>" . htmlspecialchars($masked_email) . "</strong>";

            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } else {
        // Generic message for security
        $display_message = "If an account exists with that email, a reset link has been sent.";
    }
}
?>

<div class="mx-auto px-6 py-12">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-[#0A192F] mb-3">Reset Password</h1>
        </div>

        <?php if ($display_message): ?>
            <div class="bg-blue-50 border border-blue-200 text-[#0052FF] px-6 py-4 rounded-xl font-semibold mb-6 text-center">
                <?php echo $display_message; ?>
            </div>
            <div class="text-center">
                <a href="login.php" class="text-[#0052FF] font-bold hover:underline">Back to Login</a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-800 p-3 rounded-lg mb-4 text-sm font-bold">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-[#0A192F] mb-2">University Email Address</label>
                        <input type="email" name="email" required placeholder="name@university.ac.uk"
                            class="w-full px-4 py-3 bg-[#F4F7FA] border border-[#E2E8F0] rounded-xl focus:outline-none focus:border-[#0052FF] transition-all"
                        />
                    </div>
                    <button type="submit" class="w-full bg-[#0052FF] text-white py-4 rounded-xl font-bold hover:bg-[#0041CC] transition-all">
                        Send Reset Link
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>