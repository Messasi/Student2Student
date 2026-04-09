<?php
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['ticket_id'])) {
    header("Location: ../index.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$ticket_id = (int)$_POST['ticket_id'];

// 1. Fetch ticket details + buyer details
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
    // A. Insert into the 'orders' table (MATCHES NEW SCHEMA)
    // We add 'held' explicitly to ensure escrow phase starts
    $order_sql = "INSERT INTO orders (ticket_id, buyer_id, seller_id, event_name, price, status) VALUES (?, ?, ?, ?, ?, 'held')";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("iiisd", 
        $ticket_id, 
        $buyer_id, 
        $ticket['seller_id'], 
        $ticket['event_name'], 
        $ticket['selling_price']
    );
    $order_stmt->execute();
    $new_order_id = $conn->insert_id; // Get the actual Order ID for the receipt

    // B. Update ticket status to 'sold' and assign buyer
    $update_sql = "UPDATE tickets SET status = 'sold', buyer_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $buyer_id, $ticket_id);
    $update_stmt->execute();

    // REMOVED: Point rewarding is now handled in the dashboard confirm/auto-complete phase

    $conn->commit();

    // 3. SEND EMAIL
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
        $mail->Subject = 'Order Secured: ' . $ticket['event_name'];
        $mail->Body    = "
            <div style='font-family: sans-serif; color: #0A192F;'>
                <h1 style='color: #0052FF;'>Payment Secured!</h1>
                <p>Hi " . htmlspecialchars($ticket['buyer_name']) . ",</p>
                <p>Your payment for <b>" . htmlspecialchars($ticket['event_name']) . "</b> is held in escrow.</p>
                <p>Your ticket is attached. Access it anytime in your dashboard.</p>
            </div>";

        $filePath = "../uploads/tickets/" . $ticket['pdf_hash']; 
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath, "Ticket_" . $ticket['event_name'] . ".pdf");
        }
        
        $mail->send();
    } catch (Exception $e) { }

} catch (Exception $e) {
    $conn->rollback();
    die("Purchase failed: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="bg-white min-h-screen font-sans flex items-center justify-center">
    <div class="max-w-xl w-full px-6 text-center">

        <h1 class="text-5xl font-black text-[#0A192F] tracking-tighter uppercase mb-4 leading-none">
            Payment <br> Secured
        </h1>
        
        <p class="text-[#64748B] font-bold uppercase text-xs tracking-[0.2em] mb-4">
            Order #<?= str_pad($new_order_id, 6, '0', STR_PAD_LEFT); ?> 
        </p>

        <p class="text-[#64748B] font-bold uppercase text-[10px] tracking-widest mb-12">
            A confirmation email has been sent to <?= htmlspecialchars($ticket['buyer_email']); ?>
        </p>

        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-[2rem] p-8 mb-10 text-left">
            <h3 class="text-xl font-black text-[#0A192F] uppercase mb-1"><?= htmlspecialchars($ticket['event_name']); ?></h3>
            <p class="text-sm font-bold text-[#64748B]"><?= htmlspecialchars($ticket['event_location']); ?></p>
        </div>

        <div class="flex flex-col gap-4">
            <a href="../dashboard/dashboard.php" class="w-full bg-[#0A192F] text-white py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#0052FF] transition-all no-underline">
                Manage in Financial Hub
            </a>
            <a href="../index.php" class="w-full bg-transparent text-[#64748B] py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:text-[#0A192F] transition-all no-underline">
                Back to Discovery
            </a>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
<?php include '../includes/footer.php'; ?>