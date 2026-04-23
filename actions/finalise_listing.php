<?php
// initialise the session to identify the seller
session_start();
// link to the database connection file
require_once '../config/database.php';

// check if the user is logged in and accessed the page via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../listings/ticket_listing.php");
    exit();
}

// store the logged in user id
$user_id = (int) $_SESSION['user_id'];

// fetch and clean the submitted ticket data from the form
$event_name = trim($_POST['event_name'] ?? '');
$location = trim($_POST['location'] ?? '');
$selling_price = isset($_POST['price']) ? (float) $_POST['price'] : 0.00;
$category = trim($_POST['category'] ?? '');
$raw_date = trim($_POST['event_date'] ?? '');
$retail_price = isset($_POST['retail_price']) ? (float) $_POST['retail_price'] : 0.00;
$event_image = trim($_POST['event_image'] ?? '');

// assign a default category if none was selected
if ($category === '') {
    $category = 'other';
}

// retrieve temporary ticket metadata from the session storage
$scraped_data = $_SESSION['scraped_ticket'] ?? [];
$pdf_hash = $scraped_data['p_hash'] ?? null;

// ensure the retail price is captured from the session if not in the form
if ($retail_price <= 0 && isset($scraped_data['retail_price'])) {
    $retail_price = (float) $scraped_data['retail_price'];
}

// redirect back if critical information is missing
if ($event_name === '' || $location === '') {
    header("Location: ../listings/ticket_review.php?error=missing_fields");
    exit();
}

// format the event date correctly for the database
if (!empty($raw_date)) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $raw_date);
    if ($date_obj) {
        $event_date = $date_obj->format('Y-m-d') . ' 23:59:59';
    } else {
        $event_date = date('Y-m-d 23:59:59');
    }
} else {
    $event_date = !empty($scraped_data['event_date'])
        ? $scraped_data['event_date']
        : date('Y-m-d 23:59:59');
}

// prepare sql to insert the new ticket listing into the database
$sql = "INSERT INTO tickets (
    seller_id,
    event_name,
    event_date,
    event_location,
    category,
    original_price,
    selling_price,
    pdf_hash,
    event_image,
    status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";

$stmt = $conn->prepare($sql);

// log an error and redirect if the query fails to prepare
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: ../listings/ticket_review.php?error=db_prepare_fail");
    exit();
}

// bind the data variables to the sql placeholders
$stmt->bind_param(
    "issssddss",
    $user_id,
    $event_name,
    $event_date,
    $location,
    $category,
    $retail_price,
    $selling_price,
    $pdf_hash,
    $event_image
);

// execute the query and handle the outcome
if ($stmt->execute()) {
    // reward the user with 10 points for successfully listing a ticket
    $updatePoints = "UPDATE users SET points = points + 10 WHERE id = ?";
    $ptsStmt = $conn->prepare($updatePoints);

    if ($ptsStmt) {
        $ptsStmt->bind_param("i", $user_id);
        $ptsStmt->execute();
        $ptsStmt->close();
    }

    // clear the temporary listing data and redirect to the index
    $stmt->close();
    unset($_SESSION['scraped_ticket']);
    header("Location: ../index.php?status=published");
    exit();
} else {
    // log any database execution errors
    error_log("Database Execute Error: " . $stmt->error);
    $stmt->close();
    header("Location: ../listings/ticket_review.php?error=db_fail");
    exit();
}
?>