<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment'])) {
    $id = intval($_POST['id']);
    $tracking_id = sanitize($_POST['tracking_id']);
    $shipping_cost = floatval($_POST['shipping_cost']);
    $clearance_cost = floatval($_POST['clearance_cost']);
    $total_amount = floatval($_POST['total_amount']);
    $payment_status = sanitize($_POST['payment_status']);
    
    $sql = "UPDATE payments SET 
            tracking_id = '$tracking_id',
            shipping_cost = '$shipping_cost',
            clearance_cost = '$clearance_cost',
            total_amount = '$total_amount',
            payment_status = '$payment_status',
            updated_at = NOW()
            WHERE id = '$id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating payment details: " . $db->error;
    }
    
    header("Location: ../admin.php#payments");
    exit();
}
?>