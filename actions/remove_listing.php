<?php
session_start();
require_once '../config/database.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ticket_id = (int)$_GET['id'];

// 2. Ownership & Status Check
// We only allow removal if the ticket belongs to the user AND is still 'active'
$check = $conn->prepare("SELECT id FROM tickets WHERE id = ? AND seller_id = ? AND status = 'active'");
$check->bind_param("ii", $ticket_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // 3. Perform the Removal
    // You can either DELETE the row or set status to 'cancelled'
    // Deleting is cleaner for your specific dashboard setup
    $delete = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $delete->bind_param("i", $ticket_id);
    
    if ($delete->execute()) {
        header("Location: ../dashboard/dashboard.php?msg=removed");
    } else {
        header("Location: ../dashboard/dashboard.php?error=fail");
    }
} else {
    // Attempted to delete a ticket they don't own or that is already sold
    header("Location: ../dashboard/dashboard.php?error=unauthorized");
}
exit();