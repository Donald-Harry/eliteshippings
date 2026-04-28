<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'php/functions.php';


$success_msg = '';
$error_msg = '';

if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    $error_msg = $_SESSION['error'];
    unset($_SESSION['error']);
}


function get_payment_details($tracking_id)
{
    global $db;
    $sl = "SELECT * FROM `payments` WHERE tracking_id = '$tracking_id'";
    $qr = $db->query($sl);
    return $qr->fetch_assoc();
}


function get_wallet_addresses()
{
    global $db;
    $sl = "SELECT * FROM `wallet_addresses`";
    $qr = $db->query($sl);
    $arr = [];
    while ($row = $qr->fetch_assoc())
        $arr[] = $row;
    return $arr;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Tracking Shipment</title>
    <link rel="icon" href="img/xfavicon.png.pagespeed.ic.4ol6gLfWzM.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="css/bootstrap.min.css%2banimate.css%2bowl.carousel.min.css%2bthemify-icons.css%2bflaticon.css%2bslick.css%2bnice-select.css%2ball.css%2bintlInputPhone.min.css.pagespeed.cc.WNqLfgNLMY.css" />
    <link rel="stylesheet" href="css/A.style.css.pagespeed.cf.fGTFytXrdz.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style type="text/css">
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .shipment-card {
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .shipment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .shipment-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .shipment-body {
            padding: 20px;
            background: white;
        }

        .shipment-footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #dee2e6;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .btn-action {
            margin-right: 5px;
            margin-bottom: 5px;
            border-radius: 4px;
            font-weight: 500;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-ship {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-deleted {
            background-color: #f8d7da;
            color: #721c24;
        }

        .add-shipment-form {
            display: none;
            margin-bottom: 30px;
            border: 2px dashed var(--primary-color);
            border-radius: 8px;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .shipment-progress {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }

        .shipment-progress-bar {
            height: 100%;
            background-color: var(--primary-color);
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .history-section,
        .payment-section,
        .wallet-section {
            margin-top: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .history-header,
        .payment-header,
        .wallet-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
        }

        .history-content,
        .payment-content,
        .wallet-content {
            display: none;
            padding: 15px;
            background: white;
        }

        .history-item,
        .payment-item {
            padding: 10px;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-item:last-child,
        .payment-item:last-child {
            border-bottom: none;
        }

        .history-actions,
        .payment-actions {
            display: flex;
            gap: 5px;
        }

        .history-form,
        .payment-form {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .timeline-event {
            position: relative;
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .timeline-event:before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            width: 8px;
            height: 8px;
            background: var(--primary-color);
            border-radius: 50%;
        }

        /* Payment status badges */
        .payment-status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .payment-status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .payment-status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Wallet section */
        .wallet-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wallet-table th,
        .wallet-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .wallet-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .wallet-status-active {
            color: var(--success-color);
        }

        .wallet-status-inactive {
            color: var(--secondary-color);
        }

        /* Container constraints */
        .shipment-body,
        .add-shipment-form {
            overflow: visible;
        }

        .shipment-card {
            overflow: visible;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .shipment-card {
                margin-bottom: 15px;
            }

            .shipment-body {
                padding: 15px;
            }

            .btn-action {
                width: 100%;
                margin-right: 0;
            }

            .shipment-header h5 {
                font-size: 1rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            /* Smaller dropdown on mobile */
            select.form-control.country-select {
                max-height: 150px;
            }

            .history-actions,
            .payment-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .shipment-body {
                padding: 10px;
            }

            .shipment-header,
            .shipment-footer {
                padding: 10px;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .d-flex.justify-content-between .btn {
                margin-top: 10px;
            }
        }

        /* Card edit mode */
        .edit-mode {
            border: 2px solid var(--primary-color) !important;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3) !important;
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        /* Custom section padding */
        .admin-section {
            padding-top: 80px;
            min-height: 100vh;
        }

        /* Improved form styling */
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #495057;
        }

        /* Animation for form toggle */
        .form-toggle {
            transition: all 0.3s ease;
        }

        /* Custom button styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            border: none;
            font-weight: 500;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
            border: none;
            font-weight: 500;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #bd2130 100%);
            border: none;
            font-weight: 500;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            font-weight: 500;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
            border: none;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php
    include 'nav.php';
    ?>

    <section class="admin-section">

        <div class="container-fluid">


            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row">
                        <h1 class="h3 mb-3 mb-md-0 ps-md-3">Shipment Management</h1>
                        <div>
                            <button class="btn btn-warning me-2" onclick="toggleWalletSection()">
                                <i class="fa fa-wallet"></i> Manage Wallet Addresses
                            </button>
                            <button class="btn btn-primary" onclick="toggleAddForm()">
                                <i class="fa fa-plus"></i> Add New Shipment
                            </button>
                        </div>
                    </div>

                    <!-- Wallet Address Management Section -->
                    <div class="wallet-section" id="wallet_section">
                        <div class="wallet-header" onclick="toggleWalletSection()">
                            <h5 class="mb-0">
                                <i class="fa fa-chevron-down me-2"></i>
                                Wallet Address Management
                            </h5>
                        </div>
                        <div class="wallet-content" id="wallet_content">
                            <?php
                            $wallet_addresses = get_wallet_addresses();
                            if (empty($wallet_addresses)) {
                                echo '<div class="alert alert-info">No wallet addresses found.</div>';
                            } else {
                                ?>
                                <table class="wallet-table">
                                    <thead>
                                        <tr>
                                            <th>Crypto Type</th>
                                            <th>Wallet Address</th>
                                            <th>Network</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($wallet_addresses as $wallet): ?>
                                            <tr>
                                                <td><?= $wallet['crypto_type'] ?></td>
                                                <td>
                                                    <?php
                                                    if (empty($wallet['wallet_address'])) {
                                                        echo '<span class="text-muted">Contact Support</span>';
                                                    } else {
                                                        echo $wallet['wallet_address'];
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $wallet['network'] ?></td>
                                                <td
                                                    class="<?= $wallet['status'] == 1 ? 'wallet-status-active' : 'wallet-status-inactive' ?>">
                                                    <?= $wallet['status'] == 1 ? 'Active' : 'Inactive' ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="editWallet(<?= $wallet['id'] ?>)">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php } ?>

                            <!-- Add/Edit Wallet Form -->
                            <div class="history-form mt-4">
                                <h5 id="wallet_form_title">Add New Wallet Address</h5>
                                <form action="php/manage_wallet.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-lg-5">
                                                <label class="form-label">Crypto Type</label>
                                                <select name="crypto_type" class="form-control" required>
                                                    <option value="">Select Cryptocurrency</option>
                                                    <option value="Bitcoin">Bitcoin (BTC)</option>
                                                    <option value="Ethereum">Ethereum (ETH)</option>
                                                    <option value="USDT">USDT (ERC-20)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Wallet Address</label>
                                                <input type="text" name="wallet_address" class="form-control"
                                                    placeholder="Wallet Address" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Network</label>
                                                <input type="text" name="network" class="form-control"
                                                    placeholder="e.g., Mainnet, ERC20" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="wallet_id" value="">
                                    <button type="submit" name="save_wallet" class="btn btn-success">
                                        <i class="fa fa-save"></i> Save Wallet
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetWalletForm()">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Add Shipment Form -->
                    <div class="add-shipment-form form-toggle" id="add_shipment_form">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-primary">Add New Shipment</h5>
                            <button type="button" class="btn-close" onclick="toggleAddForm()"></button>
                        </div>
                        <!-- Session Messages Display -->
                        <?php if (!empty($success_msg)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success_msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_msg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error_msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form enctype="multipart/form-data" action="php/add_shipment.php" method="POST"
                            autocomplete="off">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Sender Name</label>
                                        <input id="add_first" type="text" name="sender_name" class="form-control"
                                            placeholder="sender Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Sender phone</label>
                                        <input id="add_first" type="text" name="sender_phone" class="form-control"
                                            placeholder="sender phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Sender email</label>
                                        <input id="add_first" type="email" name="sender_email" class="form-control"
                                            placeholder="sender email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Client Name</label>
                                        <input id="add_first" type="text" name="reciever_name" class="form-control"
                                            placeholder="Client Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Client phone</label>
                                        <input id="add_first" type="text" name="reciever_phone" class="form-control"
                                            placeholder="Client phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_first" class="form-label">Client email</label>
                                        <input id="add_first" type="email" name="reciever_email" class="form-control"
                                            placeholder="Client email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="package" class="form-label">Package</label>
                                        <input required type="text" name="package" class="form-control"
                                            placeholder="Package">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="text" name="description" class="form-control"
                                            placeholder="Description">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tracking_id" class="form-label">Tracking ID</label>
                                        <input onclick="alert('This would be generated automatically upon submission.')"
                                            required readonly title="Cannot edit Tracking ID" type="text"
                                            name="tracking_id" class="form-control" value="000000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="package_type" class="form-label">Package Type</label>
                                        <input required type="text" name="package_type" class="form-control"
                                            placeholder="Package Type">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="qty" class="form-label">Quantity</label>
                                        <input required type="number" name="qty" class="form-control"
                                            placeholder="Quantity">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight" class="form-label">Weight</label>
                                        <input required type="number" name="weight" class="form-control"
                                            placeholder="Weight">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_type" class="form-label">Service Type</label>
                                        <input required type="text" name="service_type" class="form-control"
                                            placeholder="Service Type">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="package_status" class="form-label">Package Status</label>
                                        <input required type="text" name="package_status" class="form-control"
                                            placeholder="Package Status">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="booking_date" class="form-label">Booking Date</label>
                                        <input required type="date" name="booking_date" class="form-control"
                                            placeholder="Booking Date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="arrival_date" class="form-label">Arrival Date</label>
                                        <input required type="date" name="arrival_date" class="form-control"
                                            placeholder="Arrival Date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="disperse_address" class="form-label">Disperse Address</label>
                                        <input required type="text" name="disperse_address" class="form-control"
                                            placeholder="Disperse Address">
                                    </div>
                                </div>
                                <!-- HTML -->
                                <div class="col-md-6">
                                    <div class="form-group custom-select-container" id="custom-country">
                                        <label class="form-label">Disperse Country</label>
                                        <div class="custom-select">
                                            <button type="button" class="select-toggle" aria-haspopup="listbox"
                                                aria-expanded="false">
                                                Select Country
                                                <span class="arrow">▾</span>
                                            </button>
                                            <ul class="options" role="listbox" tabindex="-1" aria-label="Countries">
                                                <li data-value="" class="option">Select Country</li>
                                                <li data-value="Afghanistan" class="option">Afghanistan</li>
                                                <li data-value="Albania" class="option">Albania</li>
                                                <li data-value="Algeria" class="option">Algeria</li>
                                                <li data-value="American Samoa" class="option">American Samoa</li>
                                                <li data-value="Andorra" class="option">Andorra</li>
                                                <li data-value="Angola" class="option">Angola</li>
                                                <li data-value="Anguilla" class="option">Anguilla</li>
                                                <li data-value="Antigua & Barbuda" class="option">Antigua & Barbuda</li>
                                                <li data-value="Argentina" class="option">Argentina</li>
                                                <li data-value="Armenia" class="option">Armenia</li>
                                                <li data-value="Aruba" class="option">Aruba</li>
                                                <li data-value="Australia" class="option">Australia</li>
                                                <li data-value="Austria" class="option">Austria</li>
                                                <li data-value="Azerbaijan" class="option">Azerbaijan</li>
                                                <li data-value="Bahamas" class="option">Bahamas</li>
                                                <li data-value="Bahrain" class="option">Bahrain</li>
                                                <li data-value="Bangladesh" class="option">Bangladesh</li>
                                                <li data-value="Barbados" class="option">Barbados</li>
                                                <li data-value="Belarus" class="option">Belarus</li>
                                                <li data-value="Belgium" class="option">Belgium</li>
                                                <li data-value="Belize" class="option">Belize</li>
                                                <li data-value="Benin" class="option">Benin</li>
                                                <li data-value="Bermuda" class="option">Bermuda</li>
                                                <li data-value="Bhutan" class="option">Bhutan</li>
                                                <li data-value="Bolivia" class="option">Bolivia</li>
                                                <li data-value="Bonaire" class="option">Bonaire</li>
                                                <li data-value="Bosnia & Herzegovina" class="option">Bosnia &
                                                    Herzegovina</li>
                                                <li data-value="Botswana" class="option">Botswana</li>
                                                <li data-value="Brazil" class="option">Brazil</li>
                                                <li data-value="British Indian Ocean Ter" class="option">British Indian
                                                    Ocean Ter</li>
                                                <li data-value="Brunei" class="option">Brunei</li>
                                                <li data-value="Bulgaria" class="option">Bulgaria</li>
                                                <li data-value="Burkina Faso" class="option">Burkina Faso</li>
                                                <li data-value="Burundi" class="option">Burundi</li>
                                                <li data-value="Cambodia" class="option">Cambodia</li>
                                                <li data-value="Cameroon" class="option">Cameroon</li>
                                                <li data-value="Canada" class="option">Canada</li>
                                                <li data-value="Canary Islands" class="option">Canary Islands</li>
                                                <li data-value="Cape Verde" class="option">Cape Verde</li>
                                                <li data-value="Cayman Islands" class="option">Cayman Islands</li>
                                                <li data-value="Central African Republic" class="option">Central African
                                                    Republic</li>
                                                <li data-value="Chad" class="option">Chad</li>
                                                <li data-value="Channel Islands" class="option">Channel Islands</li>
                                                <li data-value="Chile" class="option">Chile</li>
                                                <li data-value="China" class="option">China</li>
                                                <li data-value="Christmas Island" class="option">Christmas Island</li>
                                                <li data-value="Cocos Island" class="option">Cocos Island</li>
                                                <li data-value="Colombia" class="option">Colombia</li>
                                                <li data-value="Comoros" class="option">Comoros</li>
                                                <li data-value="Congo" class="option">Congo</li>
                                                <li data-value="Cook Islands" class="option">Cook Islands</li>
                                                <li data-value="Costa Rica" class="option">Costa Rica</li>
                                                <li data-value="Cote DIvoire" class="option">Cote DIvoire</li>
                                                <li data-value="Croatia" class="option">Croatia</li>
                                                <li data-value="Cuba" class="option">Cuba</li>
                                                <li data-value="Curacao" class="option">Curacao</li>
                                                <li data-value="Cyprus" class="option">Cyprus</li>
                                                <li data-value="Czech Republic" class="option">Czech Republic</li>
                                                <li data-value="Denmark" class="option">Denmark</li>
                                                <li data-value="Djibouti" class="option">Djibouti</li>
                                                <li data-value="Dominica" class="option">Dominica</li>
                                                <li data-value="Dominican Republic" class="option">Dominican Republic
                                                </li>
                                                <li data-value="East Timor" class="option">East Timor</li>
                                                <li data-value="Ecuador" class="option">Ecuador</li>
                                                <li data-value="Egypt" class="option">Egypt</li>
                                                <li data-value="El Salvador" class="option">El Salvador</li>
                                                <li data-value="Equatorial Guinea" class="option">Equatorial Guinea</li>
                                                <li data-value="Eritrea" class="option">Eritrea</li>
                                                <li data-value="Estonia" class="option">Estonia</li>
                                                <li data-value="Ethiopia" class="option">Ethiopia</li>
                                                <li data-value="Falkland Islands" class="option">Falkland Islands</li>
                                                <li data-value="Faroe Islands" class="option">Faroe Islands</li>
                                                <li data-value="Fiji" class="option">Fiji</li>
                                                <li data-value="Finland" class="option">Finland</li>
                                                <li data-value="France" class="option">France</li>
                                                <li data-value="French Guiana" class="option">French Guiana</li>
                                                <li data-value="French Polynesia" class="option">French Polynesia</li>
                                                <li data-value="French Southern Ter" class="option">French Southern Ter
                                                </li>
                                                <li data-value="Gabon" class="option">Gabon</li>
                                                <li data-value="Gambia" class="option">Gambia</li>
                                                <li data-value="Georgia" class="option">Georgia</li>
                                                <li data-value="Germany" class="option">Germany</li>
                                                <li data-value="Ghana" class="option">Ghana</li>
                                                <li data-value="Gibraltar" class="option">Gibraltar</li>
                                                <li data-value="Great Britain" class="option">Great Britain</li>
                                                <li data-value="Greece" class="option">Greece</li>
                                                <li data-value="Greenland" class="option">Greenland</li>
                                                <li data-value="Grenada" class="option">Grenada</li>
                                                <li data-value="Guadeloupe" class="option">Guadeloupe</li>
                                                <li data-value="Guam" class="option">Guam</li>
                                                <li data-value="Guatemala" class="option">Guatemala</li>
                                                <li data-value="Guinea" class="option">Guinea</li>
                                                <li data-value="Guyana" class="option">Guyana</li>
                                                <li data-value="Haiti" class="option">Haiti</li>
                                                <li data-value="Hawaii" class="option">Hawaii</li>
                                                <li data-value="Honduras" class="option">Honduras</li>
                                                <li data-value="Hong Kong" class="option">Hong Kong</li>
                                                <li data-value="Hungary" class="option">Hungary</li>
                                                <li data-value="Iceland" class="option">Iceland</li>
                                                <li data-value="India" class="option">India</li>
                                                <li data-value="Indonesia" class="option">Indonesia</li>
                                                <li data-value="Iran" class="option">Iran</li>
                                                <li data-value="Iraq" class="option">Iraq</li>
                                                <li data-value="Ireland" class="option">Ireland</li>
                                                <li data-value="Isle of Man" class="option">Isle of Man</li>
                                                <li data-value="Israel" class="option">Israel</li>
                                                <li data-value="Italy" class="option">Italy</li>
                                                <li data-value="Jamaica" class="option">Jamaica</li>
                                                <li data-value="Japan" class="option">Japan</li>
                                                <li data-value="Jordan" class="option">Jordan</li>
                                                <li data-value="Kazakhstan" class="option">Kazakhstan</li>
                                                <li data-value="Kenya" class="option">Kenya</li>
                                                <li data-value="Kiribati" class="option">Kiribati</li>
                                                <li data-value="Korea North" class="option">Korea North</li>
                                                <li data-value="Korea South" class="option">Korea South</li>
                                                <li data-value="Kuwait" class="option">Kuwait</li>
                                                <li data-value="Kyrgyzstan" class="option">Kyrgyzstan</li>
                                                <li data-value="Laos" class="option">Laos</li>
                                                <li data-value="Latvia" class="option">Latvia</li>
                                                <li data-value="Lebanon" class="option">Lebanon</li>
                                                <li data-value="Lesotho" class="option">Lesotho</li>
                                                <li data-value="Liberia" class="option">Liberia</li>
                                                <li data-value="Libya" class="option">Libya</li>
                                                <li data-value="Liechtenstein" class="option">Liechtenstein</li>
                                                <li data-value="Lithuania" class="option">Lithuania</li>
                                                <li data-value="Luxembourg" class="option">Luxembourg</li>
                                                <li data-value="Macau" class="option">Macau</li>
                                                <li data-value="Macedonia" class="option">Macedonia</li>
                                                <li data-value="Madagascar" class="option">Madagascar</li>
                                                <li data-value="Malaysia" class="option">Malaysia</li>
                                                <li data-value="Malawi" class="option">Malawi</li>
                                                <li data-value="Maldives" class="option">Maldives</li>
                                                <li data-value="Mali" class="option">Mali</li>
                                                <li data-value="Malta" class="option">Malta</li>
                                                <li data-value="Marshall Islands" class="option">Marshall Islands</li>
                                                <li data-value="Martinique" class="option">Martinique</li>
                                                <li data-value="Mauritania" class="option">Mauritania</li>
                                                <li data-value="Mauritius" class="option">Mauritius</li>
                                                <li data-value="Mayotte" class="option">Mayotte</li>
                                                <li data-value="Mexico" class="option">Mexico</li>
                                                <li data-value="Midway Islands" class="option">Midway Islands</li>
                                                <li data-value="Moldova" class="option">Moldova</li>
                                                <li data-value="Monaco" class="option">Monaco</li>
                                                <li data-value="Mongolia" class="option">Mongolia</li>
                                                <li data-value="Montserrat" class="option">Montserrat</li>
                                                <li data-value="Morocco" class="option">Morocco</li>
                                                <li data-value="Mozambique" class="option">Mozambique</li>
                                                <li data-value="Myanmar" class="option">Myanmar</li>
                                                <li data-value="Nambia" class="option">Nambia</li>
                                                <li data-value="Nauru" class="option">Nauru</li>
                                                <li data-value="Nepal" class="option">Nepal</li>
                                                <li data-value="Netherland Antilles" class="option">Netherland Antilles
                                                </li>
                                                <li data-value="Netherlands" class="option">Netherlands (Holland,
                                                    Europe)</li>
                                                <li data-value="Nevis" class="option">Nevis</li>
                                                <li data-value="New Caledonia" class="option">New Caledonia</li>
                                                <li data-value="New Zealand" class="option">New Zealand</li>
                                                <li data-value="Nicaragua" class="option">Nicaragua</li>
                                                <li data-value="Niger" class="option">Niger</li>
                                                <li data-value="Nigeria" class="option">Nigeria</li>
                                                <li data-value="Niue" class="option">Niue</li>
                                                <li data-value="Norfolk Island" class="option">Norfolk Island</li>
                                                <li data-value="Norway" class="option">Norway</li>
                                                <li data-value="Oman" class="option">Oman</li>
                                                <li data-value="Pakistan" class="option">Pakistan</li>
                                                <li data-value="Palau Island" class="option">Palau Island</li>
                                                <li data-value="Palestine" class="option">Palestine</li>
                                                <li data-value="Panama" class="option">Panama</li>
                                                <li data-value="Papua New Guinea" class="option">Papua New Guinea</li>
                                                <li data-value="Paraguay" class="option">Paraguay</li>
                                                <li data-value="Peru" class="option">Peru</li>
                                                <li data-value="Philippines" class="option">Philippines</li>
                                                <li data-value="Pitcairn Island" class="option">Pitcairn Island</li>
                                                <li data-value="Poland" class="option">Poland</li>
                                                <li data-value="Portugal" class="option">Portugal</li>
                                                <li data-value="Puerto Rico" class="option">Puerto Rico</li>
                                                <li data-value="Qatar" class="option">Qatar</li>
                                                <li data-value="Republic of Montenegro" class="option">Republic of
                                                    Montenegro</li>
                                                <li data-value="Republic of Serbia" class="option">Republic of Serbia
                                                </li>
                                                <li data-value="Reunion" class="option">Reunion</li>
                                                <li data-value="Romania" class="option">Romania</li>
                                                <li data-value="Russia" class="option">Russia</li>
                                                <li data-value="Rwanda" class="option">Rwanda</li>
                                                <li data-value="St Barthelemy" class="option">St Barthelemy</li>
                                                <li data-value="St Eustatius" class="option">St Eustatius</li>
                                                <li data-value="St Helena" class="option">St Helena</li>
                                                <li data-value="St Kitts-Nevis" class="option">St Kitts-Nevis</li>
                                                <li data-value="St Lucia" class="option">St Lucia</li>
                                                <li data-value="St Maarten" class="option">St Maarten</li>
                                                <li data-value="St Pierre & Miquelon" class="option">St Pierre &
                                                    Miquelon</li>
                                                <li data-value="St Vincent & Grenadines" class="option">St Vincent &
                                                    Grenadines</li>
                                                <li data-value="Saipan" class="option">Saipan</li>
                                                <li data-value="Samoa" class="option">Samoa</li>
                                                <li data-value="Samoa American" class="option">Samoa American</li>
                                                <li data-value="San Marino" class="option">San Marino</li>
                                                <li data-value="Sao Tome & Principe" class="option">Sao Tome & Principe
                                                </li>
                                                <li data-value="Saudi Arabia" class="option">Saudi Arabia</li>
                                                <li data-value="Scotland" class="option">Scotland</li>
                                                <li data-value="Senegal" class="option">Senegal</li>
                                                <li data-value="Seychelles" class="option">Seychelles</li>
                                                <li data-value="Sierra Leone" class="option">Sierra Leone</li>
                                                <li data-value="Singapore" class="option">Singapore</li>
                                                <li data-value="Slovakia" class="option">Slovakia</li>
                                                <li data-value="Slovenia" class="option">Slovenia</li>
                                                <li data-value="Solomon Islands" class="option">Solomon Islands</li>
                                                <li data-value="Somalia" class="option">Somalia</li>
                                                <li data-value="South Africa" class="option">South Africa</li>
                                                <li data-value="Spain" class="option">Spain</li>
                                                <li data-value="Sri Lanka" class="option">Sri Lanka</li>
                                                <li data-value="Sudan" class="option">Sudan</li>
                                                <li data-value="Suriname" class="option">Suriname</li>
                                                <li data-value="Swaziland" class="option">Swaziland</li>
                                                <li data-value="Sweden" class="option">Sweden</li>
                                                <li data-value="Switzerland" class="option">Switzerland</li>
                                                <li data-value="Syria" class="option">Syria</li>
                                                <li data-value="Tahiti" class="option">Tahiti</li>
                                                <li data-value="Taiwan" class="option">Taiwan</li>
                                                <li data-value="Tajikistan" class="option">Tajikistan</li>
                                                <li data-value="Tanzania" class="option">Tanzania</li>
                                                <li data-value="Thailand" class="option">Thailand</li>
                                                <li data-value="Togo" class="option">Togo</li>
                                                <li data-value="Tokelau" class="option">Tokelau</li>
                                                <li data-value="Tonga" class="option">Tonga</li>
                                                <li data-value="Trinidad & Tobago" class="option">Trinidad & Tobago</li>
                                                <li data-value="Tunisia" class="option">Tunisia</li>
                                                <li data-value="Turkey" class="option">Turkey</li>
                                                <li data-value="Turkmenistan" class="option">Turkmenistan</li>
                                                <li data-value="Turks & Caicos Is" class="option">Turks & Caicos Is</li>
                                                <li data-value="Tuvalu" class="option">Tuvalu</li>
                                                <li data-value="Uganda" class="option">Uganda</li>
                                                <li data-value="Ukraine" class="option">Ukraine</li>
                                                <li data-value="United Arab Emirates" class="option">United Arab
                                                    Emirates</li>
                                                <li data-value="United Kingdom" class="option">United Kingdom</li>
                                                <li data-value="United States of America" class="option">United States
                                                    of America</li>
                                                <li data-value="Uruguay" class="option">Uruguay</li>
                                                <li data-value="Uzbekistan" class="option">Uzbekistan</li>
                                                <li data-value="Vanuatu" class="option">Vanuatu</li>
                                                <li data-value="Vatican City State" class="option">Vatican City State
                                                </li>
                                                <li data-value="Venezuela" class="option">Venezuela</li>
                                                <li data-value="Vietnam" class="option">Vietnam</li>
                                                <li data-value="Virgin Islands (Brit)" class="option">Virgin Islands
                                                    (Brit)</li>
                                                <li data-value="Virgin Islands (USA)" class="option">Virgin Islands
                                                    (USA)</li>
                                                <li data-value="Wake Island" class="option">Wake Island</li>
                                                <li data-value="Wallis & Futana Is" class="option">Wallis & Futana Is
                                                </li>
                                                <li data-value="Yemen" class="option">Yemen</li>
                                                <li data-value="Zaire" class="option">Zaire</li>
                                                <li data-value="Zambia" class="option">Zambia</li>
                                                <li data-value="Zimbabwe" class="option">Zimbabwe</li>

                                                <!-- more -->
                                            </ul>
                                            <!-- hidden real input for form submit -->
                                            <input type="hidden" name="disperse_country" id="disperse_country_hidden"
                                                value="">
                                        </div>
                                    </div>
                                </div>

                                <!-- CSS -->
                                <style>
                                    .custom-select-container {
                                        max-width: 320px;
                                    }

                                    .custom-select {
                                        position: relative;
                                        font-family: sans-serif;
                                    }

                                    .select-toggle {
                                        width: 100%;
                                        text-align: left;
                                        padding: 8px 12px;
                                        border: 1px solid #ccc;
                                        border-radius: 6px;
                                        background: #fff;
                                        cursor: pointer;
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                    }

                                    .options {
                                        position: absolute;
                                        left: 0;
                                        right: 0;
                                        margin: 6px 0 0 0;
                                        list-style: none;
                                        padding: 6px 6px;
                                        border: 1px solid #ddd;
                                        background: #fff;
                                        border-radius: 6px;
                                        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                                        max-height: 180px;
                                        /* fixed height */
                                        overflow-y: auto;
                                        /* scrollable options */
                                        z-index: 999;
                                        display: none;
                                        /* hidden by default */
                                    }

                                    .options .option {
                                        padding: 8px 10px;
                                        cursor: pointer;
                                    }

                                    .options .option:hover {
                                        background: #f2f2f2;
                                    }

                                    .custom-select.open .options {
                                        display: block;
                                    }

                                    .arrow {
                                        margin-left: 8px;
                                    }
                                </style>

                                <!-- JS -->


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivering_to" class="form-label">Delivering Address</label>
                                        <input required type="text" name="delivering_to" class="form-control"
                                            placeholder="Delivering Address">
                                    </div>
                                </div>

                                <!-- Put this where you want the control -->
                                <div class="col-md-6">
                                    <div class="form-group custom-select-container" id="custom-country-1">
                                        <label class="form-label" for="delivering_country_hidden">Destination
                                            Country</label>

                                        <div class="custom-select" role="presentation">
                                            <button type="button" class="select-toggle" aria-haspopup="listbox"
                                                aria-expanded="false" id="select-toggle-1">
                                                <span class="selected-text">Select Country</span>
                                                <span class="arrow">▾</span>
                                            </button>

                                            <ul class="options" role="listbox" tabindex="-1" aria-label="Countries"
                                                id="options-list-1">
                                                <li data-value="" class="option">Select Country</li>
                                                <li data-value="Afghanistan" class="option">Afghanistan</li>
                                                <li data-value="Albania" class="option">Albania</li>
                                                <li data-value="Algeria" class="option">Algeria</li>
                                                <li data-value="American Samoa" class="option">American Samoa</li>
                                                <li data-value="Andorra" class="option">Andorra</li>
                                                <li data-value="Angola" class="option">Angola</li>
                                                <li data-value="Anguilla" class="option">Anguilla</li>
                                                <li data-value="Antigua & Barbuda" class="option">Antigua & Barbuda</li>
                                                <li data-value="Argentina" class="option">Argentina</li>
                                                <li data-value="Armenia" class="option">Armenia</li>
                                                <li data-value="Aruba" class="option">Aruba</li>
                                                <li data-value="Australia" class="option">Australia</li>
                                                <li data-value="Austria" class="option">Austria</li>
                                                <li data-value="Azerbaijan" class="option">Azerbaijan</li>
                                                <li data-value="Bahamas" class="option">Bahamas</li>
                                                <li data-value="Bahrain" class="option">Bahrain</li>
                                                <li data-value="Bangladesh" class="option">Bangladesh</li>
                                                <li data-value="Barbados" class="option">Barbados</li>
                                                <li data-value="Belarus" class="option">Belarus</li>
                                                <li data-value="Belgium" class="option">Belgium</li>
                                                <li data-value="Belize" class="option">Belize</li>
                                                <li data-value="Benin" class="option">Benin</li>
                                                <li data-value="Bermuda" class="option">Bermuda</li>
                                                <li data-value="Bhutan" class="option">Bhutan</li>
                                                <li data-value="Bolivia" class="option">Bolivia</li>
                                                <li data-value="Bonaire" class="option">Bonaire</li>
                                                <li data-value="Bosnia & Herzegovina" class="option">Bosnia &
                                                    Herzegovina</li>
                                                <li data-value="Botswana" class="option">Botswana</li>
                                                <li data-value="Brazil" class="option">Brazil</li>
                                                <li data-value="British Indian Ocean Ter" class="option">British Indian
                                                    Ocean Ter</li>
                                                <li data-value="Brunei" class="option">Brunei</li>
                                                <li data-value="Bulgaria" class="option">Bulgaria</li>
                                                <li data-value="Burkina Faso" class="option">Burkina Faso</li>
                                                <li data-value="Burundi" class="option">Burundi</li>
                                                <li data-value="Cambodia" class="option">Cambodia</li>
                                                <li data-value="Cameroon" class="option">Cameroon</li>
                                                <li data-value="Canada" class="option">Canada</li>
                                                <li data-value="Canary Islands" class="option">Canary Islands</li>
                                                <li data-value="Cape Verde" class="option">Cape Verde</li>
                                                <li data-value="Cayman Islands" class="option">Cayman Islands</li>
                                                <li data-value="Central African Republic" class="option">Central African
                                                    Republic</li>
                                                <li data-value="Chad" class="option">Chad</li>
                                                <li data-value="Channel Islands" class="option">Channel Islands</li>
                                                <li data-value="Chile" class="option">Chile</li>
                                                <li data-value="China" class="option">China</li>
                                                <li data-value="Christmas Island" class="option">Christmas Island</li>
                                                <li data-value="Cocos Island" class="option">Cocos Island</li>
                                                <li data-value="Colombia" class="option">Colombia</li>
                                                <li data-value="Comoros" class="option">Comoros</li>
                                                <li data-value="Congo" class="option">Congo</li>
                                                <li data-value="Cook Islands" class="option">Cook Islands</li>
                                                <li data-value="Costa Rica" class="option">Costa Rica</li>
                                                <li data-value="Cote DIvoire" class="option">Cote DIvoire</li>
                                                <li data-value="Croatia" class="option">Croatia</li>
                                                <li data-value="Cuba" class="option">Cuba</li>
                                                <li data-value="Curacao" class="option">Curacao</li>
                                                <li data-value="Cyprus" class="option">Cyprus</li>
                                                <li data-value="Czech Republic" class="option">Czech Republic</li>
                                                <li data-value="Denmark" class="option">Denmark</li>
                                                <li data-value="Djibouti" class="option">Djibouti</li>
                                                <li data-value="Dominica" class="option">Dominica</li>
                                                <li data-value="Dominican Republic" class="option">Dominican Republic
                                                </li>
                                                <li data-value="East Timor" class="option">East Timor</li>
                                                <li data-value="Ecuador" class="option">Ecuador</li>
                                                <li data-value="Egypt" class="option">Egypt</li>
                                                <li data-value="El Salvador" class="option">El Salvador</li>
                                                <li data-value="Equatorial Guinea" class="option">Equatorial Guinea</li>
                                                <li data-value="Eritrea" class="option">Eritrea</li>
                                                <li data-value="Estonia" class="option">Estonia</li>
                                                <li data-value="Ethiopia" class="option">Ethiopia</li>
                                                <li data-value="Falkland Islands" class="option">Falkland Islands</li>
                                                <li data-value="Faroe Islands" class="option">Faroe Islands</li>
                                                <li data-value="Fiji" class="option">Fiji</li>
                                                <li data-value="Finland" class="option">Finland</li>
                                                <li data-value="France" class="option">France</li>
                                                <li data-value="French Guiana" class="option">French Guiana</li>
                                                <li data-value="French Polynesia" class="option">French Polynesia</li>
                                                <li data-value="French Southern Ter" class="option">French Southern Ter
                                                </li>
                                                <li data-value="Gabon" class="option">Gabon</li>
                                                <li data-value="Gambia" class="option">Gambia</li>
                                                <li data-value="Georgia" class="option">Georgia</li>
                                                <li data-value="Germany" class="option">Germany</li>
                                                <li data-value="Ghana" class="option">Ghana</li>
                                                <li data-value="Gibraltar" class="option">Gibraltar</li>
                                                <li data-value="Great Britain" class="option">Great Britain</li>
                                                <li data-value="Greece" class="option">Greece</li>
                                                <li data-value="Greenland" class="option">Greenland</li>
                                                <li data-value="Grenada" class="option">Grenada</li>
                                                <li data-value="Guadeloupe" class="option">Guadeloupe</li>
                                                <li data-value="Guam" class="option">Guam</li>
                                                <li data-value="Guatemala" class="option">Guatemala</li>
                                                <li data-value="Guinea" class="option">Guinea</li>
                                                <li data-value="Guyana" class="option">Guyana</li>
                                                <li data-value="Haiti" class="option">Haiti</li>
                                                <li data-value="Hawaii" class="option">Hawaii</li>
                                                <li data-value="Honduras" class="option">Honduras</li>
                                                <li data-value="Hong Kong" class="option">Hong Kong</li>
                                                <li data-value="Hungary" class="option">Hungary</li>
                                                <li data-value="Iceland" class="option">Iceland</li>
                                                <li data-value="India" class="option">India</li>
                                                <li data-value="Indonesia" class="option">Indonesia</li>
                                                <li data-value="Iran" class="option">Iran</li>
                                                <li data-value="Iraq" class="option">Iraq</li>
                                                <li data-value="Ireland" class="option">Ireland</li>
                                                <li data-value="Isle of Man" class="option">Isle of Man</li>
                                                <li data-value="Israel" class="option">Israel</li>
                                                <li data-value="Italy" class="option">Italy</li>
                                                <li data-value="Jamaica" class="option">Jamaica</li>
                                                <li data-value="Japan" class="option">Japan</li>
                                                <li data-value="Jordan" class="option">Jordan</li>
                                                <li data-value="Kazakhstan" class="option">Kazakhstan</li>
                                                <li data-value="Kenya" class="option">Kenya</li>
                                                <li data-value="Kiribati" class="option">Kiribati</li>
                                                <li data-value="Korea North" class="option">Korea North</li>
                                                <li data-value="Korea South" class="option">Korea South</li>
                                                <li data-value="Kuwait" class="option">Kuwait</li>
                                                <li data-value="Kyrgyzstan" class="option">Kyrgyzstan</li>
                                                <li data-value="Laos" class="option">Laos</li>
                                                <li data-value="Latvia" class="option">Latvia</li>
                                                <li data-value="Lebanon" class="option">Lebanon</li>
                                                <li data-value="Lesotho" class="option">Lesotho</li>
                                                <li data-value="Liberia" class="option">Liberia</li>
                                                <li data-value="Libya" class="option">Libya</li>
                                                <li data-value="Liechtenstein" class="option">Liechtenstein</li>
                                                <li data-value="Lithuania" class="option">Lithuania</li>
                                                <li data-value="Luxembourg" class="option">Luxembourg</li>
                                                <li data-value="Macau" class="option">Macau</li>
                                                <li data-value="Macedonia" class="option">Macedonia</li>
                                                <li data-value="Madagascar" class="option">Madagascar</li>
                                                <li data-value="Malaysia" class="option">Malaysia</li>
                                                <li data-value="Malawi" class="option">Malawi</li>
                                                <li data-value="Maldives" class="option">Maldives</li>
                                                <li data-value="Mali" class="option">Mali</li>
                                                <li data-value="Malta" class="option">Malta</li>
                                                <li data-value="Marshall Islands" class="option">Marshall Islands</li>
                                                <li data-value="Martinique" class="option">Martinique</li>
                                                <li data-value="Mauritania" class="option">Mauritania</li>
                                                <li data-value="Mauritius" class="option">Mauritius</li>
                                                <li data-value="Mayotte" class="option">Mayotte</li>
                                                <li data-value="Mexico" class="option">Mexico</li>
                                                <li data-value="Midway Islands" class="option">Midway Islands</li>
                                                <li data-value="Moldova" class="option">Moldova</li>
                                                <li data-value="Monaco" class="option">Monaco</li>
                                                <li data-value="Mongolia" class="option">Mongolia</li>
                                                <li data-value="Montserrat" class="option">Montserrat</li>
                                                <li data-value="Morocco" class="option">Morocco</li>
                                                <li data-value="Mozambique" class="option">Mozambique</li>
                                                <li data-value="Myanmar" class="option">Myanmar</li>
                                                <li data-value="Nambia" class="option">Nambia</li>
                                                <li data-value="Nauru" class="option">Nauru</li>
                                                <li data-value="Nepal" class="option">Nepal</li>
                                                <li data-value="Netherland Antilles" class="option">Netherland Antilles
                                                </li>
                                                <li data-value="Netherlands" class="option">Netherlands (Holland,
                                                    Europe)</li>
                                                <li data-value="Nevis" class="option">Nevis</li>
                                                <li data-value="New Caledonia" class="option">New Caledonia</li>
                                                <li data-value="New Zealand" class="option">New Zealand</li>
                                                <li data-value="Nicaragua" class="option">Nicaragua</li>
                                                <li data-value="Niger" class="option">Niger</li>
                                                <li data-value="Nigeria" class="option">Nigeria</li>
                                                <li data-value="Niue" class="option">Niue</li>
                                                <li data-value="Norfolk Island" class="option">Norfolk Island</li>
                                                <li data-value="Norway" class="option">Norway</li>
                                                <li data-value="Oman" class="option">Oman</li>
                                                <li data-value="Pakistan" class="option">Pakistan</li>
                                                <li data-value="Palau Island" class="option">Palau Island</li>
                                                <li data-value="Palestine" class="option">Palestine</li>
                                                <li data-value="Panama" class="option">Panama</li>
                                                <li data-value="Papua New Guinea" class="option">Papua New Guinea</li>
                                                <li data-value="Paraguay" class="option">Paraguay</li>
                                                <li data-value="Peru" class="option">Peru</li>
                                                <li data-value="Philippines" class="option">Philippines</li>
                                                <li data-value="Pitcairn Island" class="option">Pitcairn Island</li>
                                                <li data-value="Poland" class="option">Poland</li>
                                                <li data-value="Portugal" class="option">Portugal</li>
                                                <li data-value="Puerto Rico" class="option">Puerto Rico</li>
                                                <li data-value="Qatar" class="option">Qatar</li>
                                                <li data-value="Republic of Montenegro" class="option">Republic of
                                                    Montenegro</li>
                                                <li data-value="Republic of Serbia" class="option">Republic of Serbia
                                                </li>
                                                <li data-value="Reunion" class="option">Reunion</li>
                                                <li data-value="Romania" class="option">Romania</li>
                                                <li data-value="Russia" class="option">Russia</li>
                                                <li data-value="Rwanda" class="option">Rwanda</li>
                                                <li data-value="St Barthelemy" class="option">St Barthelemy</li>
                                                <li data-value="St Eustatius" class="option">St Eustatius</li>
                                                <li data-value="St Helena" class="option">St Helena</li>
                                                <li data-value="St Kitts-Nevis" class="option">St Kitts-Nevis</li>
                                                <li data-value="St Lucia" class="option">St Lucia</li>
                                                <li data-value="St Maarten" class="option">St Maarten</li>
                                                <li data-value="St Pierre & Miquelon" class="option">St Pierre &
                                                    Miquelon</li>
                                                <li data-value="St Vincent & Grenadines" class="option">St Vincent &
                                                    Grenadines</li>
                                                <li data-value="Saipan" class="option">Saipan</li>
                                                <li data-value="Samoa" class="option">Samoa</li>
                                                <li data-value="Samoa American" class="option">Samoa American</li>
                                                <li data-value="San Marino" class="option">San Marino</li>
                                                <li data-value="Sao Tome & Principe" class="option">Sao Tome & Principe
                                                </li>
                                                <li data-value="Saudi Arabia" class="option">Saudi Arabia</li>
                                                <li data-value="Scotland" class="option">Scotland</li>
                                                <li data-value="Senegal" class="option">Senegal</li>
                                                <li data-value="Seychelles" class="option">Seychelles</li>
                                                <li data-value="Sierra Leone" class="option">Sierra Leone</li>
                                                <li data-value="Singapore" class="option">Singapore</li>
                                                <li data-value="Slovakia" class="option">Slovakia</li>
                                                <li data-value="Slovenia" class="option">Slovenia</li>
                                                <li data-value="Solomon Islands" class="option">Solomon Islands</li>
                                                <li data-value="Somalia" class="option">Somalia</li>
                                                <li data-value="South Africa" class="option">South Africa</li>
                                                <li data-value="Spain" class="option">Spain</li>
                                                <li data-value="Sri Lanka" class="option">Sri Lanka</li>
                                                <li data-value="Sudan" class="option">Sudan</li>
                                                <li data-value="Suriname" class="option">Suriname</li>
                                                <li data-value="Swaziland" class="option">Swaziland</li>
                                                <li data-value="Sweden" class="option">Sweden</li>
                                                <li data-value="Switzerland" class="option">Switzerland</li>
                                                <li data-value="Syria" class="option">Syria</li>
                                                <li data-value="Tahiti" class="option">Tahiti</li>
                                                <li data-value="Taiwan" class="option">Taiwan</li>
                                                <li data-value="Tajikistan" class="option">Tajikistan</li>
                                                <li data-value="Tanzania" class="option">Tanzania</li>
                                                <li data-value="Thailand" class="option">Thailand</li>
                                                <li data-value="Togo" class="option">Togo</li>
                                                <li data-value="Tokelau" class="option">Tokelau</li>
                                                <li data-value="Tonga" class="option">Tonga</li>
                                                <li data-value="Trinidad & Tobago" class="option">Trinidad & Tobago</li>
                                                <li data-value="Tunisia" class="option">Tunisia</li>
                                                <li data-value="Turkey" class="option">Turkey</li>
                                                <li data-value="Turkmenistan" class="option">Turkmenistan</li>
                                                <li data-value="Turks & Caicos Is" class="option">Turks & Caicos Is</li>
                                                <li data-value="Tuvalu" class="option">Tuvalu</li>
                                                <li data-value="Uganda" class="option">Uganda</li>
                                                <li data-value="Ukraine" class="option">Ukraine</li>
                                                <li data-value="United Arab Emirates" class="option">United Arab
                                                    Emirates</li>
                                                <li data-value="United Kingdom" class="option">United Kingdom</li>
                                                <li data-value="United States of America" class="option">United States
                                                    of America</li>
                                                <li data-value="Uruguay" class="option">Uruguay</li>
                                                <li data-value="Uzbekistan" class="option">Uzbekistan</li>
                                                <li data-value="Vanuatu" class="option">Vanuatu</li>
                                                <li data-value="Vatican City State" class="option">Vatican City State
                                                </li>
                                                <li data-value="Venezuela" class="option">Venezuela</li>
                                                <li data-value="Vietnam" class="option">Vietnam</li>
                                                <li data-value="Virgin Islands (Brit)" class="option">Virgin Islands
                                                    (Brit)</li>
                                                <li data-value="Virgin Islands (USA)" class="option">Virgin Islands
                                                    (USA)</li>
                                                <li data-value="Wake Island" class="option">Wake Island</li>
                                                <li data-value="Wallis & Futana Is" class="option">Wallis & Futana Is
                                                </li>
                                                <li data-value="Yemen" class="option">Yemen</li>
                                                <li data-value="Zaire" class="option">Zaire</li>
                                                <li data-value="Zambia" class="option">Zambia</li>
                                                <li data-value="Zimbabwe" class="option">Zimbabwe</li>

                                            </ul>


                                            <input type="hidden" name="delivering_country"
                                                id="delivering_country_hidden" value="">
                                        </div>
                                    </div>
                                </div>

                                <!-- Styles (put in your CSS file or inside <style>) -->
                                <style>
                                    .custom-select-container {
                                        max-width: 350px;
                                        width: 100%;
                                        position: relative;
                                    }

                                    .select-toggle {
                                        width: 100%;
                                        padding: 10px 14px;
                                        border: 1px solid #ccc;
                                        border-radius: 6px;
                                        background: #fff;
                                        cursor: pointer;
                                        font-size: 15px;
                                        text-align: left;
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                    }

                                    .options {
                                        position: absolute;
                                        left: 0;
                                        right: 0;
                                        top: calc(100% + 6px);
                                        margin: 0;
                                        border: 1px solid #ddd;
                                        border-radius: 6px;
                                        background: #fff;
                                        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                                        max-height: 220px;
                                        /* fixed height */
                                        overflow-y: auto;
                                        /* scrollable */
                                        z-index: 9999;
                                        display: none;
                                        list-style: none;
                                        padding: 6px 0;
                                    }

                                    .option {
                                        padding: 8px 12px;
                                        cursor: pointer;
                                    }

                                    .option:hover,
                                    .option[aria-selected="true"] {
                                        background: #f3f3f3;
                                    }

                                    .custom-select.open .options {
                                        display: block;
                                    }

                                    .arrow {
                                        margin-left: 10px;
                                        font-size: 12px;
                                    }
                                </style>

                                <!-- Script (IDs match the HTML above) -->
                                <script>
                                    (function () {
                                        const root = document.getElementById('custom-country-1');
                                        if (!root) return;

                                        const selectWrap = root.querySelector('.custom-select');
                                        const toggle = root.querySelector('#select-toggle-1');
                                        const selectedText = toggle.querySelector('.selected-text');
                                        const optionsList = root.querySelector('#options-list-1');
                                        const options = optionsList.querySelectorAll('.option');
                                        const hidden = document.getElementById('delivering_country_hidden');

                                        // toggle open/close
                                        toggle.addEventListener('click', (e) => {
                                            e.stopPropagation();
                                            selectWrap.classList.toggle('open');
                                            const expanded = toggle.getAttribute('aria-expanded') === 'true';
                                            toggle.setAttribute('aria-expanded', String(!expanded));
                                        });

                                        // click option
                                        options.forEach(opt => {
                                            opt.addEventListener('click', (e) => {
                                                e.stopPropagation();
                                                // remove aria-selected from others
                                                options.forEach(o => o.setAttribute('aria-selected', 'false'));
                                                opt.setAttribute('aria-selected', 'true');

                                                const val = opt.getAttribute('data-value') || '';
                                                selectedText.textContent = opt.textContent;
                                                hidden.value = val;
                                                selectWrap.classList.remove('open');
                                                toggle.setAttribute('aria-expanded', 'false');
                                                // optional: trigger change event on hidden input
                                                const ev = new Event('change', { bubbles: true });
                                                hidden.dispatchEvent(ev);
                                            });
                                        });

                                        // close when clicking outside
                                        document.addEventListener('click', (e) => {
                                            if (!root.contains(e.target)) {
                                                selectWrap.classList.remove('open');
                                                toggle.setAttribute('aria-expanded', 'false');
                                            }
                                        });

                                        // keyboard accessibility (basic)
                                        toggle.addEventListener('keydown', (e) => {
                                            if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                                                e.preventDefault();
                                                selectWrap.classList.add('open');
                                                toggle.setAttribute('aria-expanded', 'true');
                                                optionsList.focus();
                                            }
                                        });

                                        optionsList.addEventListener('keydown', (e) => {
                                            const focusable = Array.from(options);
                                            const idx = focusable.indexOf(document.activeElement);
                                            if (e.key === 'ArrowDown') {
                                                e.preventDefault();
                                                const next = focusable[(idx + 1) % focusable.length];
                                                next.focus();
                                            } else if (e.key === 'ArrowUp') {
                                                e.preventDefault();
                                                const prev = focusable[(idx - 1 + focusable.length) % focusable.length];
                                                prev.focus();
                                            } else if (e.key === 'Enter') {
                                                e.preventDefault();
                                                if (document.activeElement && document.activeElement.classList.contains('option')) {
                                                    document.activeElement.click();
                                                }
                                            } else if (e.key === 'Escape') {
                                                selectWrap.classList.remove('open');
                                                toggle.setAttribute('aria-expanded', 'false');
                                                toggle.focus();
                                            }
                                        });

                                        // make options focusable for keyboard nav
                                        options.forEach(o => o.setAttribute('tabindex', '0'));
                                    })();
                                </script>











                                <!--<div class="col-md-6">-->
                                <!--    <div class="form-group country-select-container">-->
                                <!--        <label for="delivering_country" class="form-label">Destination Country</label>-->
                                <!--        <select required name="delivering_country" class="form-control country-select" id="delivering_country">-->
                                <!--            <option value="">Select Country</option>-->
                                <!--            <option value="Afganistan">Afghanistan</option>-->
                                <!--            <option value="Albania">Albania</option>-->

                                <!--        </select>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current_destination" class="form-label">Current Destination</label>
                                        <input required type="text" name="current_destination" class="form-control"
                                            placeholder="Current Destination">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="perc" class="form-label">Progress (%)</label>
                                        <input required type="number" name="perc" class="form-control"
                                            placeholder="Enter Progress number" min="0" max="100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="photo" class="form-label">Photo</label>
                                        <input required type="file" name="photo" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status</label>
                                        <select required name="status" class="form-control">
                                            <option>PENDING</option>
                                            <option>SHIP</option>
                                            <option>DELIVERED</option>
                                            <option>DELETED</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" name="add" class="btn btn-success">
                                        <i class="fa fa-check"></i> Add Shipment
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="shipments-list">
                        <?php
                        function get_add_shipment()
                        {
                            global $db;
                            $sl = "SELECT * FROM info ORDER BY id DESC";
                            $qr = $db->query($sl);
                            $arr = [];
                            while ($row = $qr->fetch_assoc())
                                $arr[] = $row;
                            return $arr;
                        }

                        function get_shipment_history($tracking_id)
                        {
                            global $db;
                            $sl = "SELECT `id`, `tracking_id`, `event_date`, `status`, `location`, `description` FROM `shipment_history` WHERE tracking_id = '$tracking_id' ORDER BY event_date DESC";
                            $qr = $db->query($sl);
                            $arr = [];
                            while ($row = $qr->fetch_assoc())
                                $arr[] = $row;
                            return $arr;
                        }

                        $get_add_shipment = get_add_shipment();
                        $count = 0;

                        if (empty($get_add_shipment)) {
                            echo '
                        <div class="empty-state">
                            <i class="fa fa-box-open"></i>
                            <h4>No Shipments Found</h4>
                            <p>Get started by adding your first shipment.</p>
                            <button class="btn btn-primary mt-2" onclick="toggleAddForm()">
                                <i class="fa fa-plus"></i> Add New Shipment
                            </button>
                        </div>';
                        } else {
                            foreach ($get_add_shipment as $ship) {
                                $count += 1;
                                if ($ship['status'] == 1) {
                                    $status = "PENDING";
                                    $status1 = "SHIPPED";
                                    $status2 = "DELIVERED";
                                    $status3 = "DELETED";
                                    $status_class = "status-pending";
                                } else if ($ship['status'] == 2) {
                                    $status = "SHIPPED";
                                    $status1 = "PENDING";
                                    $status2 = "DELIVERED";
                                    $status3 = "DELETED";
                                    $status_class = "status-ship";
                                } else if ($ship['status'] == 3) {
                                    $status = "DELIVERED";
                                    $status1 = "PENDING";
                                    $status2 = "SHIPPED";
                                    $status3 = "DELETED";
                                    $status_class = "status-delivered";
                                } else {
                                    $status = "UNDO";
                                    $status1 = "PENDING";
                                    $status2 = "DELIVERED";
                                    $status3 = "SHIPPED";
                                    $status_class = "status-deleted";
                                }
                                $real_status = $status;


                                $shipment_history = get_shipment_history($ship['tracking_id']);


                                $payment_details = get_payment_details($ship['tracking_id']);
                                ?>
                                <div class="shipment-card card" id="shipment_<?php echo $ship['id']; ?>">
                                    <div class="shipment-header">
                                        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                                            <div class="mb-2 mb-md-0">
                                                <h5 class="mb-0">Shipment #<?php echo $count ?></h5>
                                                <small class="text-muted">Tracking ID: <?= $ship['tracking_id'] ?></small>
                                            </div>
                                            <span class="status-badge <?php echo $status_class; ?>"><?= $real_status ?></span>
                                        </div>
                                    </div>
                                    <div class="shipment-body">
                                        <form enctype="multipart/form-data" action="php/update_shipment.php" method="POST"
                                            autocomplete="off">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Sender Name</label>
                                                        <input type="text" name="sender_name" class="form-control"
                                                            value="<?= $ship['sender_name'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Sender phone</label>
                                                        <input type="text" name="sender_phone" class="form-control"
                                                            value="<?= $ship['sender_phone'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Sender email</label>
                                                        <input type="email" name="sender_email" class="form-control"
                                                            value="<?= $ship['sender_email'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Client Name</label>
                                                        <input type="text" name="reciever_name" class="form-control"
                                                            value="<?= $ship['reciever_name'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Client phone</label>
                                                        <input type="text" name="reciever_phone" class="form-control"
                                                            value="<?= $ship['reciever_phone'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="add_first" class="form-label">Client email</label>
                                                        <input type="email" name="reciever_email" class="form-control"
                                                            value="<?= $ship['reciever_email'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Package</label>
                                                        <input type="text" name="package" class="form-control"
                                                            value="<?= $ship['package'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Description</label>
                                                        <input type="text" name="description" class="form-control"
                                                            value="<?= $ship['description'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Package Type</label>
                                                        <input type="text" name="package_type" class="form-control"
                                                            value="<?= $ship['package_type'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" name="qty" class="form-control"
                                                            value="<?= $ship['qty'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Weight</label>
                                                        <input type="text" name="weight" class="form-control"
                                                            value="<?= $ship['weight'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Service Type</label>
                                                        <input type="text" name="service_type" class="form-control"
                                                            value="<?= $ship['service_type'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Package Status</label>
                                                        <input type="text" name="package_status" class="form-control"
                                                            value="<?= $ship['package_status'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Booking Date</label>
                                                        <!-- <input type="date" name="booking_date" class="form-control"
                                                            value="<?= $ship['booking_date'] ?>"> -->
                                                        <input type="date" name="booking_date" class="form-control"
                                                            value="<?= date('Y-m-d', strtotime($ship['booking_date'])) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Arrival Date</label>
                                                        <input type="date" name="arrival_date" class="form-control"
                                                            value="<?= date('Y-m-d', strtotime($ship['arrival_date'])) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Disperse Address</label>
                                                        <input type="text" name="disperse_address" class="form-control"
                                                            value="<?= $ship['disperse_address'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group country-select-container">
                                                        <label class="form-label">Disperse Country</label>
                                                        <select name="disperse_country" class="form-control country-select">
                                                            <option><?= $ship['disperse_country'] ?></option>
                                                            <option value="United States">United States</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="France">France</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="China">China</option>
                                                            <option value="India">India</option>
                                                            <option value="Brazil">Brazil</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Delivering Address</label>
                                                        <input type="text" name="delivering_to" class="form-control"
                                                            value="<?= $ship['delivering_to'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group country-select-container">
                                                        <label class="form-label">Destination Country</label>
                                                        <select name="delivering_country" class="form-control country-select">
                                                            <option><?= $ship['delivering_country'] ?></option>
                                                            <option value="United States">United States</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="France">France</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="China">China</option>
                                                            <option value="India">India</option>
                                                            <option value="Brazil">Brazil</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Current Destination</label>
                                                        <input type="text" name="current_destination" class="form-control"
                                                            value="<?= $ship['current_destination'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Progress</label>
                                                        <input type="text" name="perc" class="form-control"
                                                            value="<?= $ship['perc'] ?>">
                                                        <div class="shipment-progress">
                                                            <div class="shipment-progress-bar"
                                                                style="width: <?= $ship['perc'] ?>%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Photo</label>
                                                        <input type="file" name="photo" class="form-control"
                                                            value="<?= $ship['photo'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-control">
                                                            <option><?= $status ?></option>
                                                            <option><?= $status1 ?></option>
                                                            <option><?= $status2 ?></option>
                                                            <option><?= $status3 ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="<?= $ship['id'] ?>">
                                            <input type="hidden" name="tracking_id" value="<?= $ship['tracking_id'] ?>">
                                    </div>
                                    <div class="shipment-footer">
                                        <div class="d-flex justify-content-end flex-wrap">
                                            <button type="submit" name="update" class="btn btn-primary btn-action">
                                                <i class="fa fa-save"></i> Update
                                            </button>
                                            <button type="submit" name="delete" class="btn btn-danger btn-action"
                                                onclick="return confirm('Are you sure you want to delete this shipment?');">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                            <button type="button" class="btn btn-info btn-action"
                                                onclick="toggleHistory('history_<?= $ship['tracking_id'] ?>')">
                                                <i class="fa fa-history"></i> Shipping History
                                            </button>
                                            <button type="button" class="btn btn-warning btn-action"
                                                onclick="togglePayment('payment_<?= $ship['tracking_id'] ?>')">
                                                <i class="fa fa-credit-card"></i> Payment Details
                                            </button>
                                        </div>
                                        </form>
                                    </div>

                                    <!-- Shipping History Section -->
                                    <div class="history-section">
                                        <div class="history-header"
                                            onclick="toggleHistory('history_<?= $ship['tracking_id'] ?>')">
                                            <h6 class="mb-0">
                                                <i class="fa fa-chevron-down me-2"></i>
                                                Shipping History (<?= count($shipment_history) ?> events)
                                            </h6>
                                        </div>
                                        <div class="history-content" id="history_<?= $ship['tracking_id'] ?>">
                                            <!-- Add New History Event Form -->
                                            <div class="history-form">
                                                <h6>Add New History Event</h6>
                                                <form action="php/manage_shipment_history.php" method="POST">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Event Date</label>
                                                                <input type="datetime-local" name="event_date"
                                                                    class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Status</label>
                                                                <input type="text" name="status" class="form-control"
                                                                    placeholder="Status" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Location</label>
                                                                <input type="text" name="location" class="form-control"
                                                                    placeholder="Location" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Description</label>
                                                                <input type="text" name="description" class="form-control"
                                                                    placeholder="Description" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="tracking_id" value="<?= $ship['tracking_id'] ?>">
                                                    <button type="submit" name="add_history" class="btn btn-success btn-sm">
                                                        <i class="fa fa-plus"></i> Add Event
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Existing History Events -->
                                            <?php if (empty($shipment_history)): ?>
                                                <div class="text-center py-3 text-muted">
                                                    <i class="fa fa-history fa-2x mb-2"></i>
                                                    <p>No shipping history events found.</p>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($shipment_history as $history): ?>
                                                    <div class="history-item">
                                                        <div class="timeline-event">
                                                            <strong><?= $history['status'] ?></strong>
                                                            at <?= $history['location'] ?>
                                                            <br>
                                                            <small class="text-muted"><?= $history['event_date'] ?></small>
                                                            <div><?= $history['description'] ?></div>
                                                        </div>
                                                        <div class="history-actions">
                                                            <form action="php/manage_shipment_history.php" method="POST"
                                                                style="display: inline;">
                                                                <input type="hidden" name="id" value="<?= $history['id'] ?>">
                                                                <input type="hidden" name="tracking_id"
                                                                    value="<?= $ship['tracking_id'] ?>">
                                                                <button type="submit" name="delete_history"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Are you sure you want to delete this history event?');">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Payment Details Section -->
                                    <div class="payment-section">
                                        <div class="payment-header"
                                            onclick="togglePayment('payment_<?= $ship['tracking_id'] ?>')">
                                            <h6 class="mb-0">
                                                <i class="fa fa-chevron-down me-2"></i>
                                                Payment Details
                                                <?php if ($payment_details): ?>
                                                    <span class="status-badge <?=
                                                        $payment_details['payment_status'] == 'PENDING' ? 'payment-status-pending' :
                                                        ($payment_details['payment_status'] == 'PAID' ? 'payment-status-paid' : 'payment-status-failed')
                                                        ?> ms-2">
                                                        <?= $payment_details['payment_status'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                        <div class="payment-content" id="payment_<?= $ship['tracking_id'] ?>">
                                            <!-- Add/Edit Payment Details Form -->
                                            <div class="payment-form">
                                                <h6><?= $payment_details ? 'Update' : 'Add' ?> Payment Details</h6>
                                                <form action="php/manage_payment.php" method="POST">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Shipping Cost ($)</label>
                                                                <input type="number" step="0.01" name="shipping_cost"
                                                                    class="form-control"
                                                                    value="<?= $payment_details ? $payment_details['shipping_cost'] : '' ?>"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Clearance Cost ($)</label>
                                                                <input type="number" step="0.01" name="clearance_cost"
                                                                    class="form-control"
                                                                    value="<?= $payment_details ? $payment_details['clearance_cost'] : '' ?>"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Total Amount ($)</label>
                                                                <input type="number" step="0.01" name="total_amount"
                                                                    class="form-control"
                                                                    value="<?= $payment_details ? $payment_details['total_amount'] : '' ?>"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Payment Status</label>
                                                                <select name="payment_status" class="form-control" required>
                                                                    <option value="PENDING" <?= $payment_details && $payment_details['payment_status'] == 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                                                    <option value="PAID" <?= $payment_details && $payment_details['payment_status'] == 'PAID' ? 'selected' : '' ?>>PAID</option>
                                                                    <option value="FAILED" <?= $payment_details && $payment_details['payment_status'] == 'FAILED' ? 'selected' : '' ?>>FAILED</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="tracking_id" value="<?= $ship['tracking_id'] ?>">
                                                    <?php if ($payment_details): ?>
                                                        <input type="hidden" name="payment_id"
                                                            value="<?= $payment_details['id'] ?>">
                                                    <?php endif; ?>
                                                    <button type="submit"
                                                        name="<?= $payment_details ? 'update_payment' : 'add_payment' ?>"
                                                        class="btn btn-success">
                                                        <i class="fa fa-credit-card"></i>
                                                        <?= $payment_details ? 'Update' : 'Add' ?> Payment
                                                    </button>
                                                    <?php if ($payment_details): ?>
                                                        <button type="submit" name="delete_payment" class="btn btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete these payment details?');">
                                                            <i class="fa fa-trash"></i> Delete Payment
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            </div>

                                            <!-- Existing Payment Details -->
                                            <?php if ($payment_details): ?>
                                                <div class="payment-item mt-3">
                                                    <div>
                                                        <h6>Current Payment Details</h6>
                                                        <p><strong>Shipping Cost:</strong> $<?= $payment_details['shipping_cost'] ?>
                                                        </p>
                                                        <p><strong>Clearance Cost:</strong>
                                                            $<?= $payment_details['clearance_cost'] ?></p>
                                                        <p><strong>Total Amount:</strong> $<?= $payment_details['total_amount'] ?>
                                                        </p>
                                                        <p><strong>Status:</strong>
                                                            <span class="status-badge <?=
                                                                $payment_details['payment_status'] == 'PENDING' ? 'payment-status-pending' :
                                                                ($payment_details['payment_status'] == 'PAID' ? 'payment-status-paid' : 'payment-status-failed')
                                                                ?>">
                                                                <?= $payment_details['payment_status'] ?>
                                                            </span>
                                                        </p>
                                                        <p><strong>Last Updated:</strong> <?= $payment_details['updated_at'] ?></p>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-center py-3 text-muted">
                                                    <i class="fa fa-credit-card fa-2x mb-2"></i>
                                                    <p>No payment details found for this shipment.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <script>
        (function () {
            const root = document.getElementById('custom-country');
            const toggle = root.querySelector('.select-toggle');
            const options = root.querySelectorAll('.option');
            const list = root.querySelector('.options');
            const hidden = document.getElementById('disperse_country_hidden');

            toggle.addEventListener('click', () => {
                root.querySelector('.custom-select').classList.toggle('open');
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!expanded));
            });

            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    const val = opt.getAttribute('data-value');
                    toggle.firstChild.textContent = opt.textContent; // shows selected text
                    hidden.value = val;
                    root.querySelector('.custom-select').classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });

            // close when clicking outside
            document.addEventListener('click', (e) => {
                if (!root.contains(e.target)) {
                    root.querySelector('.custom-select').classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/popper.min.js%2bbootstrap.min.js.pagespeed.jc.LKWtDHPWPS.js"></script>
    <script>eval(mod_pagespeed_CUTY2hLpTk);</script>
    <script>eval(mod_pagespeed_wRQBYJaI$a);</script>
    <script src="js/jquery.magnific-popup.js%2bmasonry.pkgd.js.pagespeed.jc.7pgdBQf2rE.js"></script>
    <script>eval(mod_pagespeed_zg7eA38Qhj);</script>
    <script>eval(mod_pagespeed_tW5sxDBeWi);</script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/gijgo.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/intlInputPhone.min.js"></script>
    <script
        src="js/jquery.ajaxchimp.min.js%2bjquery.form.js%2bjquery.validate.min.js%2bmail-script.js%2bcontact.js%2bcustom.js.pagespeed.jc.gluAe1vgNY.js"></script>
    <script>eval(mod_pagespeed_TLJfk8ljIZ);</script>
    <script>eval(mod_pagespeed_93Bf1mo$HF);</script>
    <script>eval(mod_pagespeed_PvP9UDGZsA);</script>
    <script>eval(mod_pagespeed_12zv6r3LZ0);</script>
    <script>eval(mod_pagespeed_wCsNvbfkPC);</script>
    <script>eval(mod_pagespeed_nENxee0pfk);</script>

    <script>
        function toggleAddForm() {
            var form = document.getElementById('add_shipment_form');
            if (form.style.display === 'block' || form.classList.contains('d-block')) {
                form.style.display = 'none';
                form.classList.remove('d-block');
            } else {
                form.style.display = 'block';
                form.classList.add('d-block');
                // Scroll to form for better UX
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function toggleHistory(historyId) {
            var historyContent = document.getElementById(historyId);
            var historyHeader = historyContent.previousElementSibling;
            var icon = historyHeader.querySelector('i');

            if (historyContent.style.display === 'block' || historyContent.classList.contains('d-block')) {
                historyContent.style.display = 'none';
                historyContent.classList.remove('d-block');
                icon.className = 'fa fa-chevron-down me-2';
            } else {
                historyContent.style.display = 'block';
                historyContent.classList.add('d-block');
                icon.className = 'fa fa-chevron-up me-2';
                // Scroll to history section for better UX
                historyContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function togglePayment(paymentId) {
            var paymentContent = document.getElementById(paymentId);
            var paymentHeader = paymentContent.previousElementSibling;
            var icon = paymentHeader.querySelector('i');

            if (paymentContent.style.display === 'block' || paymentContent.classList.contains('d-block')) {
                paymentContent.style.display = 'none';
                paymentContent.classList.remove('d-block');
                icon.className = 'fa fa-chevron-down me-2';
            } else {
                paymentContent.style.display = 'block';
                paymentContent.classList.add('d-block');
                icon.className = 'fa fa-chevron-up me-2';
                // Scroll to payment section for better UX
                paymentContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function toggleWalletSection() {
            var walletContent = document.getElementById('wallet_content');
            var walletHeader = document.querySelector('.wallet-header');
            var icon = walletHeader.querySelector('i');

            if (walletContent.style.display === 'block' || walletContent.classList.contains('d-block')) {
                walletContent.style.display = 'none';
                walletContent.classList.remove('d-block');
                icon.className = 'fa fa-chevron-down me-2';
            } else {
                walletContent.style.display = 'block';
                walletContent.classList.add('d-block');
                icon.className = 'fa fa-chevron-up me-2';
                // Scroll to wallet section for better UX
                document.getElementById('wallet_section').scrollIntoView({ behavior: 'smooth' });
            }
        }

        function editWallet(id) {
            // This would typically make an AJAX call to get wallet details
            // For now, we'll just show a placeholder implementation
            document.getElementById('wallet_form_title').textContent = 'Edit Wallet Address';
            document.getElementById('wallet_id').value = id;

            // Scroll to the form
            document.getElementById('wallet_content').scrollIntoView({ behavior: 'smooth' });
        }

        function resetWalletForm() {
            document.getElementById('wallet_form_title').textContent = 'Add New Wallet Address';
            document.getElementById('wallet_id').value = '';
            document.querySelector('form[name="wallet_form"]').reset();
        }

        // Make shipment cards editable on double click
        document.addEventListener('DOMContentLoaded', function () {
            var cards = document.querySelectorAll('.shipment-card');
            cards.forEach(function (card) {
                card.addEventListener('dblclick', function () {
                    this.classList.toggle('edit-mode');
                });
            });

            // Update progress bar when input changes
            var progressInputs = document.querySelectorAll('input[name="perc"]');
            progressInputs.forEach(function (input) {
                input.addEventListener('input', function () {
                    var progressBar = this.parentNode.querySelector('.shipment-progress-bar');
                    if (progressBar) {
                        progressBar.style.width = this.value + '%';
                    }
                });
            });

            // Additional fix for mobile devices
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                document.querySelectorAll('select').forEach(function (select) {
                    select.addEventListener('focus', function () {
                        // On mobile, ensure the dropdown doesn't resize the page
                        document.body.style.overflow = 'hidden';
                    });

                    select.addEventListener('blur', function () {
                        document.body.style.overflow = '';
                    });
                });
            }
        });
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-23581568-13');
    </script>
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194"
        integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw=="
        data-cf-beacon='{"rayId":"6fdcd1bb28d15989","token":"cd0b4b3a733644fc843ef0b185f98241","version":"2021.12.0","si":100}'
        crossorigin="anonymous"></script>
</body>

</html>