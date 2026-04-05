<?php

session_start();

require_once '../config/database.php';

// Check if the user is logged in and actually clicked the submit button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    
    // Store the logged in student ID in a simple variable
    $user_id = $_SESSION['user_id'];
    
    // Get the event name that the student typed in the form
    $event_name = $_POST['event_name'];
    // Get the building or club location from the form input
    $location = $_POST['location'];
    // Get the price the student wants to sell the ticket for
    $selling_price = $_POST['price'];
    
    // Pull the verified data that was saved in the session earlier
    $scraped_data = $_SESSION['scraped_ticket'] ?? [];
    // Get the unique code for the ticket pdf to prevent duplicates
    $pdf_hash = $scraped_data['p_hash'] ?? null;
    // Get the original shop price of the ticket from the scraper
    $retail_price = $scraped_data['retail_price'] ?? 0.00;
    // Get the official date of the event from the verified data
    $event_date = $scraped_data['event_date'] ?? date('Y-m-d H:i:s');
    
    // Set a default category like Other if the user forgot to pick one
    $category = $_POST['category'] ?? 'Other';

    // Create the sql query to add a new row to the tickets table
    $sql = "INSERT INTO tickets (
                seller_id, 
                event_name, 
                event_date, 
                event_location, 
                category, 
                original_price, 
                selling_price, 
                pdf_hash, 
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";

    // Prepare the sql statement to keep the database secure from hackers
    $stmt = $conn->prepare($sql);
    // Link all the student and ticket variables to the sql query
    $stmt->bind_param(
        "issssdds", 
        $user_id, 
        $event_name, 
        $event_date, 
        $location, 
        $category, 
        $retail_price, 
        $selling_price, 
        $pdf_hash
    );

    // Run the query and check if the ticket was saved successfully
    if ($stmt->execute()) {
        // Create an update query to give the student trust points
        $updatePoints = "UPDATE users SET points = points + 10 WHERE id = ?";
        // Prepare the points update statement for the database
        $ptsStmt = $conn->prepare($updatePoints);
        // Link the user id to the points update query
        $ptsStmt->bind_param("i", $user_id);
        // Run the query to officially award the 10 trust points
        $ptsStmt->execute();

        // Clear the temporary ticket data from the session memory
        unset($_SESSION['scraped_ticket']);

        // Send the student back to the home page with a success message
        header("Location: ../index.php?status=published");
        // Stop the script from running any further
        exit();
    } else {
        // Save the error message in a log file if the database fails
        error_log("Database Error: " . $conn->error);
        // Send the user back to the review page to try again
        header("Location: ../listings/ticket_review.php?error=db_fail");
        // Stop the script from running any further
        exit();
    }
} else {
    // Kick the user back to the start if they try to skip steps
    header("Location: ../listings/ticket_listing.php");
    // Stop the script from running any further
    exit();
}