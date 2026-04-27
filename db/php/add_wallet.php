<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_wallet'])) {
    $crypto_type = sanitize($_POST['crypto_type']);
    $wallet_address = sanitize($_POST['wallet_address']);
    $network = sanitize($_POST['network']);
    $status = sanitize($_POST['status']);
    
    $sql = "INSERT INTO wallet_addresses (crypto_type, wallet_address, network, status) 
            VALUES ('$crypto_type', '$wallet_address', '$network', '$status')";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Wallet address added successfully!";
    } else {
        $_SESSION['error'] = "Error adding wallet address: " . $db->error;
    }
    
    header("Location: ../admin.php#wallets");
    exit();
}
?>