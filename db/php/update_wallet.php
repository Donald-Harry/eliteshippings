<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_wallet'])) {
    $id = intval($_POST['id']);
    $crypto_type = sanitize($_POST['crypto_type']);
    $wallet_address = sanitize($_POST['wallet_address']);
    $network = sanitize($_POST['network']);
    $status = sanitize($_POST['status']);
    
    $sql = "UPDATE wallet_addresses SET 
            crypto_type = '$crypto_type',
            wallet_address = '$wallet_address',
            network = '$network',
            status = '$status'
            WHERE id = '$id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Wallet address updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating wallet address: " . $db->error;
    }
    
    header("Location: ../admin.php#wallets");
    exit();
}
?>