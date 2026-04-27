<?php 
include 'functions.php';

if (get('type') != 1) {
    echo "<script> alert('Access denied. Please check again as admin'); window.location.href='../admin.php' </script>";
    exit();
}

if (isset($_POST['add'])) {

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

    // Status mapping
    $sta_arr = array('DELETED' => '0', 'UNDO' => '1', 'PENDING' => '1', 'SHIP' => '2', 'DELIVERED' => '3');
    $status = isset($sta_arr[$status]) ? $sta_arr[$status] : '1';

    // Generate tracking ID
    $sl = "SELECT id FROM info ORDER BY id DESC LIMIT 1";
    $qr = $db->query($sl);
    $row = $qr->fetch_assoc();
    $generated1 = $row['id'] + 1239;
    $generated2 = $row['id'] + 2112;
    $generated = $generated1 . '' . $generated2;
    $tracking_id = "BD" . $generated;

    // Handle photo upload
    $filename = $_FILES['photo']['name'];
    $tmp_name = $_FILES['photo']['tmp_name'];
    $fileSize = $_FILES['photo']['size'];

    $img_explode = explode('.', $filename);
    $img_ent = end($img_explode);
    $valid_extensions = ['jpg', 'png', 'jpeg', 'gif', 'webp', 'JPG', 'PNG', 'JPEG', 'GIF', 'WEBP'];

    if (in_array($img_ent, $valid_extensions)) {
        if ($fileSize < 50000000) {
            $img = time() . $filename;
            if (move_uploaded_file($tmp_name, "../uploads/" . $img)) {

                // Insert full record including sender & receiver details
                $in = "INSERT INTO `info`(
                    `sender_name`, `sender_phone`, `sender_email`, 
                    `reciever_name`, `reciever_phone`, `reciever_email`, 
                    `package`, `description`, `tracking_id`, `package_type`, `qty`, `weight`, 
                    `service_type`, `package_status`, `booking_date`, `arrival_date`, 
                    `disperse_address`, `disperse_country`, `delivering_to`, `delivering_country`, 
                    `current_destination`, `perc`, `photo`, `status`
                ) VALUES (
                    '$sender_name', '$sender_phone', '$sender_email',
                    '$reciever_name', '$reciever_phone', '$reciever_email',
                    '$package', '$description', '$tracking_id', '$package_type', '$qty', '$weight',
                    '$service_type', '$package_status', '$booking_date', '$arrival_date',
                    '$disperse_address', '$disperse_country', '$delivering_to', '$delivering_country',
                    '$current_destination', '$perc', '$img', '$status'
                )";

                $qr = $db->query($in);
                if ($qr) {
                    echo "<script> alert('Package successfully added.'); window.location.href='../admin.php' </script>";
                    exit();
                } else {
                    echo "<script> alert('Database error occurred.'); window.location.href='../admin.php' </script>";
                    exit();
                }
            } else {
                echo "<script> alert('Image upload failed.'); window.location.href='../admin.php' </script>";
                exit();
            }
        } else {
            echo "<script> alert('Image size too large. Maximum 50MB allowed.'); window.location.href='../admin.php' </script>";
            exit();
        }
    } else {
        echo "<script> alert('Invalid image format. Only JPG, PNG, GIF, or WEBP allowed.'); window.location.href='../admin.php' </script>";
        exit();
    }
}
?>
