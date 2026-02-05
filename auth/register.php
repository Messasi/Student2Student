<?php
// 1. Start session and include database FIRST

require_once '../config/database.php';

// Initialise variables
$errors = [];
$success = '';
$username = ''; 
$first_name = '';
$last_name = '';
$email = '';
$personal_email = '';
$grad_year = '';

// 2. Process Form Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? ''); 
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $personal_email = trim($_POST['personal_email'] ?? '');
    $grad_year = trim($_POST['grad_year'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    
    // --- VALIDATION ---
    // Username
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) <= 2) {
        $errors[] = 'Username must be longer than 2 characters';
    } elseif (!preg_match('/^[A-Za-z]+$/', $username)) {
        $errors[] = 'Username can only contain letters';
    }

    // Name
    if (empty($first_name) || empty($last_name) || strlen($first_name) < 2 || strlen($last_name) < 2) {
        $errors[] = 'Full name is required and must be at least 2 characters long';
    }

    // Emails
    if (empty($email)) {
        $errors[] = 'Student email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@(.+\.)?(ac\.uk|edu)$/i', $email)) {
        $errors[] = 'Please use a valid student email address (.ac.uk or .edu)';
    }

    if (empty($personal_email) || !filter_var($personal_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid personal email is required';
    }

    // Grad Year
    if (empty($grad_year)) {
        $errors[] = 'Graduation year is required';
    }

    // Password
    if (empty($password) || strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must be 8+ characters with uppercase, lowercase, and numbers';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    if (!$terms) {
        $errors[] = 'You must agree to the terms and conditions';
    }

    // --- DATABASE INSERTION (MySQLi / $conn Style) ---
  // --- DATABASE INSERTION (MySQLi / $conn Style) ---
    if (empty($errors)) {
        if (!isset($conn)) {
            $errors[] = "System Error: Database connection variable '\$conn' is missing.";
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, first_name, last_name, email, personal_email, grad_year, password_hash, is_verified, is_admin) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0)";

                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("sssssss", 
                        $username, 
                        $first_name, 
                        $last_name, 
                        $email, 
                        $personal_email, 
                        $grad_year, 
                        $hashed_password
                    );

                    $stmt->execute();

                    // Success! 
                    $user_id = $conn->insert_id;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['first_name'] = $first_name;

                    header("Location: /student2student/dashboard/index.php");
                    exit;
                }
            } catch (mysqli_sql_exception $e) {
                // Check if the error code is 1062 (Duplicate Entry)
                if ($e->getCode() === 1062) {
                    $errors[] = 'This email or username is already registered. Please try logging in.';
                } else {
                    $errors[] = 'Database Error: ' . $e->getMessage();
                }
            }
        }
    }
}

// 3. Include Header at the end
include '../includes/header.php';
?>

<div class="bg-white min-h-screen">
    <div class="mx-auto px-6 lg:px-[60px] py-16">
        <div class="max-w-2xl mx-auto">
            
            <div class="text-center mb-10">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-[#0A192F] mb-3 tracking-tight">Create an Account</h1>
                <p class="text-[#64748B] font-medium text-lg">Join the Student2Student marketplace</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-8">
                    <ul class="list-disc list-inside text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-8 shadow-sm">
                <form method="POST" action="" id="registerForm" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="user" class="w-4 h-4"></i> First Name
                            </label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                        <div>
                            <label for="last_name" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="user" class="w-4 h-4"></i> Last Name
                            </label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="graduation-cap" class="w-4 h-4"></i> Student Email
                            </label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                        <div>
                            <label for="personal_email" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="mail" class="w-4 h-4"></i> Personal Email
                            </label>
                            <input type="email" id="personal_email" name="personal_email" value="<?php echo htmlspecialchars($personal_email); ?>" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="grad_year" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                            <i data-lucide="calendar" class="w-4 h-4"></i> Graduation Year
                        </label>
                        <select id="grad_year" name="grad_year" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none appearance-none">
                            <option value="">Select Year</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                        </select>
                    </div>

                    <div>
                        <label for="username" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                            <i data-lucide="at-sign" class="w-4 h-4"></i> Username (Letters only)
                        </label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="lock" class="w-4 h-4"></i> Password
                            </label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                        <div>
                            <label for="confirm_password" class="flex items-center gap-2 text-sm font-bold text-[#0A192F] mb-2">
                                <i data-lucide="shield-check" class="w-4 h-4"></i> Confirm Password
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl focus:border-[#0052FF] outline-none">
                        </div>
                    </div>

                    <div class="flex items-start gap-3 py-2">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1 w-4 h-4 text-[#0052FF] rounded border-gray-300">
                        <label for="terms" class="text-sm text-[#64748B] font-medium">
                            I agree to the <a href="#" class="text-[#0052FF] hover:underline">Terms and Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-[#0052FF] text-white px-8 py-4 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all shadow-lg shadow-blue-500/20">
                        Create Account
                    </button>
                </form>

                <div class="text-center mt-8 pt-6 border-t border-[#E2E8F0]">
                    <p class="text-[#64748B] font-medium">
                        Already have an account? <a href="/student2student/auth/login.php" class="text-[#0052FF] font-bold hover:underline">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>