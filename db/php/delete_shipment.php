<?php
include 'functions.php'; 

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Get the ID from the POST request and ensure it's an integer

    // Establish a connection to the database
    $conn = $db; 

    // Create the SQL DELETE query
    $sql = "DELETE FROM info WHERE id = $id";

    // Execute the query
    if (mysqli_query($conn, $sql)) {

        header("Location: ../admin.php");
        exit();
    } else {
        // If there was an error, redirect back with an error message
        header("Location: ../admin.php");
        exit();
    }

    // Close the connection
    mysqli_close($conn);
} else {
  
    header("Location: ../admin.php");
    exit();
}
?>
