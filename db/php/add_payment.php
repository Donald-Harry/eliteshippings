<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment'])) {
    $tracking_id = sanitize($_POST['tracking_id']);
    $shipping_cost = floatval($_POST['shipping_cost']);
    $clearance_cost = floatval($_POST['clearance_cost']);
    $total_amount = floatval($_POST['total_amount']);
    $payment_status = sanitize($_POST['payment_status']);
    
    $sql = "INSERT INTO payments (tracking_id, shipping_cost, clearance_cost, total_amount, payment_status) 
            VALUES ('$tracking_id', '$shipping_cost', '$clearance_cost', '$total_amount', '$payment_status')";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Payment details added successfully!";
    } else {
        $_SESSION['error'] = "Error adding payment details: " . $db->error;
    }
    
    header("Location: ../admin.php#payments");
    exit();
}
?>