<?php
// 1. Error Reporting (Crucial for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $buyer_id = $_SESSION['user_id'];

    if ($ticket_id === 0) {
        die("Error: Invalid Ticket ID received.");
    }

    // 2. Fetch Ticket, Seller, and Buyer Personal Email
    $query = "SELECT t.*, s.id AS seller_id, s.username AS seller_name, b.personal_email AS buyer_email, b.first_name AS buyer_name 
              FROM tickets t 
              JOIN users s ON t.seller_id = s.id 
              JOIN users b ON b.id = ? 
              WHERE t.id = ? AND t.status = 'active' LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $buyer_id, $ticket_id);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();

    if (!$ticket) {
        // If the query fails, let's see if the ticket exists at all
        die("Error: Ticket not found, already sold, or buyer email is missing in database.");
    }

    // 3. Database Update: Mark as Sold
    $update = $conn->prepare("UPDATE tickets SET status = 'sold', buyer_id = ? WHERE id = ?");
    $update->bind_param("ii", $buyer_id, $ticket_id);
    
    if ($update->execute()) {
        
        // 4. Reward Seller
        $points_update = $conn->prepare("UPDATE users SET points = points + 20 WHERE id = ?");
        $points_update->bind_param("i", $ticket['seller_id']);
        $points_update->execute();

        // 5. Send Email
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
            $mail->Subject = 'Confirmed: ' . $ticket['event_name'] . ' Ticket';
            $mail->Body    = "<h2>Order Successful!</h2><p>Hi {$ticket['buyer_name']}, your ticket for <b>{$ticket['event_name']}</b> is attached.</p>";

            // PATH FIX: Go up one level then into uploads
            $filePath = "../uploads/tickets/" . $ticket['pdf_hash']; 
            
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath, "Ticket_" . $ticket['event_name'] . ".pdf");
            }

            $mail->send();
            
            // REDIRECT FIX: Go up one level then into pages
            header("Location: ../pages/success.php");
            exit;

        } catch (Exception $e) {
            // Even if mail fails, redirect but pass an error code
            header("Location: ../pages/success.php?mail_error=1");
            exit;
        }
    } else {
        die("Database Error: Could not update ticket status.");
    }
} else {
    die("Error: Invalid Request Method.");
}