<?php
include 'functions.php';

if (isset($_POST['save_wallet'])) {
    $id = $_POST['id'];
    $crypto_type = $_POST['crypto_type'];
    $wallet_address = $_POST['wallet_address'];
    $network = $_POST['network'];
    $status = $_POST['status'];
    
    if (empty($id)) {
        // Add new wallet
        $sql = "INSERT INTO wallet_addresses (crypto_type, wallet_address, network, status) 
                VALUES ('$crypto_type', '$wallet_address', '$network', '$status')";
    } else {
        // Update existing wallet
        $sql = "UPDATE wallet_addresses SET 
                crypto_type = '$crypto_type', 
                wallet_address = '$wallet_address', 
                network = '$network', 
                status = '$status'
                WHERE id = '$id'";
    }
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Wallet address saved successfully!";
    } else {
        $_SESSION['error'] = "Error saving wallet address: " . $db->error;
    }
}

header("Location: ../admin.php");
exit();
?>