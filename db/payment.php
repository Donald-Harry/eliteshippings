<?php
include 'php/functions.php';
session_start();

if (isset($_POST['tracking_id']) || isset($_SESSION['tracking_id'])) {
    $tracking_id = sanitize($_POST['tracking_id']);

    if (isset($_SESSION['tracking_id']) && empty($_POST['tracking_id'])) {
        $tracking_id = $_SESSION['tracking_id'];
    }

    $sql = "SELECT * FROM info WHERE tracking_id = '$tracking_id'";
    $result = $db->query($sql);

    if ($result->num_rows !== 0) {
        $row = $result->fetch_assoc();
        $_SESSION['tracking_id'] = $row['tracking_id'];

        if ($row['type'] == 1) {
            echo "<script>window.location.href='admin.php'</script>";
            exit();
        }

    } else {
        // Tracking ID not found → trigger modal display
        $_SESSION['tracking_error'] = "Tracking ID Not Found";
        echo "<script>window.location.href='../track.php?error=1';</script>";
        exit();
    }

} else {
    $_SESSION['errorr'] = "Please Enter tracking id";
    echo '<script>window.location.href="track.php?#that_input";</script>';
    exit();
}

// Get payment details from database
function get_payment_details($tracking_id)
{
    global $db;
    $sql = "SELECT * FROM payments WHERE tracking_id = '$tracking_id'";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

// Get wallet addresses from database
function get_wallet_addresses()
{
    global $db;
    $sql = "SELECT * FROM wallet_addresses WHERE status = 1";
    $result = $db->query($sql);
    $wallets = [];
    while ($row = $result->fetch_assoc()) {
        $wallets[$row['crypto_type']] = $row;
    }
    return $wallets;
}

$payment_details = get_payment_details($tracking_id);
$wallet_addresses = get_wallet_addresses();

// If no payment details found in database, use defaults
if (!$payment_details) {
    $payment_details = [
        'shipping_cost' => 700.00,
        'clearance_cost' => 370.00,
        'total_amount' => 1070.00,
        'payment_status' => 'PENDING'
    ];
} else {
    // Convert database payment status to display format
    $status_map = [
        'PENDING' => 'Awaiting Payment',
        'PAID' => 'Payment Received',
        'FAILED' => 'Payment Failed'
    ];
    $payment_details['payment_status_display'] = $status_map[$payment_details['payment_status']] ?? 'Awaiting Payment';
}

// Helper function to get shipment data - RENAMED to avoid conflict
function get_shipment_data($field)
{
    global $row;
    return isset($row[$field]) ? $row[$field] : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Eliteshippings Logistics | Payment</title>

    <!-- Define Charset -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page Description and Author -->
    <meta name="description" content="Eliteshippings Logistics Payment and Clearance">
    <meta name="keywords"
        content="Eliteshippings Logistics, payment, clearance, courier, crypto, bitcoin, ethereum, usdt">
    <meta name="author" content="Eliteshippings Logistics">

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        /* Eliteshippings Logistics brand colors */
        .bg-pattern-primary {
            background-color: #1e3a8a;
        }

        .text-pattern-primary {
            color: #1e3a8a;
        }

        .border-pattern-primary {
            border-color: #1e3a8a;
        }

        /* Crypto colors */
        .bg-bitcoin {
            background-color: #f7931a;
        }

        .bg-ethereum {
            background-color: #627eea;
        }

        .bg-usdt {
            background-color: #26a17b;
        }

        .text-bitcoin {
            color: #f7931a;
        }

        .text-ethereum {
            color: #627eea;
        }

        .text-usdt {
            color: #26a17b;
        }

        /* Payment status colors */
        .payment-status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .payment-status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .payment-status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Payment form animations */
        .payment-card {
            transition: all 0.3s ease;
        }

        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .payment-card.selected {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
        }

        /* Crypto QR code styling */
        .crypto-qr {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            background: white;
        }

        .wallet-address {
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.5rem;
            border-radius: 0.25rem;
            word-break: break-all;
            font-size: 0.875rem;
        }

        /* Countdown timer */
        .countdown {
            font-family: monospace;
            font-weight: bold;
            color: #ef4444;
        }

        /* Table styles */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-table th,
        .payment-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .payment-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
    </style>
</head>

<body>

    <div class="bg-white" x-data="paymentApp()">
        <!-- Professional payment container with subtle background pattern -->
        <div class="relative max-w-6xl mx-auto bg-white shadow-lg overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-white opacity-50"></div>

            <!-- Main content -->
            <div class="relative p-8 bg-white z-10">
                <!-- Premium header with logo and border effect -->
                <div class="relative">
                    <!-- Colorful top border -->
                    <div
                        class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 via-pattern-primary to-blue-800">
                    </div>

                    <div class="pt-6 pb-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="h-14 w-14 bg-pattern-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-train text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h1 class="text-xl font-bold text-gray-800">Eliteshippings</h1>
                                    <p class="text-sm text-gray-500">Logistics Solutions</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <div class="text-right mb-2">
                                    <span class="text-xs text-gray-500">Payment Requested</span>
                                    <p class="text-sm font-medium text-gray-700"><?= date('F d, Y') ?></p>
                                </div>
                                <div class="py-1 px-3 bg-blue-50 rounded-full border border-blue-100">
                                    <span class="text-xs font-medium text-pattern-primary">SECURE PAYMENT</span>
                                </div>
                            </div>
                        </div>

                        <!-- Company information -->
                        <div class="mt-4 text-center sm:text-left sm:flex sm:justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-pattern-primary sm:text-xl">Clearance Fee Payment</h2>
                                <p class="text-sm text-gray-600 mt-1">Complete your payment to release your shipment</p>
                            </div>
                            <div class="mt-2 sm:mt-0 text-sm text-gray-600 text-center sm:text-right">
                                <p>support@tcmonetaryshippings.online</p>
                                <p>159 Carriage Dr, Carol Stream, IL 60188</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking ID Banner -->
                <div
                    class="flex justify-between items-center bg-gradient-to-r from-pattern-primary to-blue-700 text-white p-4 rounded-md my-6">
                    <div class="flex items-center">
                        <i class="fas fa-box text-white/90 mr-2 text-xl"></i>
                        <div>
                            <span class="text-xs font-medium text-white/80">Tracking Number</span>
                            <h3 class="text-xl font-bold"><?= get_shipment_data('tracking_id'); ?></h3>
                        </div>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-pattern-primary">
                            <i class="fas fa-lock mr-1 text-xs"></i>
                            Secure Payment
                        </span>
                    </div>
                </div>

                <!-- Payment Summary from Database -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div class="bg-pattern-primary px-5 py-4">
                        <h4 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                            Payment Summary
                        </h4>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto">
                            <table class="payment-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Amount (USD)</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Shipping Cost</td>
                                        <td>$<?= number_format($payment_details['shipping_cost'], 2) ?></td>
                                        <td>
                                            <?php if ($payment_details['payment_status'] == 'PAID'): ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-paid">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Paid
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-pending">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y') ?></td>
                                    </tr>
                                    <tr>
                                        <td>Clearance Fee</td>
                                        <td>$<?= number_format($payment_details['clearance_cost'], 2) ?></td>
                                        <td>
                                            <?php if ($payment_details['payment_status'] == 'PAID'): ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-paid">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Paid
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-pending">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y') ?></td>
                                    </tr>
                                    <tr class="bg-gray-50 font-semibold">
                                        <td>Total Amount Due</td>
                                        <td class="text-pattern-primary text-lg">
                                            $<?= number_format($payment_details['total_amount'], 2) ?></td>
                                        <td>
                                            <?php if ($payment_details['payment_status'] == 'PAID'): ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-paid">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Payment Received
                                                </span>
                                            <?php elseif ($payment_details['payment_status'] == 'FAILED'): ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-failed">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Payment Failed
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium payment-status-pending">
                                                    Awaiting Payment
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Additional Payment Information -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-blue-50 p-3 rounded-md">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-info-circle text-pattern-primary mr-2"></i>
                                    <span class="font-medium">Payment Reference</span>
                                </div>
                                <p class="text-gray-600"><?= get_shipment_data('tracking_id'); ?></p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-md">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                                    <span class="font-medium">Payment Method</span>
                                </div>
                                <p class="text-gray-600">Cryptocurrency / Bank Transfer</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-md">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-clock text-purple-600 mr-2"></i>
                                    <span class="font-medium">Processing Time</span>
                                </div>
                                <p class="text-gray-600">Instant (Crypto) / 1-3 days (Bank)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($payment_details['payment_status'] != 'PAID'): ?>
                    <!-- Wallet Addresses Table -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-8">
                        <div class="flex items-center px-5 py-4 bg-gray-50 border-b border-gray-200">
                            <i class="fas fa-wallet text-pattern-primary mr-2"></i>
                            <h3 class="text-base font-semibold text-gray-800">Company Wallet Addresses</h3>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="payment-table">
                                    <thead>
                                        <tr>
                                            <th>Cryptocurrency</th>
                                            <th>Wallet Address</th>
                                            <th>Network</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($wallet_addresses['Bitcoin'])): ?>
                                            <tr>
                                                <td class="flex items-center">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-bitcoin flex items-center justify-center mr-3">
                                                        <i class="fab fa-bitcoin text-white text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">Bitcoin (BTC)</span>
                                                </td>
                                                <td>
                                                    <div class="wallet-address">
                                                        <?= !empty($wallet_addresses['Bitcoin']['wallet_address']) ? $wallet_addresses['Bitcoin']['wallet_address'] : 'Contact Support' ?>
                                                    </div>
                                                </td>
                                                <td><?= $wallet_addresses['Bitcoin']['network'] ?? 'BTC Network' ?></td>
                                                <td>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($wallet_addresses['Bitcoin']['wallet_address'])): ?>
                                                        <button
                                                            @click="copyToClipboard('<?= $wallet_addresses['Bitcoin']['wallet_address'] ?>')"
                                                            class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                            <i class="fas fa-copy mr-1"></i> Copy
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (isset($wallet_addresses['Ethereum'])): ?>
                                            <tr>
                                                <td class="flex items-center">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-ethereum flex items-center justify-center mr-3">
                                                        <i class="fab fa-ethereum text-white text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">Ethereum (ETH)</span>
                                                </td>
                                                <td>
                                                    <div class="wallet-address">
                                                        <?= !empty($wallet_addresses['Ethereum']['wallet_address']) ? $wallet_addresses['Ethereum']['wallet_address'] : 'Contact Support' ?>
                                                    </div>
                                                </td>
                                                <td><?= $wallet_addresses['Ethereum']['network'] ?? 'ERC-20' ?></td>
                                                <td>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($wallet_addresses['Ethereum']['wallet_address'])): ?>
                                                        <button
                                                            @click="copyToClipboard('<?= $wallet_addresses['Ethereum']['wallet_address'] ?>')"
                                                            class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                            <i class="fas fa-copy mr-1"></i> Copy
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (isset($wallet_addresses['USDT'])): ?>
                                            <tr>
                                                <td class="flex items-center">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-usdt flex items-center justify-center mr-3">
                                                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">USDT</span>
                                                </td>
                                                <td>
                                                    <div class="wallet-address">
                                                        <?= !empty($wallet_addresses['USDT']['wallet_address']) ? $wallet_addresses['USDT']['wallet_address'] : 'Contact Support' ?>
                                                    </div>
                                                </td>
                                                <td><?= $wallet_addresses['USDT']['network'] ?? 'ERC-20' ?></td>
                                                <td>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($wallet_addresses['USDT']['wallet_address'])): ?>
                                                        <button
                                                            @click="copyToClipboard('<?= $wallet_addresses['USDT']['wallet_address'] ?>')"
                                                            class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                            <i class="fas fa-copy mr-1"></i> Copy
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (empty($wallet_addresses)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-gray-500">
                                                    <i class="fas fa-wallet text-2xl mb-2 block"></i>
                                                    No wallet addresses configured. Please contact support.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 bg-blue-50 p-4 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Important Notice</h4>
                                        <div class="mt-1 text-sm text-blue-700">
                                            <p>Always send payments to the official wallet addresses listed above. Include
                                                your tracking number (<?= get_shipment_data('tracking_id'); ?>) in the
                                                payment
                                                memo/notes for proper credit.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-8">
                        <div class="flex items-center px-4 py-3 bg-gray-50 rounded-t-lg border border-gray-200 border-b-0">
                            <i class="fas fa-coins text-pattern-primary mr-2"></i>
                            <h3 class="text-base font-semibold text-gray-800">Select Payment Method</h3>
                        </div>
                        <div class="border border-gray-200 rounded-b-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- Bitcoin -->
                                <?php if (isset($wallet_addresses['Bitcoin']) && !empty($wallet_addresses['Bitcoin']['wallet_address'])): ?>
                                    <div class="payment-card bg-white rounded-lg p-4 border border-gray-200 shadow-sm cursor-pointer"
                                        :class="selectedMethod === 'bitcoin' ? 'selected' : ''"
                                        @click="selectedMethod = 'bitcoin'">
                                        <div class="flex items-center mb-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-bitcoin flex items-center justify-center mr-3">
                                                <i class="fab fa-bitcoin text-white"></i>
                                            </div>
                                            <h4 class="text-base font-semibold text-gray-800">Bitcoin (BTC)</h4>
                                        </div>
                                        <p class="text-xs text-gray-600">Pay with Bitcoin cryptocurrency</p>
                                        <div class="flex mt-3">
                                            <div class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded">
                                                <i class="fab fa-bitcoin mr-1"></i>
                                                Fast & Secure
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Ethereum -->
                                <?php if (isset($wallet_addresses['Ethereum']) && !empty($wallet_addresses['Ethereum']['wallet_address'])): ?>
                                    <div class="payment-card bg-white rounded-lg p-4 border border-gray-200 shadow-sm cursor-pointer"
                                        :class="selectedMethod === 'ethereum' ? 'selected' : ''"
                                        @click="selectedMethod = 'ethereum'">
                                        <div class="flex items-center mb-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-ethereum flex items-center justify-center mr-3">
                                                <i class="fab fa-ethereum text-white"></i>
                                            </div>
                                            <h4 class="text-base font-semibold text-gray-800">Ethereum (ETH)</h4>
                                        </div>
                                        <p class="text-xs text-gray-600">Pay with Ethereum cryptocurrency</p>
                                        <div class="flex mt-3">
                                            <div class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                                <i class="fab fa-ethereum mr-1"></i>
                                                ERC-20 Network
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- USDT -->
                                <?php if (isset($wallet_addresses['USDT']) && !empty($wallet_addresses['USDT']['wallet_address'])): ?>
                                    <div class="payment-card bg-white rounded-lg p-4 border border-gray-200 shadow-sm cursor-pointer"
                                        :class="selectedMethod === 'usdt' ? 'selected' : ''" @click="selectedMethod = 'usdt'">
                                        <div class="flex items-center mb-3">
                                            <div class="h-10 w-10 rounded-full bg-usdt flex items-center justify-center mr-3">
                                                <i class="fas fa-dollar-sign text-white"></i>
                                            </div>
                                            <h4 class="text-base font-semibold text-gray-800">USDT</h4>
                                        </div>
                                        <p class="text-xs text-gray-600">Pay with Tether (USDT) stablecoin</p>
                                        <div class="flex mt-3">
                                            <div class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">
                                                <i class="fas fa-coins mr-1"></i>
                                                Stable Value
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Bank Transfer -->
                                <div class="payment-card bg-white rounded-lg p-4 border border-gray-200 shadow-sm cursor-pointer"
                                    :class="selectedMethod === 'bank' ? 'selected' : ''" @click="selectedMethod = 'bank'">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-university text-pattern-primary"></i>
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-800">Bank Transfer</h4>
                                    </div>
                                    <p class="text-xs text-gray-600">Direct bank transfer</p>
                                    <div class="flex mt-3">
                                        <div class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded">
                                            <i class="fas fa-university mr-1"></i>
                                            Traditional Banking
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bitcoin Payment -->
                            <?php if (isset($wallet_addresses['Bitcoin']) && !empty($wallet_addresses['Bitcoin']['wallet_address'])): ?>
                                <div x-show="selectedMethod === 'bitcoin'" class="mt-6">
                                    <div class="bg-orange-50 p-4 rounded-md mb-6">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fab fa-bitcoin text-orange-500 text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-orange-800">Bitcoin Payment</h3>
                                                <div class="mt-1 text-sm text-orange-700">
                                                    <p>Send the exact amount of Bitcoin to the address below. Payment will be
                                                        confirmed after 3 network confirmations.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Details</h4>
                                            <div class="space-y-3 bg-gray-50 p-4 rounded-md">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Amount (USD):</span>
                                                    <span
                                                        class="text-sm font-medium">$<?= number_format($payment_details['total_amount'], 2) ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">BTC Amount:</span>
                                                    <span class="text-sm font-medium"
                                                        x-text="calculateCryptoAmount('bitcoin')"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Exchange Rate:</span>
                                                    <span class="text-sm font-medium">1 BTC = $<span
                                                            x-text="cryptoRates.bitcoin"></span></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Network Fee:</span>
                                                    <span class="text-sm font-medium">Included</span>
                                                </div>
                                            </div>

                                            <div class="mt-4 bg-amber-50 p-3 rounded-md">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                                    </div>
                                                    <div class="ml-2">
                                                        <p class="text-xs text-amber-700">
                                                            <span class="font-medium">Time Sensitive:</span>
                                                            This payment address expires in <span class="countdown"
                                                                x-text="formatTime(timeRemaining)"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Send Payment To</h4>
                                            <div class="crypto-qr text-center">
                                                <!-- QR Code Placeholder -->
                                                <div class="flex justify-center mb-3">
                                                    <div
                                                        class="w-48 h-48 bg-white border border-gray-300 flex items-center justify-center">
                                                        <div class="text-center">
                                                            <i class="fab fa-bitcoin text-4xl text-orange-500 mb-2"></i>
                                                            <p class="text-xs text-gray-500">Bitcoin QR Code</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-2">Scan QR code or copy address below</p>
                                                <div class="wallet-address mb-3">
                                                    <?= $wallet_addresses['Bitcoin']['wallet_address'] ?>
                                                </div>
                                                <button
                                                    @click="copyToClipboard('<?= $wallet_addresses['Bitcoin']['wallet_address'] ?>')"
                                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                    <i class="fas fa-copy mr-1"></i> Copy Address
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Instructions</h4>
                                        <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                                            <li>Send exactly <span class="font-medium"
                                                    x-text="calculateCryptoAmount('bitcoin')"></span> BTC to the address above
                                            </li>
                                            <li>Include the transaction fee in your payment (do not deduct)</li>
                                            <li>Wait for 3 network confirmations (approx. 30 minutes)</li>
                                            <li>Your shipment will be released automatically after confirmation</li>
                                        </ol>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Ethereum Payment -->
                            <?php if (isset($wallet_addresses['Ethereum']) && !empty($wallet_addresses['Ethereum']['wallet_address'])): ?>
                                <div x-show="selectedMethod === 'ethereum'" class="mt-6">
                                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fab fa-ethereum text-blue-500 text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">Ethereum Payment</h3>
                                                <div class="mt-1 text-sm text-blue-700">
                                                    <p>Send the exact amount of Ethereum to the address below. Payment will be
                                                        confirmed after 12 network confirmations.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Details</h4>
                                            <div class="space-y-3 bg-gray-50 p-4 rounded-md">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Amount (USD):</span>
                                                    <span
                                                        class="text-sm font-medium">$<?= number_format($payment_details['total_amount'], 2) ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">ETH Amount:</span>
                                                    <span class="text-sm font-medium"
                                                        x-text="calculateCryptoAmount('ethereum')"></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Exchange Rate:</span>
                                                    <span class="text-sm font-medium">1 ETH = $<span
                                                            x-text="cryptoRates.ethereum"></span></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Gas Fee:</span>
                                                    <span class="text-sm font-medium">Paid by sender</span>
                                                </div>
                                            </div>

                                            <div class="mt-4 bg-amber-50 p-3 rounded-md">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                                    </div>
                                                    <div class="ml-2">
                                                        <p class="text-xs text-amber-700">
                                                            <span class="font-medium">Time Sensitive:</span>
                                                            This payment address expires in <span class="countdown"
                                                                x-text="formatTime(timeRemaining)"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Send Payment To</h4>
                                            <div class="crypto-qr text-center">
                                                <!-- QR Code Placeholder -->
                                                <div class="flex justify-center mb-3">
                                                    <div
                                                        class="w-48 h-48 bg-white border border-gray-300 flex items-center justify-center">
                                                        <div class="text-center">
                                                            <i class="fab fa-ethereum text-4xl text-blue-500 mb-2"></i>
                                                            <p class="text-xs text-gray-500">Ethereum QR Code</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-2">Scan QR code or copy address below</p>
                                                <div class="wallet-address mb-3">
                                                    <?= $wallet_addresses['Ethereum']['wallet_address'] ?>
                                                </div>
                                                <button
                                                    @click="copyToClipboard('<?= $wallet_addresses['Ethereum']['wallet_address'] ?>')"
                                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                    <i class="fas fa-copy mr-1"></i> Copy Address
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Instructions</h4>
                                        <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                                            <li>Send exactly <span class="font-medium"
                                                    x-text="calculateCryptoAmount('ethereum')"></span> ETH to the address above
                                            </li>
                                            <li>Ensure you have enough ETH to cover gas fees</li>
                                            <li>Wait for 12 network confirmations (approx. 3-5 minutes)</li>
                                            <li>Your shipment will be released automatically after confirmation</li>
                                        </ol>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- USDT Payment -->
                            <?php if (isset($wallet_addresses['USDT']) && !empty($wallet_addresses['USDT']['wallet_address'])): ?>
                                <div x-show="selectedMethod === 'usdt'" class="mt-6">
                                    <div class="bg-green-50 p-4 rounded-md mb-6">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-dollar-sign text-green-500 text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-green-800">USDT Payment</h3>
                                                <div class="mt-1 text-sm text-green-700">
                                                    <p>Send the exact amount of USDT to the address below. Payment will be
                                                        confirmed after 12 network confirmations.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Details</h4>
                                            <div class="space-y-3 bg-gray-50 p-4 rounded-md">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Amount (USD):</span>
                                                    <span
                                                        class="text-sm font-medium">$<?= number_format($payment_details['total_amount'], 2) ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">USDT Amount:</span>
                                                    <span
                                                        class="text-sm font-medium"><?= number_format($payment_details['total_amount'], 2) ?>
                                                        USDT</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Network:</span>
                                                    <span class="text-sm font-medium">ERC-20</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Gas Fee:</span>
                                                    <span class="text-sm font-medium">Paid by sender</span>
                                                </div>
                                            </div>

                                            <div class="mt-4 bg-amber-50 p-3 rounded-md">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                                    </div>
                                                    <div class="ml-2">
                                                        <p class="text-xs text-amber-700">
                                                            <span class="font-medium">Time Sensitive:</span>
                                                            This payment address expires in <span class="countdown"
                                                                x-text="formatTime(timeRemaining)"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Send Payment To</h4>
                                            <div class="crypto-qr text-center">
                                                <!-- QR Code Placeholder -->
                                                <div class="flex justify-center mb-3">
                                                    <div
                                                        class="w-48 h-48 bg-white border border-gray-300 flex items-center justify-center">
                                                        <div class="text-center">
                                                            <i class="fas fa-dollar-sign text-4xl text-green-500 mb-2"></i>
                                                            <p class="text-xs text-gray-500">USDT QR Code</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-2">Scan QR code or copy address below</p>
                                                <div class="wallet-address mb-3">
                                                    <?= $wallet_addresses['USDT']['wallet_address'] ?>
                                                </div>
                                                <button
                                                    @click="copyToClipboard('<?= $wallet_addresses['USDT']['wallet_address'] ?>')"
                                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-2 rounded">
                                                    <i class="fas fa-copy mr-1"></i> Copy Address
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Instructions</h4>
                                        <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                                            <li>Send exactly <?= number_format($payment_details['total_amount'], 2) ?> USDT to
                                                the
                                                address above</li>
                                            <li>Use ERC-20 network only (do not use other networks)</li>
                                            <li>Ensure you have enough ETH to cover gas fees</li>
                                            <li>Wait for 12 network confirmations (approx. 3-5 minutes)</li>
                                            <li>Your shipment will be released automatically after confirmation</li>
                                        </ol>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Bank Transfer -->
                            <div x-show="selectedMethod === 'bank'" class="mt-6">
                                <div class="bg-blue-50 p-4 rounded-md mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-university text-blue-600 text-lg"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800">Bank Transfer</h3>
                                            <div class="mt-1 text-sm text-blue-700">
                                                <p>Please use the following bank details to complete your payment.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-md mb-6">
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Bank Name:</span>
                                            <span class="text-sm font-medium">Contact Support (24/7 Live Chat)</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Account Name:</span>
                                            <span class="text-sm font-medium">Contact Support (24/7 Live Chat)</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Account Number:</span>
                                            <span class="text-sm font-medium">Contact Support (24/7 Live Chat)</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Routing Number:</span>
                                            <span class="text-sm font-medium">Contact Support (24/7 Live Chat)</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">SWIFT/BIC:</span>
                                            <span class="text-sm font-medium">Contact Support (24/7 Live Chat)</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Amount:</span>
                                            <span class="text-sm font-medium text-pattern-primary">USD
                                                <?= number_format($payment_details['total_amount'], 2) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Reference:</span>
                                            <span
                                                class="text-sm font-medium text-pattern-primary"><?= get_shipment_data('tracking_id'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-amber-50 p-4 rounded-md mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-amber-500 text-lg"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-amber-800">Important</h3>
                                            <div class="mt-1 text-sm text-amber-700">
                                                <p>Please include your tracking number
                                                    (<?= get_shipment_data('tracking_id'); ?>) as the payment reference.
                                                    Your
                                                    shipment will be released once we receive confirmation of your payment
                                                    (1-3 business days).</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Package Information -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-8">
                    <div class="flex items-center px-5 py-4 bg-gray-50 border-b border-gray-200">
                        <i class="fas fa-box-open text-pattern-primary mr-2"></i>
                        <h3 class="text-base font-semibold text-gray-800">Package Information</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Sender Details</h4>
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium">Name:</span>
                                        <?= get_shipment_data('sender_name') ?></p>
                                    <p class="text-sm"><span class="font-medium">Address:</span>
                                        <?= get_shipment_data('disperse_address') ?>,
                                        <?= get_shipment_data('disperse_country') ?>
                                    </p>
                                    <p class="text-sm"><span class="font-medium">Phone:</span>
                                        <?= get_shipment_data('sender_phone') ?></p>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Receiver Details</h4>
                                <div class="space-y-2">
                                    <p class="text-sm"><span class="font-medium">Name:</span>
                                        <?= get_shipment_data('reciever_name') ?></p>
                                    <p class="text-sm"><span class="font-medium">Address:</span>
                                        <?= get_shipment_data('delivering_to') ?>,
                                        <?= get_shipment_data('delivering_country') ?>
                                    </p>
                                    <p class="text-sm"><span class="font-medium">Phone:</span>
                                        <?= get_shipment_data('reciever_phone') ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if (get_shipment_data('photo') && file_exists('uploads/' . get_shipment_data('photo'))): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Package Image</h4>
                                <div class="bg-gray-100 p-2 rounded-md max-w-xs">
                                    <img src="uploads/<?= get_shipment_data('photo') ?>" alt="Package Image"
                                        class="w-full h-auto rounded-md object-cover">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($payment_details['payment_status'] != 'PAID'): ?>
                    <!-- Security & Support -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-3">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-shield-alt text-green-600"></i>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">Payment Security</h4>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                    <span>Blockchain verified transactions</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                    <span>One-time payment addresses</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                    <span>Automatic payment confirmation</span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-headset text-pattern-primary"></i>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">Need Help?</h4>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p>Our support team is available 24/7 to assist you with any payment issues.</p>
                                <div class="flex items-center mt-3">
                                    <i class="fas fa-comments text-pattern-primary mr-2"></i>
                                    <span class="font-medium">24/7 Live support</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-pattern-primary mr-2"></i>
                                    <span class="font-medium">support@tcmonetaryshippings.online</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function paymentApp() {
            return {
                selectedMethod: '<?php echo (isset($wallet_addresses['Bitcoin']) && !empty($wallet_addresses['Bitcoin']['wallet_address'])) ? 'bitcoin' : ((isset($wallet_addresses['Ethereum']) && !empty($wallet_addresses['Ethereum']['wallet_address'])) ? 'ethereum' : ((isset($wallet_addresses['USDT']) && !empty($wallet_addresses['USDT']['wallet_address'])) ? 'usdt' : 'bank')); ?>',
                timeRemaining: 1800, // 30 minutes in seconds
                cryptoRates: {
                    bitcoin: 101000,
                    ethereum: 3400,
                    usdt: 1
                },

                init() {
                    // Start countdown timer
                    this.startCountdown();

                    // Simulate fetching live crypto rates (in a real app, this would be an API call)
                    setInterval(() => {
                        // Small random fluctuations to simulate live rates
                        this.cryptoRates.bitcoin = 101000 + (Math.random() * 100 - 50);
                        this.cryptoRates.ethereum = 3400 + (Math.random() * 10 - 5);
                    }, 10000);
                },

                startCountdown() {
                    const timer = setInterval(() => {
                        if (this.timeRemaining > 0) {
                            this.timeRemaining--;
                        } else {
                            clearInterval(timer);
                            // In a real application, you would refresh the payment address
                        }
                    }, 1000);
                },

                formatTime(seconds) {
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                },

                calculateCryptoAmount(crypto) {
                    const amount = <?= $payment_details['total_amount'] ?> / this.cryptoRates[crypto];
                    return amount.toFixed(8);
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        // Show a temporary success message
                        const button = event.target;
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
                        button.classList.remove('bg-gray-200', 'hover:bg-gray-300');
                        button.classList.add('bg-green-200', 'text-green-800');

                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.classList.remove('bg-green-200', 'text-green-800');
                            button.classList.add('bg-gray-200', 'hover:bg-gray-300');
                        }, 2000);
                    });
                }
            }
        }
    </script>

</body>

</html>