<?php
// success.php
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// 1. THE "BRAIN" LOGIC - Runs before any HTML is shown
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    
    $ticket_id = (int)$_POST['ticket_id'];
    $buyer_id = $_SESSION['user_id'] ?? null;

    if (!$buyer_id) {
        die("You must be logged in to complete a purchase.");
    }

    // Fetch ticket, seller, and buyer email
    $query = "SELECT t.*, s.id AS seller_id, s.username AS seller_name, b.personal_email AS buyer_email, b.first_name AS buyer_name 
              FROM tickets t 
              JOIN users s ON t.seller_id = s.id 
              JOIN users b ON b.id = ? 
              WHERE t.id = ? AND t.status = 'active' LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $buyer_id, $ticket_id);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();

    if ($ticket) {
        // Update Ticket to Sold
        $update = $conn->prepare("UPDATE tickets SET status = 'sold', buyer_id = ? WHERE id = ?");
        $update->bind_param("ii", $buyer_id, $ticket_id);
        
        if ($update->execute()) {
            // Reward Seller with 20 points
            $points_update = $conn->prepare("UPDATE users SET points = points + 20 WHERE id = ?");
            $points_update->bind_param("i", $ticket['seller_id']);
            $points_update->execute();

            // Send Email with Attachment
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
                $mail->Body    = "<h1>Order Confirmed!</h1><p>Hi {$ticket['buyer_name']}, your ticket for <b>{$ticket['event_name']}</b> is attached.</p>";

                // Check for the file (adjust path if your hash doesn't have .pdf already)
                $filePath = "../uploads/tickets/" . $ticket['pdf_hash'] . ".pdf";
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath, "Ticket_" . $ticket['event_name'] . ".pdf");
                }
                
                $mail->send();
            } catch (Exception $e) {
                // Email failed but DB is updated, we continue to show the success page
            }
        }
    }
}

// 2. THE VISUAL PAGE - Shows after the logic is finished
include '../includes/header.php';
?>

<div class="bg-white min-h-screen font-sans flex items-center justify-center">
    <div class="mx-auto px-6 py-12 max-w-2xl text-center">
        <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 border border-green-100">
            <i data-lucide="check" class="w-12 h-12"></i>
        </div>

        <h1 class="text-5xl font-extrabold text-[#0A192F] tracking-tight mb-6 uppercase">Order Confirmed!</h1>
        <div class="h-1.5 w-20 bg-[#0052FF] mx-auto rounded-full mb-10"></div>

        <p class="text-lg text-[#64748B] font-medium mb-12 leading-relaxed">
            Success! The ticket has been added to your dashboard and sent to your 
            <span class="text-[#0A192F] font-bold">personal email address</span>.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="../index.php" class="bg-[#0A192F] text-white px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-[#1a2e4d] transition-all no-underline">Back to Browse</a>
            <a href="/student2student/dashboard/dashboard.php" class="bg-white border-2 border-[#E2E8F0] text-[#0A192F] px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:border-[#0052FF] transition-all no-underline">View My Tickets</a>
        </div>

        
    </div>
</div>

<script>lucide.createIcons();</script>
<?php include '../includes/footer.php'; ?>