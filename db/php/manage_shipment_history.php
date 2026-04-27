<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'functions.php';

// Sanitize input safely
function sanitize_input($data)
{
    return trim(strip_tags($data));
}

// ADD HISTORY EVENT
if (isset($_POST['add_history'])) {
    global $db;

    $tracking_id = sanitize_input($_POST['tracking_id']);
    $event_date = sanitize_input($_POST['event_date']);
    $status = sanitize_input($_POST['status']);
    $location = sanitize_input($_POST['location']);
    $description = sanitize_input($_POST['description']);

    if (empty($tracking_id) || empty($event_date) || empty($status) || empty($location) || empty($description)) {
        echo "<script>
                alert('All fields are required.');
                window.location.href = '../admin.php';
              </script>";
        exit();
    }

    // Check if tracking ID exists
    $check = $db->prepare("SELECT id FROM info WHERE tracking_id = ?");
    $check->bind_param("s", $tracking_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo "<script>
                alert('Invalid tracking ID.');
                window.location.href = '../admin.php';
              </script>";
        exit();
    }

    // Insert new history event
    $insert = $db->prepare("INSERT INTO shipment_history (tracking_id, event_date, status, location, description) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssss", $tracking_id, $event_date, $status, $location, $description);

    if ($insert->execute()) {
        // Update main shipment status
        $update = $db->prepare("UPDATE info SET package_status = ?, current_destination = ? WHERE tracking_id = ?");
        $update->bind_param("sss", $status, $location, $tracking_id);
        $update->execute();

        echo "<script>
                alert('History event added successfully.');
                window.location.href = '../admin.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Error adding history event: " . addslashes($db->error) . "');
                window.location.href = '../admin.php';
              </script>";
        exit();
    }
}

// DELETE HISTORY EVENT
if (isset($_POST['delete_history'])) {
    global $db;

    $id = sanitize_input($_POST['id']);
    $tracking_id = sanitize_input($_POST['tracking_id']);

    if (empty($id) || empty($tracking_id)) {
        echo "<script>
                alert('Invalid request.');
                window.location.href = '../admin.php';
              </script>";
        exit();
    }

    $delete = $db->prepare("DELETE FROM shipment_history WHERE id = ? AND tracking_id = ?");
    $delete->bind_param("is", $id, $tracking_id);

    if ($delete->execute()) {
        echo "<script>
                alert('History event deleted successfully.');
                window.location.href = '../admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting history event: " . addslashes($db->error) . "');
                window.location.href = '../admin.php';
              </script>";
    }
    exit();
}

// UPDATE HISTORY EVENT
if (isset($_POST['update_history'])) {
    global $db;

    $id = sanitize_input($_POST['id']);
    $tracking_id = sanitize_input($_POST['tracking_id']);
    $event_date = sanitize_input($_POST['event_date']);
    $status = sanitize_input($_POST['status']);
    $location = sanitize_input($_POST['location']);
    $description = sanitize_input($_POST['description']);

    if (empty($id) || empty($tracking_id) || empty($event_date) || empty($status) || empty($location) || empty($description)) {
        echo "<script>
                alert('All fields are required.');
                window.location.href = '../admin.php';
              </script>";
        exit();
    }

    $update = $db->prepare("UPDATE shipment_history SET event_date = ?, status = ?, location = ?, description = ? WHERE id = ? AND tracking_id = ?");
    $update->bind_param("ssssss", $event_date, $status, $location, $description, $id, $tracking_id);

    if ($update->execute()) {
        // Update latest status in main info
        $latest = $db->prepare("SELECT * FROM shipment_history WHERE tracking_id = ? ORDER BY event_date DESC LIMIT 1");
        $latest->bind_param("s", $tracking_id);
        $latest->execute();
        $latest_result = $latest->get_result();

        if ($latest_result->num_rows > 0) {
            $data = $latest_result->fetch_assoc();
            $main = $db->prepare("UPDATE info SET package_status = ?, current_destination = ? WHERE tracking_id = ?");
            $main->bind_param("sss", $data['status'], $data['location'], $tracking_id);
            $main->execute();
        }

        echo "<script>
                alert('History event updated successfully.');
                window.location.href = '../admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error updating history event: " . addslashes($db->error) . "');
                window.location.href = '../admin.php';
              </script>";
    }
    exit();
}

// NO VALID ACTION
echo "<script>
        alert('Invalid action.');
        window.location.href = '../admin.php';
      </script>";
exit();
?>