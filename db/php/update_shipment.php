<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'functions.php';

if (get('type') != 1) {
    echo "<script>alert('Access denied. Please check again as admin'); window.location.href='../admin.php'</script>";
    exit();
}

if (isset($_POST['update'])) {

    // Sender Information
    $sender_name = sanitize($_POST['sender_name']);
    $sender_phone = sanitize($_POST['sender_phone']);
    $sender_email = sanitize($_POST['sender_email']);

    // Receiver Information
    $reciever_name = sanitize($_POST['reciever_name']);
    $reciever_phone = sanitize($_POST['reciever_phone']);
    $reciever_email = sanitize($_POST['reciever_email']);

    // Package Information
    $package = sanitize($_POST['package']);
    $description = sanitize($_POST['description']);
    $tracking_id = sanitize($_POST['tracking_id']);
    $package_type = sanitize($_POST['package_type']);
    $qty = sanitize($_POST['qty']);
    $weight = sanitize($_POST['weight']);
    $service_type = sanitize($_POST['service_type']);
    $package_status = sanitize($_POST['package_status']);
    $booking_date = sanitize($_POST['booking_date']);
    $arrival_date = sanitize($_POST['arrival_date']);
    $disperse_address = sanitize($_POST['disperse_address']);
    $disperse_country = sanitize($_POST['disperse_country']);
    $delivering_to = sanitize($_POST['delivering_to']);
    $delivering_country = sanitize($_POST['delivering_country']);
    $current_destination = sanitize($_POST['current_destination']);
    $perc = sanitize($_POST['perc']);
    $status = sanitize($_POST['status']);
    
    // Map status string to its corresponding value
    $status_map = [
        'DELETED' => '0',
        'UNDO' => '1',
        'PENDING' => '1',
        'SHIP' => '2',
        'DELIVERED' => '3'
    ];

    $status = isset($status_map[$status]) ? $status_map[$status] : '1';

    // Handle file upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $filename = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];
        $fileSize = $_FILES['photo']['size'];

        $img_explode = explode('.', $filename);
        $img_ext = end($img_explode);
        $valid_extensions = ['jpg', 'png', 'jpeg', 'gif', 'webp', 'JPG', 'PNG', 'JPEG', 'GIF', 'WEBP'];

        if (in_array($img_ext, $valid_extensions)) {
            if ($fileSize < 50000000) {
                $photo = time() . $filename;
                $upload_dir = '../uploads/';
                $upload_file = $upload_dir . $photo;

                if (!move_uploaded_file($tmp_name, $upload_file)) {
                    echo "<script>alert('Error uploading file.'); window.location.href='../admin.php'</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Image size too large. Maximum 50MB allowed.'); window.location.href='../admin.php'</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid image format.'); window.location.href='../admin.php'</script>";
            exit();
        }
    }

    // Build update query
    $update_query = "UPDATE `info` SET 
        `sender_name` = '$sender_name',
        `sender_phone` = '$sender_phone',
        `sender_email` = '$sender_email',
        `reciever_name` = '$reciever_name',
        `reciever_phone` = '$reciever_phone',
        `reciever_email` = '$reciever_email',
        `package` = '$package',
        `description` = '$description',
        `package_type` = '$package_type',
        `qty` = '$qty',
        `weight` = '$weight',
        `service_type` = '$service_type',
        `package_status` = '$package_status',
        `booking_date` = '$booking_date',
        `arrival_date` = '$arrival_date',
        `disperse_address` = '$disperse_address',
        `disperse_country` = '$disperse_country',
        `delivering_to` = '$delivering_to',
        `delivering_country` = '$delivering_country',
        `current_destination` = '$current_destination',
        `perc` = '$perc',
        `status` = '$status'";

    if ($photo) {
        $update_query .= ", `photo` = '$photo'";
    }

    $update_query .= " WHERE `tracking_id` = '$tracking_id'";

    if ($db->query($update_query)) {
        echo "<script>alert('Update successful.'); window.location.href='../admin.php'</script>";
    } else {
        echo "<script>alert('An error occurred: " . $db->error . "'); window.location.href='../admin.php'</script>";
    }
    exit();

} elseif (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $delete_query = "DELETE FROM `info` WHERE `id` = $id";

    if ($db->query($delete_query)) {
        echo "<script>alert('Shipment deleted successfully.'); window.location.href='../admin.php'</script>";
    } else {
        echo "<script>alert('Could not delete shipment. Error: " . $db->error . "'); window.location.href='../admin.php'</script>";
    }
    exit();

} else {
    echo "<script>alert('Unauthorized access.'); window.location.href='../admin.php'</script>";
    exit();
}
?>
