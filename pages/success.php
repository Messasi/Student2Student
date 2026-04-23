<?php
// load external library files
require_once '../vendor/autoload.php';

// use classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// start user session storage
session_start();
// connect to database file
require_once '../config/database.php';

// check for user id and ticket id
if (!isset($_SESSION['user_id']) || !isset($_POST['ticket_id'])) {
    header("Location: ../index.php");
    exit();
}

// store buyer and ticket identifiers
$buyer_id = $_SESSION['user_id'];
$ticket_id = (int)$_POST['ticket_id'];

// prepare sql to fetch ticket and buyer info
$ticket_query = "SELECT t.*, u.email as buyer_email, u.username as buyer_name 
                 FROM tickets t 
                 JOIN users u ON u.id = ? 
                 WHERE t.id = ? AND t.status = 'active' LIMIT 1";
$stmt = $conn->prepare($ticket_query);
// bind buyer and ticket identifiers
$stmt->bind_param("ii", $buyer_id, $ticket_id);
// run ticket lookup query
$stmt->execute();
// store ticket result array
$ticket = $stmt->get_result()->fetch_assoc();

// stop if ticket is unavailable
if (!$ticket) {
    die("Error: Ticket is no longer available or already sold.");
}

// open database transaction group
$conn->begin_transaction();

try {
    // prepare sql to create new order
    $order_sql = "INSERT INTO orders (ticket_id, buyer_id, seller_id, event_name, price, status) VALUES (?, ?, ?, ?, ?, 'held')";
    $order_stmt = $conn->prepare($order_sql);
    // bind order detail parameters
    $order_stmt->bind_param("iiisd", 
        $ticket_id, 
        $buyer_id, 
        $ticket['seller_id'], 
        $ticket['event_name'], 
        $ticket['selling_price']
    );
    // run order insertion
    $order_stmt->execute();
    // get unique order id
    $new_order_id = $conn->insert_id;

    // prepare sql to update ticket status
    $update_sql = "UPDATE tickets SET status = 'sold', buyer_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    // bind buyer and ticket id
    $update_stmt->bind_param("ii", $buyer_id, $ticket_id);
    // run status update
    $update_stmt->execute();

    // save all database changes
    $conn->commit();

    // setup email library instance
    $mail = new PHPMailer(true);
    try {
        // configure mail server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'leonnupa8@gmail.com';
        $mail->Password   = 'obtefwnbeelihkjg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // set sender and recipient
        $mail->setFrom('leonnupa8@gmail.com', 'Student2Student');
        $mail->addAddress($ticket['buyer_email']); 

        // configure email content format
        $mail->isHTML(true);
        $mail->Subject = 'Order Secured: ' . $ticket['event_name'];
        $mail->Body    = "
            <div style='font-family: sans-serif; color: #0A192F;'>
                <h1 style='color: #0052FF;'>Payment Secured!</h1>
                <p>Hi " . htmlspecialchars($ticket['buyer_name']) . ",</p>
                <p>Your payment for <b>" . htmlspecialchars($ticket['event_name']) . "</b> is held in escrow.</p>
                <p>Your ticket is attached. Access it anytime in your dashboard.</p>
            </div>";

        // check for ticket file path
        $filePath = "../uploads/tickets/" . $ticket['pdf_hash']; 
        if (file_exists($filePath)) {
            // attach ticket file to email
            $mail->addAttachment($filePath, "Ticket_" . $ticket['event_name'] . ".pdf");
        }
        
        // run email delivery
        $mail->send();
    } catch (Exception $e) { }

} catch (Exception $e) {
    // undo database changes on error
    $conn->rollback();
    die("Purchase failed: " . $e->getMessage());
}

// add header navigation
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