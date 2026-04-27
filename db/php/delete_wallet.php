<?php
include 'functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM wallet_addresses WHERE id = '$id'";
    
    if ($db->query($sql)) {
        $_SESSION['success'] = "Wallet address deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting wallet address: " . $db->error;
    }
    
    header("Location: ../admin.php#wallets");
    exit();
}
?>