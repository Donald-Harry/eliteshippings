<?php
include 'php/functions.php';
session_start();

include '../livechat.php';


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
            echo "<script>window.location.href='admin.php';</script>";
            exit();
        }

    } else {

        echo "<script>
                alert('Tracking ID not found. Please check and try again.');
                window.location.href = '../index.php';
              </script>";
        exit();
    }

} else {

    echo "<script>
            alert('Please enter a tracking ID.');
            window.location.href = '../index.php';
          </script>";
    exit();
}


$history_sql = "SELECT `id`, `tracking_id`, `event_date`, `status`, `location`, `description` 
                FROM `shipment_history` 
                WHERE tracking_id = '$tracking_id' 
                ORDER BY event_date DESC";
$history_result = $db->query($history_sql);
$shipping_history = [];

if ($history_result->num_rows > 0) {
    while ($history_row = $history_result->fetch_assoc()) {
        $shipping_history[] = $history_row;
    }
}
?>


<!DOCTYPE html>
<html dir="ltr" lang="en-US" class="scroll-smooth dark">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="description"
        content="Swift Express Logistics - Premium Worldwide Logistics and Rail Transport Services" />
    <meta name="keywords"
        content="logistics, rail transport, shipping, freight, courier, transport, global delivery, package tracking" />
    <meta name="author" content="Swift Express Logistics" />
    <meta name="robots" content="index, follow" />
    <meta name="google-site-verification" content="" />

    <!-- Modern Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Leaflet Awesome Markers -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-awesome-markers/2.0.2/leaflet.awesome-markers.css">

    <link href="img/xbps.png" rel="shortcut icon">
    <title>Track Shipment - Swift Express Logistics</title>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        rail: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Alpine.js x-cloak directive */
        [x-cloak] {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .skiptranslate iframe {
            visibility: hidden !important;
        }

        /* Custom scrollbar for dark mode */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }

        /* Custom Swift Express Logistics colors */
        .bg-rail-primary {
            background-color: #0ea5e9;
        }

        .text-rail-primary {
            color: #0ea5e9;
        }

        .border-rail-primary {
            border-color: #0ea5e9;
        }

        /* Map container styling */
        #currentLocationMap,
        #routeMap {
            position: relative !important;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .leaflet-container {
            position: absolute !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        /* Enhanced map controls */
        .leaflet-control-zoom a {
            background: white !important;
            color: #333 !important;
            border: 1px solid #ccc !important;
        }

        .dark .leaflet-control-zoom a {
            background: #374151 !important;
            color: white !important;
            border: 1px solid #4b5563 !important;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }

        .dark .leaflet-popup-content-wrapper {
            background: #1f2937;
            color: white;
        }

        /* Route animation */
        @keyframes dash {
            to {
                stroke-dashoffset: -100;
            }
        }

        .animated-route {
            stroke-dasharray: 10;
            animation: dash 60s linear infinite;
        }

        /* Pulsing marker effect */
        @keyframes pulse-marker {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pulse-marker {
            animation: pulse-marker 2s infinite;
        }

        /* Floating animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Pulse animation */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.4);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(14, 165, 233, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(14, 165, 233, 0);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body class="font-inter bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-data="{ loading: true }"
    x-cloak>
    <!-- iOS-Compatible Preloader -->
    <div id="preloader" class="fixed inset-0 bg-white dark:bg-gray-900 z-[9999] flex items-center justify-center">
        <div class="text-center">
            <div class="w-24 h-24 bg-rail-primary rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-train text-white text-2xl"></i>
            </div>
            <div class="mt-4 flex items-center justify-center space-x-2">
                <div class="w-3 h-3 bg-rail-primary rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                <div class="w-3 h-3 bg-rail-primary rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                <div class="w-3 h-3 bg-rail-primary rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12"
        x-init="setTimeout(() => { loading = false; document.getElementById('preloader').style.display = 'none'; }, 800)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Loading State -->
            <div x-show="loading"
                class="flex flex-col items-center justify-center p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="w-16 h-16 border-4 border-rail-primary border-t-transparent rounded-full animate-spin mb-4">
                </div>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">
                    Fetching Result for Tracking Number: <?= get('tracking_id'); ?>...
                </h4>
            </div>

            <!-- Tracking Result Container -->
            <div x-show="!loading" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100">

                <!-- Header & Navigation -->
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-train text-rail-primary text-xl"></i>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Shipment Tracking</h1>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <a href="/" class="flex items-center hover:text-rail-primary transition-colors">
                                <i class="fas fa-home mr-1"></i>
                                Home
                            </a>
                            <span>/</span>
                            <span>Tracking</span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="/"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-rail-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>

                <!-- Tracking Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                    <div
                        class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-rail-primary to-blue-600">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="mb-4 md:mb-0">
                                <div class="flex items-center">
                                    <i class="fas fa-box text-white/90 mr-2 text-xl"></i>
                                    <h2 class="text-xl font-bold text-white">Tracking Number</h2>
                                </div>
                                <div
                                    class="mt-2 font-mono text-2xl md:text-3xl font-bold text-white opacity-90 flex items-center">
                                    <?= get('tracking_id'); ?>
                                    <span
                                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white text-rail-primary">
                                        Active
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <div
                                    class="flex items-center px-4 py-2 bg-white/20 backdrop-filter backdrop-blur-sm rounded-lg mb-2">
                                    <i class="fas fa-info-circle text-white mr-2"></i>
                                    <span class="text-white font-medium">Current Status:</span>
                                    <div
                                        class="ml-2 px-2 py-0.5 rounded-full bg-blue-500 text-white text-sm font-medium">
                                        <?= get('package_status') ?>
                                    </div>
                                </div>
                                <div class="flex items-center text-white text-sm">
                                    <i class="far fa-clock mr-2 opacity-80"></i>
                                    <!-- <span class="opacity-90">Last Updated: <?= get('booking_date') ?></span> -->
                                    <!-- <span class="opacity-90">Last Updated:
                                        <?= date('M d, Y - h:i A', strtotime($event['event_date'])) ?></span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Shipment Information -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Sender Information -->
                        <div
                            class="space-y-3 bg-blue-50/60 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center text-rail-primary mb-2">
                                <i class="fas fa-user mr-2"></i>
                                <h3 class="text-base font-semibold">Sender Information</h3>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200 break-words"><?= get('sender_name') ?></span>
                                </div>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('disperse_address') ?>,
                                        <?= get('disperse_country') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('sender_phone') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('sender_email') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Receiver Information -->
                        <div
                            class="space-y-3 bg-blue-50/60 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center text-rail-primary mb-2">
                                <i class="fas fa-user mr-2"></i>
                                <h3 class="text-base font-semibold">Receiver Information</h3>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200 break-words"><?= get('reciever_name') ?></span>
                                </div>
                                <div class="flex items-start">
                                    <i
                                        class="fas fa-map-marker-alt text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('delivering_to') ?>,
                                        <?= get('delivering_country') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('reciever_phone') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300 break-words"><?= get('reciever_email') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Shipment Details -->
                        <div
                            class="space-y-3 bg-blue-50/60 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center text-rail-primary mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                <h3 class="text-base font-semibold">Shipment Details</h3>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-weight-hanging text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span class="text-gray-500 dark:text-gray-400 mr-2">Weight:</span>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200"><?= number_format(get('weight'), 2) ?>
                                        kg</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-box text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span class="text-gray-500 dark:text-gray-400 mr-2">Type:</span>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200"><?= get('package_TYPE') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i
                                        class="far fa-calendar-alt text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span class="text-gray-500 dark:text-gray-400 mr-2">Shipped:</span>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200"><?= get('booking_date') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Information -->
                        <div
                            class="space-y-3 bg-blue-50/60 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center text-rail-primary mb-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <h3 class="text-base font-semibold">Status Information</h3>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-truck text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span class="text-gray-500 dark:text-gray-400 mr-2">Status:</span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        <?= get('package_status') ?>
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-location-arrow text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0"></i>
                                    <span class="text-gray-500 dark:text-gray-400 mr-2">Location:</span>
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200"><?= get('current_destination') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipment Progress Tracker -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Shipment Progress</h3>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <!-- Progress Lines -->
                        <div class="hidden md:flex absolute top-1/2 left-0 w-full h-1 transform -translate-y-1/2 z-0">
                            <!-- Line 1: Order Confirmed to Picked -->
                            <div class="h-full flex-1 bg-rail-primary"></div>

                            <!-- Line 2: Picked to On The Way -->
                            <div class="h-full flex-1 bg-rail-primary"></div>

                            <!-- Line 3: On The Way to Custom Hold -->
                            <div class="h-full flex-1 bg-blue-500"></div>

                            <!-- Line 4: Custom Hold to Delivered -->
                            <div class="h-full flex-1 bg-green-500"></div>
                        </div>

                        <!-- Mobile Progress Line with Colored Segments -->
                        <div class="md:hidden absolute top-0 left-8 h-full z-0 flex flex-col">
                            <!-- Line 1: To Picked -->
                            <div class="w-1 flex-1 bg-rail-primary"></div>

                            <!-- Line 2: To On The Way -->
                            <div class="w-1 flex-1 bg-rail-primary"></div>

                            <!-- Line 3: To Custom Hold -->
                            <div class="w-1 flex-1 bg-blue-500"></div>

                            <!-- Line 4: To Delivered -->
                            <div class="w-1 flex-1 bg-green-500"></div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="flex flex-col md:flex-row justify-between relative z-10">
                            <!-- Step 1: Order Confirmed -->
                            <div class="flex md:block md:text-center mb-8 md:mb-0">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:mx-auto rounded-full bg-rail-primary text-white shadow-lg border-4 border-white dark:border-gray-800">
                                    <i class="fas fa-check text-xl"></i>
                                </div>
                                <div class="ml-4 md:ml-0 md:mt-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Order Confirmed</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= date('M j, Y', strtotime(get('booking_date'))) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Step 2: Picked by Courier -->
                            <div class="flex md:block md:text-center mb-8 md:mb-0">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:mx-auto rounded-full bg-rail-primary text-white shadow-lg border-4 border-white dark:border-gray-800">
                                    <i class="fas fa-truck text-xl"></i>
                                </div>
                                <div class="ml-4 md:ml-0 md:mt-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Picked by Courier
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= date('M j, Y', strtotime(get('booking_date'))) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Step 3: On The Way -->
                            <div class="flex md:block md:text-center mb-8 md:mb-0">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:mx-auto rounded-full bg-rail-primary text-white shadow-lg border-4 border-white dark:border-gray-800">
                                    <i class="fas fa-shipping-fast text-xl"></i>
                                </div>
                                <div class="ml-4 md:ml-0 md:mt-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">On The Way</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">In Transit</p>
                                </div>
                            </div>

                            <!-- Step 4: Custom Hold -->
                            <div class="flex md:block md:text-center mb-8 md:mb-0">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:mx-auto rounded-full bg-blue-500 text-white shadow-lg border-4 border-white dark:border-gray-800">
                                    <i class="fas fa-pause text-xl"></i>
                                </div>
                                <div class="ml-4 md:ml-0 md:mt-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Current Location
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= get('current_destination') ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Step 5: Delivered -->
                            <div class="flex md:block md:text-center">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:mx-auto rounded-full bg-green-500 text-white shadow-lg border-4 border-white dark:border-gray-800">
                                    <i class="fas fa-flag-checkered text-xl"></i>
                                </div>
                                <div class="ml-4 md:ml-0 md:mt-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Delivered</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= date('M j, Y', strtotime(get('arrival_date'))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Left Column: Shipping Timeline & History -->
                <div class="lg:order-1 space-y-6">
                    <!-- Shipping Timeline -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-stream text-rail-primary mr-2"></i>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Shipping Timeline</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Default timeline -->
                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-rail-primary text-white shadow-md border-2 border-white dark:border-gray-800">
                                        <i class="fas fa-map-marker-alt text-xs"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <span
                                                class="text-xs font-medium text-gray-500 dark:text-gray-400"><?= get('booking_date') ?></span>
                                        </div>
                                        <div class="mt-1">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                FROM
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            <p class="font-medium"><?= get('disperse_address') ?></p>
                                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                                <?= get('disperse_country') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white shadow-md border-2 border-white dark:border-gray-800">
                                        <i class="fas fa-location-arrow text-xs"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="mt-1">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                CURRENTLY IN
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            <p class="font-medium"><?= get('current_destination') ?></p>
                                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                                <?= get('package_status') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white shadow-md border-2 border-white dark:border-gray-800">
                                        <i class="fas fa-flag-checkered text-xs"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Est.
                                                <?= get('arrival_date') ?></span>
                                        </div>
                                        <div class="mt-1">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                TO
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            <p class="font-medium"><?= get('delivering_to') ?></p>
                                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                                <?= get('delivering_country') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping History -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-history text-rail-primary mr-2"></i>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Shipping History</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <!-- Timeline Line -->
                                <div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200 dark:bg-gray-600 z-0"></div>

                                <!-- Timeline Items -->
                                <div class="space-y-6">
                                    <?php if (!empty($shipping_history)): ?>
                                        <?php foreach ($shipping_history as $index => $event): ?>
                                            <div class="relative z-10 flex items-start">
                                                <div
                                                    class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full 
                                                        <?= $index === 0 ? 'bg-green-500' : 'bg-rail-primary' ?> 
                                                        text-white shadow-md border-2 border-white dark:border-gray-800">
                                                    <?php if ($index === 0): ?>
                                                        <i class="fas fa-check text-xs"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-info text-xs"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-center">
                                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                            <?= date('M d, Y - h:i A', strtotime($event['event_date'])) ?>
                                                        </span>
                                                    </div>
                                                    <div class="mt-1">
                                                        <span
                                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                <?= $index === 0 ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' ?>">
                                                            <?= htmlspecialchars($event['status']) ?>
                                                        </span>
                                                    </div>
                                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <p class="font-medium"><?= htmlspecialchars($event['location']) ?></p>
                                                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                                                            <?= htmlspecialchars($event['description']) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default history if no history available -->
                                        <div class="text-center py-8">
                                            <i class="fas fa-history text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                                            <h4 class="text-lg font-medium text-gray-600 dark:text-gray-400">No Shipping
                                                History Available</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Tracking updates will
                                                appear here as your shipment progresses.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center/Right Column: Package Route Map -->
                <div class="lg:order-2 lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div
                        class="flex items-center px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <i class="fas fa-route text-rail-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Package Journey Map</h3>
                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">Live Global Tracking</span>
                    </div>
                    <div class="p-0">
                        <div class="relative h-[600px]">
                            <!-- Leaflet.js map for route visualization -->
                            <div id="routeMap" class="w-full h-full overflow-hidden"></div>

                            <!-- Map Controls Overlay -->
                            <div class="absolute top-4 right-4 flex flex-col space-y-2">
                                <button id="fitBoundsBtn"
                                    class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-expand text-gray-700 dark:text-gray-300"></i>
                                </button>
                                <button id="toggleSatelliteBtn"
                                    class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-satellite text-gray-700 dark:text-gray-300"></i>
                                </button>
                            </div>

                            <!-- Route Info Overlay -->
                            <div
                                class="absolute bottom-4 left-4 bg-white dark:bg-gray-800 bg-opacity-95 dark:bg-opacity-95 backdrop-filter backdrop-blur-sm p-4 rounded-lg shadow-lg max-w-sm border border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Shipment Route
                                    Details</h4>

                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-500 border-2 border-white dark:border-gray-800 shadow-sm flex items-center justify-center mt-0.5">
                                            <span class="text-white text-xs font-bold">A</span>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white">Origin Point
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <?= get('disperse_country') ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 h-6 w-6 rounded-full bg-rail-primary border-2 border-white dark:border-gray-800 shadow-sm flex items-center justify-center mt-0.5 pulse-marker">
                                            <span class="text-white text-xs font-bold">B</span>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white">Current
                                                Location</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <?= get('current_destination') ?>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Status: <?= get('package_status') ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 h-6 w-6 rounded-full bg-green-500 border-2 border-white dark:border-gray-800 shadow-sm flex items-center justify-center mt-0.5">
                                            <span class="text-white text-xs font-bold">C</span>
                                        </div>
                                        <div class="ml-2">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white">Destination</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <?= get('delivering_country') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-route text-rail-primary mr-1 text-xs"></i>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Estimated
                                                Distance:</span>
                                        </div>
                                        <span id="route_distance"
                                            class="text-xs font-semibold text-rail-primary">Calculating...</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="far fa-clock text-rail-primary mr-1 text-xs"></i>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Est.
                                                Time:</span>
                                        </div>
                                        <span id="route_time"
                                            class="text-xs font-semibold text-rail-primary"><?= get('arrival_date') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-box-open text-rail-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Package Details</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row">
                        <?php if (get('photo') && file_exists('uploads/' . get('photo'))): ?>
                            <div class="md:w-1/4 mb-6 md:mb-0 md:pr-6">
                                <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                                    <img src="uploads/<?= get('photo') ?>" class="w-full h-auto rounded-lg object-cover"
                                        alt="Parcel photo">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div
                            class="<?= (get('photo') && file_exists('uploads/' . get('photo'))) ? 'md:w-3/4' : 'w-full' ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-user text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Sender's Name
                                        </h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('sender_name') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-user text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Receiver's Name
                                        </h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('reciever_name') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-box text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Package</h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('package') ?></p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-align-left text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Description</h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('description') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-barcode text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Product ID</h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('tracking_id') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-tag text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Package Type
                                        </h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('package_TYPE') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-cubes text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Quantity</h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300"><?= get('qty') ?></p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-weight-hanging text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Weight</h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300"><?= number_format(get('weight'), 2) ?>
                                        kg
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-shipping-fast text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Service Type
                                        </h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 break-words"><?= get('service_type') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-info-circle text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Status</h4>
                                    </div>
                                    <p
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        <?= get('package_status') ?>
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="far fa-calendar-alt text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Booking Date
                                        </h4>
                                    </div>
                                    <!-- <p class="text-gray-700 dark:text-gray-300"><?= get('booking_date') ?></p> -->
                                    <p class="text-gray-700 dark:text-gray-300">
                                        <?= date('M j, Y', strtotime(get('booking_date'))) ?>
                                    </p>

                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-2">
                                        <i class="far fa-calendar-check text-rail-primary mr-2"></i>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Arrival Date
                                        </h4>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">
                                        <?= date('M j, Y', strtotime(get('arrival_date'))) ?>
                                    </p>

                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-3 justify-center md:justify-start">
                                <a href="print_receipt.php?tracking_id=<?= get('tracking_id'); ?>"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-rail-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                                    target="_blank">
                                    <i class="fas fa-print mr-2"></i>
                                    Print Receipt
                                </a>

                                <a href="payment.php?tracking_id=<?= get('tracking_id'); ?>"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    Pay Clearance Fee
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Location Map Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div
                    class="flex items-center px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <i class="fas fa-map-marker-alt text-rail-primary mr-2"></i>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Current Location</h3>
                </div>
                <div class="p-0">
                    <div class="relative h-[400px]">
                        <div id="currentLocationMap" class="w-full h-full overflow-hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modern Footer -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
        <!-- Main Footer Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-2 text-white">Swift Express Logistics</h3>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Premium worldwide rail logistics and transport services with headquarters and branches across
                        the globe.
                        We deliver excellence in rail shipping, logistics services, and package tracking with our global
                        network of trusted rail partners.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-rail-primary rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-rail-primary rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-rail-primary rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-rail-primary rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-white">Quick Links</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="/" class="text-gray-300 hover:text-blue-400 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-blue-500"></i>
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="about"
                                class="text-gray-300 hover:text-blue-400 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-blue-500"></i>
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="services"
                                class="text-gray-300 hover:text-blue-400 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-blue-500"></i>
                                Our Services
                            </a>
                        </li>
                        <li>
                            <a href="order"
                                class="text-gray-300 hover:text-blue-400 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-blue-500"></i>
                                Track Shipment
                            </a>
                        </li>
                        <li>
                            <a href="contact"
                                class="text-gray-300 hover:text-blue-400 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-blue-500"></i>
                                Contact Us
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-white">Contact Info</h4>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-8 h-8 bg-rail-primary rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Address</p>
                                <p class="text-white">159 Carriage Dr, Carol Stream, IL 60188</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-8 h-8 bg-rail-primary rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Support</p>
                                <a href="#" class="text-white hover:text-blue-400 transition-colors">Live Chat
                                    Support</a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-8 h-8 bg-rail-primary rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-envelope text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Email Us</p>
                                <a href="mailto:support@eliteswiftship.online"
                                    class="text-white hover:text-blue-400 transition-colors break-words">
                                    support@eliteswiftship.online
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Track -->
                    <div class="mt-8">
                        <h5 class="text-sm font-semibold mb-3 text-white">Quick Track</h5>
                        <form method="POST" action="" class="space-y-2">
                            <input type="text" name="trackingnumber" placeholder="Enter tracking number..."
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm">
                            <button type="submit"
                                class="w-full bg-rail-primary text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-search mr-1"></i>Track
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                    <div class="text-center md:text-left">
                        <p class="text-gray-400 text-sm">
                            Copyright &copy; <span id="currentYear"></span> EliteShippings Logistics Services Limited.
                            All rights reserved.
                        </p>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Shipping Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Leaflet Awesome Markers -->
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-awesome-markers/2.0.2/leaflet.awesome-markers.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Set current year for copyright
            document.getElementById('currentYear').textContent = new Date().getFullYear();

            // Sample coordinates for demonstration (in real app, these would come from your database)
            const locationData = {
                origin: {
                    name: "<?= get('disperse_country') ?>",
                    address: "<?= get('disperse_address') ?>",
                    coords: [40.7128, -74.0060] // Default to New York
                },
                current: {
                    name: "<?= get('current_destination') ?>",
                    status: "<?= get('package_status') ?>",
                    coords: [34.0522, -118.2437] // Default to Los Angeles
                },
                destination: {
                    name: "<?= get('delivering_country') ?>",
                    address: "<?= get('delivering_to') ?>",
                    coords: [51.5074, -0.1278] // Default to London
                }
            };

            // Initialize Route Map with world view
            var routeMap = L.map('routeMap', {
                zoomControl: true,
                attributionControl: true,
                minZoom: 2,
                maxZoom: 18
            }).setView([30, 0], 3);

            // Base layers
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            });

            var satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google'
            });

            var darkLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                maxZoom: 19
            });

            // Add default layer
            osmLayer.addTo(routeMap);

            // Layer control
            var baseMaps = {
                "Street Map": osmLayer,
                "Satellite": satelliteLayer,
                "Dark Mode": darkLayer
            };

            L.control.layers(baseMaps).addTo(routeMap);

            // Function to geocode location names to coordinates
            async function geocodeLocation(locationName) {
                if (!locationName || locationName.trim() === '') {
                    return null;
                }

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationName)}&limit=1`);
                    const data = await response.json();

                    if (data && data.length > 0) {
                        return {
                            lat: parseFloat(data[0].lat),
                            lon: parseFloat(data[0].lon),
                            name: locationName
                        };
                    }
                    return null;
                } catch (error) {
                    console.error('Geocoding error:', error);
                    return null;
                }
            }

            // Custom icon creation function
            function createCustomIcon(color, letter, isPulsing = false) {
                const className = isPulsing ? 'pulse-marker' : '';
                return L.divIcon({
                    className: `custom-marker ${className}`,
                    html: `
                        <div style="
                            background: ${color};
                            width: 30px;
                            height: 30px;
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: bold;
                            color: white;
                            font-size: 14px;
                        ">${letter}</div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
            }

            // Main function to initialize the map with real locations
            async function initializeMap() {
                // Geocode all locations
                const [originCoords, currentCoords, destinationCoords] = await Promise.all([
                    geocodeLocation("<?= get('disperse_country') ?>"),
                    geocodeLocation("<?= get('current_destination') ?>"),
                    geocodeLocation("<?= get('delivering_country') ?>")
                ]);

                // Use geocoded coordinates or fallback to defaults
                const origin = originCoords ? [originCoords.lat, originCoords.lon] : locationData.origin.coords;
                const current = currentCoords ? [currentCoords.lat, currentCoords.lon] : locationData.current.coords;
                const destination = destinationCoords ? [destinationCoords.lat, destinationCoords.lon] : locationData.destination.coords;

                // Create marker layer group
                const markers = L.layerGroup().addTo(routeMap);

                // Add origin marker
                const originMarker = L.marker(origin, {
                    icon: createCustomIcon('#3b82f6', 'A')
                }).addTo(markers);

                originMarker.bindPopup(`
                    <div class="text-sm">
                        <strong>🚂 Origin</strong><br>
                        <b>Location:</b> ${locationData.origin.name}<br>
                        <b>Address:</b> ${locationData.origin.address}<br>
                        <b>Date:</b> <?= get('booking_date') ?>
                    </div>
                `);

                // Add current location marker (with pulsing effect)
                const currentMarker = L.marker(current, {
                    icon: createCustomIcon('#0ea5e9', 'B', true)
                }).addTo(markers);

                currentMarker.bindPopup(`
                    <div class="text-sm">
                        <strong>📍 Current Location</strong><br>
                        <b>Location:</b> ${locationData.current.name}<br>
                        <b>Status:</b> ${locationData.current.status}<br>
                        <b>Last Update:</b> <?= get('booking_date') ?>
                    </div>
                `).openPopup();

                // Add destination marker
                const destinationMarker = L.marker(destination, {
                    icon: createCustomIcon('#10b981', 'C')
                }).addTo(markers);

                destinationMarker.bindPopup(`
                    <div class="text-sm">
                        <strong>🏁 Destination</strong><br>
                        <b>Location:</b> ${locationData.destination.name}<br>
                        <b>Address:</b> ${locationData.destination.address}<br>
                        <b>Est. Arrival:</b> <?= get('arrival_date') ?>
                    </div>
                `);

                // Create animated route line
                const routePoints = [origin, current, destination];
                const routeLine = L.polyline(routePoints, {
                    color: '#0ea5e9',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '10, 10',
                    className: 'animated-route'
                }).addTo(routeMap);

                // Add completed route segment (origin to current)
                const completedRoute = L.polyline([origin, current], {
                    color: '#10b981',
                    weight: 3,
                    opacity: 0.7
                }).addTo(routeMap);

                // Fit map to show all markers with padding
                const group = new L.featureGroup([originMarker, currentMarker, destinationMarker]);
                routeMap.fitBounds(group.getBounds().pad(0.1));

                // Calculate and display distance
                const totalDistance = calculateDistance(origin, current) + calculateDistance(current, destination);
                const completedDistance = calculateDistance(origin, current);
                const progress = (completedDistance / totalDistance) * 100;

                document.getElementById("route_distance").textContent = `${Math.round(totalDistance)} km`;
                document.getElementById("route_time").textContent = `Progress: ${Math.round(progress)}%`;

                // Add progress visualization
                L.control.attribution({
                    position: 'bottomright',
                    prefix: `Progress: ${Math.round(progress)}% • Distance: ${Math.round(totalDistance)} km`
                }).addTo(routeMap);

                // Map control buttons functionality
                document.getElementById('fitBoundsBtn').addEventListener('click', function () {
                    routeMap.fitBounds(group.getBounds().pad(0.1));
                });

                document.getElementById('toggleSatelliteBtn').addEventListener('click', function () {
                    if (routeMap.hasLayer(osmLayer)) {
                        routeMap.removeLayer(osmLayer);
                        satelliteLayer.addTo(routeMap);
                    } else if (routeMap.hasLayer(satelliteLayer)) {
                        routeMap.removeLayer(satelliteLayer);
                        darkLayer.addTo(routeMap);
                    } else {
                        routeMap.removeLayer(darkLayer);
                        osmLayer.addTo(routeMap);
                    }
                });
            }

            // Helper function to calculate distance between two coordinates (simplified)
            function calculateDistance(coord1, coord2) {
                const R = 6371; // Earth's radius in km
                const dLat = (coord2[0] - coord1[0]) * Math.PI / 180;
                const dLon = (coord2[1] - coord1[1]) * Math.PI / 180;
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(coord1[0] * Math.PI / 180) * Math.cos(coord2[0] * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            // Initialize the map
            initializeMap().catch(error => {
                console.error('Error initializing map:', error);
                document.getElementById("route_distance").textContent = "Unable to load route";
            });

            // Initialize Current Location Map
            var currentLocationMap = L.map('currentLocationMap', {
                zoomControl: false,
                attributionControl: false
            }).setView([0, 0], 3);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(currentLocationMap);

            // Add zoom control
            L.control.zoom({
                position: 'topright'
            }).addTo(currentLocationMap);

            // Geocode the current destination
            var countryName = "<?= get('current_destination') ?>";

            if (countryName) {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(countryName))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            var latitude = parseFloat(data[0].lat);
                            var longitude = parseFloat(data[0].lon);

                            // Custom icon
                            var customIcon = L.divIcon({
                                className: 'custom-icon',
                                html: '<div style="background: #0ea5e9; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px #0ea5e9;"></div>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            });

                            // Add marker with pulse animation
                            var marker = L.marker([latitude, longitude], {
                                icon: customIcon
                            }).addTo(currentLocationMap)
                                .bindPopup("<b>Current Location</b><br>" + countryName)
                                .openPopup();

                            // Add pulsing circle effect
                            L.circle([latitude, longitude], {
                                color: '#0ea5e9',
                                fillColor: '#0ea5e9',
                                fillOpacity: 0.2,
                                radius: 50000
                            }).addTo(currentLocationMap);

                            // Set view to marker location
                            currentLocationMap.setView([latitude, longitude], 6);
                        } else {
                            console.log('Location not found');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Auto-refresh map every 30 seconds for live tracking feel
            setInterval(() => {
                // In a real application, this would fetch updated coordinates from your server
                console.log('Map auto-refreshed for live tracking');
            }, 30000);
        });
    </script>

</body>

</html>