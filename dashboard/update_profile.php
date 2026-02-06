<?php
session_start();
require_once '../config/database.php';

// Enable error reporting for debugging - remove this once fixed
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');

    // 1. HANDLE USERNAME UPDATE
    if (!empty($username)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $username, $user_id);
            $stmt->execute();
            $_SESSION['username'] = $username; // Update session
        } catch (Exception $e) {
            header("Location: settings.php?error=username_taken");
            exit;
        }
    }

    // 2. HANDLE IMAGE UPLOAD
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        // Check file type
        if (!in_array($file['type'], $allowed_types)) {
            header("Location: settings.php?error=invalid_type");
            exit;
        }

        // Check size (2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            header("Location: settings.php?error=too_large");
            exit;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_name = "profile_" . $user_id . "_" . time() . "." . $ext;
        
        // Define path - ensure this folder exists!
        $upload_dir = '../uploads/profiles/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $new_name, $user_id);
            $stmt->execute();
        }
    }

    // Success!
    header("Location: settings.php?success=1");
    exit;
} else {
    header("Location: settings.php");
    exit;
}