<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../listings/ticket_listing.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$event_name = trim($_POST['event_name'] ?? '');
$location = trim($_POST['location'] ?? '');
$selling_price = isset($_POST['price']) ? (float) $_POST['price'] : 0.00;
$category = trim($_POST['category'] ?? '');
$raw_date = trim($_POST['event_date'] ?? '');
$retail_price = isset($_POST['retail_price']) ? (float) $_POST['retail_price'] : 0.00;

// --- ADDED THIS LINE TO CATCH THE IMAGE URL ---
$event_image = trim($_POST['event_image'] ?? '');

if ($category === '') {
    $category = 'other';
}

$scraped_data = $_SESSION['scraped_ticket'] ?? [];
$pdf_hash = $scraped_data['p_hash'] ?? null;

if ($retail_price <= 0 && isset($scraped_data['retail_price'])) {
    $retail_price = (float) $scraped_data['retail_price'];
}

if ($event_name === '' || $location === '') {
    header("Location: ../listings/ticket_review.php?error=missing_fields");
    exit();
}

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

// --- UPDATED SQL TO INCLUDE event_image ---
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

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: ../listings/ticket_review.php?error=db_prepare_fail");
    exit();
}

// --- UPDATED BIND_PARAM: Added 's' for the image URL ---
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

if ($stmt->execute()) {
    $updatePoints = "UPDATE users SET points = points + 10 WHERE id = ?";
    $ptsStmt = $conn->prepare($updatePoints);

    if ($ptsStmt) {
        $ptsStmt->bind_param("i", $user_id);
        $ptsStmt->execute();
        $ptsStmt->close();
    }

    $stmt->close();
    unset($_SESSION['scraped_ticket']);
    header("Location: ../index.php?status=published");
    exit();
} else {
    error_log("Database Execute Error: " . $stmt->error);
    $stmt->close();
    header("Location: ../listings/ticket_review.php?error=db_fail");
    exit();
}
?>