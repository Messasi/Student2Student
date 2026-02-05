<?php
require_once '../config/database.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /student2student/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Initialize form variables
$event_name = '';
$event_date = '';
$event_location = '';
$ticket_type = '';
$original_price = '';
$selling_price = '';
$quantity = 1;
$description = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $event_name = trim($_POST['event_name']);
    $event_date = trim($_POST['event_date']);
    $event_location = trim($_POST['event_location']);
    $ticket_type = trim($_POST['ticket_type']);
    $original_price = trim($_POST['original_price']);
    $selling_price = trim($_POST['selling_price']);
    $quantity = (int)$_POST['quantity'];
    $description = trim($_POST['description']);
    
    // Validation
    if (empty($event_name)) {
        $errors[] = 'Event name is required';
    } elseif (strlen($event_name) < 3 || strlen($event_name) > 255) {
        $errors[] = 'Event name must be between 3 and 255 characters';
    }
    
    if (empty($event_date)) {
        $errors[] = 'Event date is required';
    } else {
        $event_timestamp = strtotime($event_date);
        if ($event_timestamp === false) {
            $errors[] = 'Invalid event date format';
        } elseif ($event_timestamp < time()) {
            $errors[] = 'Event date must be in the future';
        }
    }
    
    if (empty($event_location)) {
        $errors[] = 'Event location is required';
    } elseif (strlen($event_location) < 3 || strlen($event_location) > 255) {
        $errors[] = 'Event location must be between 3 and 255 characters';
    }
    
    if (empty($original_price)) {
        $errors[] = 'Original price is required';
    } elseif (!is_numeric($original_price) || $original_price <= 0) {
        $errors[] = 'Original price must be a positive number';
    }
    
    if (empty($selling_price)) {
        $errors[] = 'Selling price is required';
    } elseif (!is_numeric($selling_price) || $selling_price <= 0) {
        $errors[] = 'Selling price must be a positive number';
    }
    
    if ($quantity < 1 || $quantity > 10) {
        $errors[] = 'Quantity must be between 1 and 10';
    }
    
    if (!empty($description) && strlen($description) > 1000) {
        $errors[] = 'Description must not exceed 1000 characters';
    }
    
    // Insert ticket if no errors
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tickets (seller_id, event_name, event_date, event_location, ticket_type, original_price, selling_price, quantity, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')");
        mysqli_stmt_bind_param($stmt, "issssddis", $user_id, $event_name, $event_date, $event_location, $ticket_type, $original_price, $selling_price, $quantity, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Ticket listed successfully!';
            
            // Clear form
            $event_name = '';
            $event_date = '';
            $event_location = '';
            $ticket_type = '';
            $original_price = '';
            $selling_price = '';
            $quantity = 1;
            $description = '';
        } else {
            $errors[] = 'Failed to create listing. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}

include '../includes/header.php';
?>

<div class="bg-white min-h-screen">
    <div class="mx-auto px-6 lg:px-[60px] py-12">
        <div class="max-w-3xl mx-auto">
            
            <!-- Page Header -->
            <div class="mb-10">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-[#0A192F] mb-2 tracking-tight">
                    List Your Ticket
                </h1>
                <p class="text-[#64748B] font-medium text-lg">Fill in the details to sell your spare tickets</p>
            </div>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-5 rounded-lg font-medium mb-8">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mt-0.5"></i>
                        <div>
                            <p class="font-bold mb-1">Success!</p>
                            <p class="text-sm"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-8">
                    <a href="/student2student/dashboard/index.php" class="text-[#0052FF] font-bold hover:underline inline-flex items-center gap-2">
                        View My Listings <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-5 rounded-lg font-medium mb-8">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                        <div>
                            <p class="font-bold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Listing Form -->
            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-8 shadow-sm">
                <form method="POST" action="" id="createListingForm">
                    
                    <!-- Event Name -->
                    <div class="mb-6">
                        <label for="event_name" class="block text-sm font-bold text-[#0A192F] mb-2">Event Name *</label>
                        <input 
                            type="text" 
                            id="event_name" 
                            name="event_name" 
                            value="<?php echo htmlspecialchars($event_name); ?>"
                            placeholder="e.g. Arctic Monkeys Concert, Football Match, Graduation Ball"
                            required
                            class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                        />
                    </div>

                    <!-- Event Date and Location Grid -->
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <!-- Event Date -->
                        <div>
                            <label for="event_date" class="block text-sm font-bold text-[#0A192F] mb-2">Event Date *</label>
                            <input 
                                type="date" 
                                id="event_date" 
                                name="event_date" 
                                value="<?php echo htmlspecialchars($event_date); ?>"
                                min="<?php echo date('Y-m-d'); ?>"
                                required
                                class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                            />
                        </div>

                        <!-- Event Location -->
                        <div>
                            <label for="event_location" class="block text-sm font-bold text-[#0A192F] mb-2">Event Location *</label>
                            <input 
                                type="text" 
                                id="event_location" 
                                name="event_location" 
                                value="<?php echo htmlspecialchars($event_location); ?>"
                                placeholder="e.g. O2 Arena, Wembley Stadium"
                                required
                                class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                            />
                        </div>
                    </div>

                    <!-- Ticket Type -->
                    <div class="mb-6">
                        <label for="ticket_type" class="block text-sm font-bold text-[#0A192F] mb-2">Ticket Type <span class="text-[#64748B] font-normal text-xs">(Optional)</span></label>
                        <input 
                            type="text" 
                            id="ticket_type" 
                            name="ticket_type" 
                            value="<?php echo htmlspecialchars($ticket_type); ?>"
                            placeholder="e.g. VIP, General Admission, Standing, Seated"
                            class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                        />
                    </div>

                    <!-- Price Grid -->
                    <div class="grid md:grid-cols-3 gap-6 mb-6">
                        <!-- Original Price -->
                        <div>
                            <label for="original_price" class="block text-sm font-bold text-[#0A192F] mb-2">Original Price (£) *</label>
                            <input 
                                type="number" 
                                id="original_price" 
                                name="original_price" 
                                value="<?php echo htmlspecialchars($original_price); ?>"
                                placeholder="50.00"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                            />
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-bold text-[#0A192F] mb-2">Selling Price (£) *</label>
                            <input 
                                type="number" 
                                id="selling_price" 
                                name="selling_price" 
                                value="<?php echo htmlspecialchars($selling_price); ?>"
                                placeholder="45.00"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                            />
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-bold text-[#0A192F] mb-2">Quantity *</label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                value="<?php echo htmlspecialchars($quantity); ?>"
                                min="1"
                                max="10"
                                required
                                class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <label for="description" class="block text-sm font-bold text-[#0A192F] mb-2">Description <span class="text-[#64748B] font-normal text-xs">(Optional)</span></label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5"
                            placeholder="Add any additional details about the tickets (seat numbers, restrictions, etc.)"
                            class="w-full px-4 py-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0A192F] font-medium focus:outline-none focus:border-[#0052FF] focus:bg-white transition-all resize-none"
                        ><?php echo htmlspecialchars($description); ?></textarea>
                        <p class="text-xs text-[#64748B] mt-2 font-medium flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Maximum 1000 characters
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 px-6 py-4 rounded-lg mb-8">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                            <div class="text-sm text-blue-800 font-medium">
                                <p class="font-bold mb-2">Before you list:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Ensure your ticket is genuine and transferable</li>
                                    <li>Price fairly - overpricing may deter buyers</li>
                                    <li>Tickets sold or pending cannot be removed</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-[#0052FF] text-white px-8 py-4 rounded-xl text-base font-bold hover:bg-[#0041CC] transition-all shadow-lg shadow-blue-500/20"
                    >
                        List Ticket for Sale
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>