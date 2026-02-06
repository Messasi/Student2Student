<?php

require_once '../config/database.php';
include '../includes/header.php';
// Redirect if already logged i
if (isset($_SESSION['user_id'])) {
    header('Location: /student2student/index.php');
    exit;
}




$errors = [];
$login_input = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['login_input'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login_input)) {
        $errors[] = 'Username or Email is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        try {
            //query to fetch the data
            $sql = "SELECT id, username, email, password_hash, first_name, last_name, is_verified, is_admin 
                    FROM users 
                    WHERE email = ? OR username = ? 
                    LIMIT 1";
            
            $stmt = $conn->prepare($sql);
            
            // Check if prepare() succeeded
            if ($stmt) {
                $stmt->bind_param("ss", $login_input, $login_input);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if ($user) {
                    if (password_verify($password, $user['password_hash'])) {
                        if (!$user['is_verified']) {
                            $errors[] = 'Please verify your email before logging in';
                        } else {
                            // Set session variables matching your DB fields
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['first_name'] = $user['first_name'];
                            $_SESSION['last_name'] = $user['last_name'];
                            $_SESSION['is_admin'] = $user['is_admin'];
                            
                            // Redirect to the correct file name
                            header('Location: /student2student/index.php');
                            exit;
                        }
                    } else {
                        $errors[] = 'Invalid login credentials';
                    }
                } else {
                    $errors[] = 'Invalid login credentials';
                }
                $stmt->close();
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}


?>
<div class=" mx-auto px-6 lg:px-[60px] py-12">
    <div class="max-w-md mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-[#0A192F] mb-3">Welcome Back</h1>
            <p class="text-[#64748B] font-medium">Login to your Student2Student account</p>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl font-semibold mb-6">
                <ul class="list-disc list-inside space-y-1 m-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <div class="bg-white rounded-2xl border border-[#E2E8F0] p-8">
            <form method="POST" action="" id="loginForm">
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="login_input" class="block text-sm font-bold text-[#0A192F] mb-2">University Email Address or Username</label>
                    <input type="text" id="login_input" name="login_input" value="<?php echo htmlspecialchars($login_input); ?>" placeholder="Username or University Email" required
                        class="w-full px-4 py-3 bg-[#F4F7FA] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                    />
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-[#0A192F] mb-2">Password</label>
                    <input type="password" id="password"  name="password"  placeholder="*********" required
                        class="w-full px-4 py-3 bg-[#F4F7FA] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                    />
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-[#0052FF] text-white px-8 py-4 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all shadow-lg hover:shadow-xl mb-4"
                >
                    Login
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="text-[#64748B] font-medium">
                    Don't have an account? 
                    <a href="/student2student/auth/register.php" class="text-[#0052FF] font-bold hover:underline">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>