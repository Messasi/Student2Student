<?php
session_start();
require_once '../config/database.php';

//Get user ID from session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    //Validation
    if (empty($current_password) || empty($new_password)) {
        header("Location: settings.php?error=empty_fields#security");
        exit;
    }

    //Fetch the current hash from DB
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($current_password, $user['password_hash'])) {

        //if the current password matches, hash the new one
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_hash, $user_id);
        
        if ($update_stmt->execute()) { //success
            header("Location: settings.php?success=password#security");
        } else {//DB error
            header("Location: settings.php?error=db_error#security");
        }
    } else {
        // Current password was wrong
        header("Location: settings.php?error=wrong_pass#security");
    }
    exit;
}