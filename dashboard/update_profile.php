<?php
// start session to keep track of logged in user
session_start();
// link to the database connection file
require_once '../config/database.php';


// check if a user is logged in and if they submitted the form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // store the id of the person logged in
    $user_id = $_SESSION['user_id'];
    // get the new name from the form and clean up extra spaces
    $username = trim($_POST['username'] ?? '');

    // check if a new name was provided
    if (!empty($username)) {
        try {
            // prepare sql to change the username in the table
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            // bind the new name and id to the query
            $stmt->bind_param("si", $username, $user_id);
            // run the update on the database
            $stmt->execute();
            // update the session variable so the new name shows up immediately
            $_SESSION['username'] = $username; 
        } catch (Exception $e) {
            // go back to settings with an error if the name is already used
            header("Location: settings.php?error=username_taken");
            exit;
        }
    }

    // check if an image file was uploaded without errors
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        // store the file information
        $file = $_FILES['profile_picture'];
        // list of image formats allowed on the site
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        // check if the file format matches our allowed list
        if (!in_array($file['type'], $allowed_types)) {
            header("Location: settings.php?error=invalid_type");
            exit;
        }

        // check if the file size is bigger than two megabytes
        if ($file['size'] > 2 * 1024 * 1024) {
            header("Location: settings.php?error=too_large");
            exit;
        }

        // get the file extension from the original name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // create a unique name using the user id and current time
        $new_name = "profile_" . $user_id . "_" . time() . "." . $ext;
        
        // set the folder where the profile pictures go
        $upload_dir = '../uploads/profiles/';
        
        // check if the folder exists and create it if not
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // try to move the uploaded file from temporary storage to our folder
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
            // prepare sql to save the new filename in the user table
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            // bind the filename and id to the query
            $stmt->bind_param("si", $new_name, $user_id);
            // run the update query
            $stmt->execute();
        }
    }

    // go back to settings and show a success message
    header("Location: settings.php?success=1");
    exit;
} else {
    // redirect to settings if the page was accessed incorrectly
    header("Location: settings.php");
    exit;
}