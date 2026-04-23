<?php
// initialise the user session for authentication
session_start();
// link to the database configuration file
require_once '../config/database.php';


// the user is logged in and a ticket identifier is provided in the url
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

// store the user identifier and sanitise the ticket identifier
$user_id = $_SESSION['user_id'];
$ticket_id = (int)$_GET['id'];

//
// verify the ticket belongs to the user and is still listed as 'active'
$check = $conn->prepare("SELECT id FROM tickets WHERE id = ? AND seller_id = ? AND status = 'active'");
$check->bind_param("ii", $ticket_id, $user_id);
$check->execute();
$result = $check->get_result();

// check if a matching record was found
if ($result->num_rows > 0) {
   
    // remove the record from the database to clean up the user dashboard
    $delete = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $delete->bind_param("i", $ticket_id);
    
    // redirect to the dashboard with a success or failure message
    if ($delete->execute()) {
        header("Location: ../dashboard/dashboard.php?msg=removed");
    } else {
        header("Location: ../dashboard/dashboard.php?error=fail");
    }
} else {
    // redirect if the user attempted to delete a ticket they do not own or one that is already sold
    header("Location: ../dashboard/dashboard.php?error=unauthorized");
}


exit();