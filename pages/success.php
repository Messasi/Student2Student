<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust these paths based on where your PHPMailer folder is located
require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['ticket_id'])) {
    header("Location: ../index.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$ticket_id = (int)$_POST['ticket_id'];

// 1. Fetch ticket details + buyer details (for the email)
$ticket_query = "SELECT t.*, u.email as buyer_email, u.username as buyer_name 
                 FROM tickets t 
                 JOIN users u ON u.id = ? 
                 WHERE t.id = ? AND t.status = 'active' LIMIT 1";
$stmt = $conn->prepare($ticket_query);
$stmt->bind_param("ii", $buyer_id, $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    die("Error: Ticket is no longer available or already sold.");
}

// 2. START DATABASE TRANSACTION
$conn->begin_transaction();

try {
    // A. Insert into the 'orders' table
    $order_sql = "INSERT INTO orders (ticket_id, buyer_id, seller_id, event_name, price) VALUES (?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("iiisd", 
        $ticket_id, 
        $buyer_id, 
        $ticket['seller_id'], 
        $ticket['event_name'], 
        $ticket['selling_price']
    );
    $order_stmt->execute();

    // B. Update ticket status to 'sold'
    // B. Update ticket status to 'sold' AND assign the buyer_id
// This is the link that allows the download_ticket.php script to work!
    $update_sql = "UPDATE tickets SET status = 'sold', buyer_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $buyer_id, $ticket_id);
    $update_stmt->execute();

    // C. Reward Seller with 20 points
    $points_update = $conn->prepare("UPDATE users SET points = points + 20 WHERE id = ?");
    $points_update->bind_param("i", $ticket['seller_id']);
    $points_update->execute();

    // Commit database changes
    $conn->commit();

    // 3. SEND EMAIL (After DB is safe)
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'leonnupa8@gmail.com';
        $mail->Password   = 'obtefwnbeelihkjg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('leonnupa8@gmail.com', 'Student2Student');
        $mail->addAddress($ticket['buyer_email']); 

        $mail->isHTML(true);
        $mail->Subject = 'Ticket Confirmed: ' . $ticket['event_name'];
        $mail->Body    = "
            <div style='font-family: sans-serif; color: #0A192F;'>
                <h1 style='color: #0052FF;'>Order Confirmed!</h1>
                <p>Hi " . htmlspecialchars($ticket['buyer_name']) . ",</p>
                <p>Your ticket for <b>" . htmlspecialchars($ticket['event_name']) . "</b> is attached to this email.</p>
                <p>You can also access it anytime in your dashboard.</p>
            </div>";

        // Attachment logic
        $filePath = "../uploads/tickets/" . $ticket['pdf_hash']; 
        // Note: If your database hash doesn't include .pdf, add it here: $filePath .= ".pdf";
        
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath, "Ticket_" . $ticket['event_name'] . ".pdf");
        }
        
        $mail->send();
    } catch (Exception $e) {
        // Log error but don't stop the user; the purchase is already done in the DB
    }

} catch (Exception $e) {
    $conn->rollback();
    die("Purchase failed: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="bg-white min-h-screen font-sans flex items-center justify-center">
    <div class="max-w-xl w-full px-6 text-center">
        <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg shadow-green-100/50">
            <i data-lucide="check-circle-2" class="w-12 h-12"></i>
        </div>

        <h1 class="text-5xl font-black text-[#0A192F] tracking-tighter uppercase mb-4">
            Payment <br> Successful
        </h1>
        
        <p class="text-[#64748B] font-bold uppercase text-xs tracking-[0.2em] mb-4">
            Order #<?php echo str_pad($ticket_id, 6, '0', STR_PAD_LEFT); ?> • Confirmed
        </p>

        <p class="text-[#10B981] font-bold uppercase text-[10px] tracking-widest mb-12">
            A confirmation email has been sent to <?php echo htmlspecialchars($ticket['buyer_email']); ?>
        </p>

        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-[2rem] p-8 mb-10 text-left">
            <p class="text-[10px] font-black text-[#94A3B8] uppercase tracking-widest mb-2">Item Secured</p>
            <h3 class="text-xl font-black text-[#0A192F] uppercase mb-1"><?php echo htmlspecialchars($ticket['event_name']); ?></h3>
            <p class="text-sm font-bold text-[#64748B]"><?php echo htmlspecialchars($ticket['event_location']); ?></p>
        </div>

        <div class="flex flex-col gap-4">
            <a href="../dashboard/dashboard.php" class="w-full bg-[#0A192F] text-white py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#0052FF] transition-all no-underline">
                Go to Financial Hub
            </a>
            <a href="../index.php" class="w-full bg-transparent text-[#64748B] py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:text-[#0A192F] transition-all no-underline">
                Back to Discovery
            </a>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php include '../includes/footer.php'; ?>