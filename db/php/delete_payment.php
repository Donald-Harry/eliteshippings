<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM payments WHERE id = '$id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment record deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting payment record: " . $db->error;
    }
    
    header("Location: ../admin.php#payments");
    exit();
}
?>