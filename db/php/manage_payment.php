<?php
include 'functions.php';

if (isset($_POST['add_payment'])) {
    $tracking_id = $_POST['tracking_id'];
    $shipping_cost = $_POST['shipping_cost'];
    $clearance_cost = $_POST['clearance_cost'];
    $total_amount = $_POST['total_amount'];
    $payment_status = $_POST['payment_status'];
    
    $sql = "INSERT INTO payments (tracking_id, shipping_cost, clearance_cost, total_amount, payment_status) 
            VALUES ('$tracking_id', '$shipping_cost', '$clearance_cost', '$total_amount', '$payment_status')";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment details added successfully!";
    } else {
        $_SESSION['error'] = "Error adding payment details: " . $db->error;
    }
}

if (isset($_POST['update_payment'])) {
    $payment_id = $_POST['payment_id'];
    $shipping_cost = $_POST['shipping_cost'];
    $clearance_cost = $_POST['clearance_cost'];
    $total_amount = $_POST['total_amount'];
    $payment_status = $_POST['payment_status'];
    
    $sql = "UPDATE payments SET 
            shipping_cost = '$shipping_cost', 
            clearance_cost = '$clearance_cost', 
            total_amount = '$total_amount', 
            payment_status = '$payment_status',
            updated_at = NOW()
            WHERE id = '$payment_id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating payment details: " . $db->error;
    }
}

if (isset($_POST['delete_payment'])) {
    $payment_id = $_POST['payment_id'];
    
    $sql = "DELETE FROM payments WHERE id = '$payment_id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment details deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting payment details: " . $db->error;
    }
}

header("Location: ../admin.php");
exit();
?>