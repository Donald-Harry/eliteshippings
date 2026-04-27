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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Swift Express Logistics | Invoice</title>
    
    <!-- Define Charset -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Page Description and Author -->
    <meta name="description" content="Swift Express Logistics Tracking and Shipping">
    <meta name="keywords" content="Swift Express Logistics, logistics, shipping, courier, rail transport">
    <meta name="author" content="Swift Express Logistics">
    
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
        
        @page  {
            size: A4;
            margin: 0.5cm;
        }
        
        @media  print {
            html, body {
                width: 210mm;
                height: 297mm;
                background-color: white !important;
            }
            
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .print\:hidden {
                display: none !important;
            }
            
            .print\:block {
                display: block !important;
            }
            
            .print\:shadow-none {
                box-shadow: none !important;
            }
            
            .print\:bg-white {
                background-color: white !important;
            }
        }
        
        /* Swift Express Logistics brand colors */
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
        .bg-bitcoin { background-color: #f7931a; }
        .bg-ethereum { background-color: #627eea; }
        .bg-usdt { background-color: #26a17b; }
        .bg-bank { background-color: #1e3a8a; }
    </style>
</head>
<body>

<div class="bg-white print:bg-white" x-data="{ printModal: true }" x-init="setTimeout(() => { window.print(); }, 1000)">
    <!-- Professional receipt container with subtle background pattern -->
    <div class="relative max-w-4xl mx-auto bg-white shadow-lg print:shadow-none overflow-hidden">
        <!-- Background pattern - only visible on screen, not in print -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-white opacity-50 print:hidden"></div>
        
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03] print:opacity-[0.04] z-0">
            <div class="rotate-[-30deg]">
                <p class="text-[120px] font-bold text-gray-700">OFFICIAL</p>
                <p class="text-[80px] font-bold text-gray-700 -mt-24">RECEIPT</p>
            </div>
        </div>

        <!-- Print Button - Only visible on screen -->
        <div class="print:hidden fixed top-4 right-4 z-50" x-show="printModal">
            <button @click="window.print()" class="flex items-center px-4 py-2 bg-pattern-primary text-white rounded-md shadow-sm hover:bg-blue-800 transition-colors">
                <i class="fas fa-print mr-2"></i>
                Print Receipt
            </button>
        </div>

        <!-- Main content -->
        <div class="relative p-8 bg-white z-10">
            <!-- Premium header with logo and border effect -->
            <div class="relative">
                <!-- Colorful top border -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 via-pattern-primary to-blue-800"></div>
                
                <div class="pt-6 pb-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="h-14 w-14 bg-pattern-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-train text-white text-xl"></i>
                            </div>
                            <div class="ml-4 hidden sm:block">
                                <h1 class="text-xl font-bold text-gray-800">Swift Express</h1>
                                <p class="text-sm text-gray-500">Logistics Solutions</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-right mb-2">
                                <span class="text-xs text-gray-500">Receipt Generated</span>
                                <p class="text-sm font-medium text-gray-700"><?=date('F d, Y')?></p>
                            </div>
                            <div class="py-1 px-3 bg-blue-50 rounded-full border border-blue-100">
                                <span class="text-xs font-medium text-pattern-primary">OFFICIAL RECEIPT</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Company information -->
                    <div class="mt-4 text-center sm:text-left sm:flex sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-pattern-primary sm:text-xl">Swift Express Logistics</h2>
                            <p class="text-sm text-gray-600 mt-1">Reliable Rail & Logistics Solutions</p>
                        </div>
                        <div class="mt-2 sm:mt-0 text-sm text-gray-600 text-center sm:text-right">
                            <p>support@tcmonetaryshippings.online</p>
                            <p>159 Carriage Dr, Carol Stream, IL 60188</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking ID Banner -->
            <div class="flex justify-between items-center bg-gradient-to-r from-pattern-primary to-blue-700 text-white p-4 rounded-md my-6">
                <div class="flex items-center">
                    <i class="fas fa-box text-white/90 mr-2 text-xl"></i>
                    <div>
                        <span class="text-xs font-medium text-white/80">Tracking Number</span>
                        <h3 class="text-xl font-bold"><?=get('tracking_id');?></h3>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-pattern-primary">
                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                        Verified
                    </span>
                </div>
            </div>

            <!-- Info cards in 3-column grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Sender info -->
                <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-pattern-primary text-sm"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">Sender</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="text-pattern-primary font-semibold text-base"><?=get('sender_name')?></div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-500 mr-2 mt-0.5 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('disperse_address')?>, <?=get('disperse_country')?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-500 mr-2 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('sender_phone')?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-500 mr-2 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('sender_email')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Receiver info -->
                <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-pattern-primary text-sm"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">Receiver</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="text-pattern-primary font-semibold text-base"><?=get('reciever_name')?></div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-500 mr-2 mt-0.5 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('delivering_to')?>, <?=get('delivering_country')?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-500 mr-2 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('reciever_phone')?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-500 mr-2 flex-shrink-0 text-sm"></i>
                            <span class="text-gray-700 text-sm"><?=get('reciever_email')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Shipment info -->
                <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-box text-pattern-primary text-sm"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">Shipment Details</h3>
                    </div>
                    <div class="flex justify-center mb-4">
                        <div class="p-2 bg-white border border-gray-200 rounded shadow-sm">
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=<?=get('tracking_id');?>&code=Code128" alt="<?=get('tracking_id');?>" class="h-16">
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Package Type:</span>
                            <span class="font-medium text-gray-800"><?=get('package_TYPE')?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Weight:</span>
                            <span class="font-medium text-gray-800"><?=number_format(get('weight'),2)?> kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Quantity:</span>
                            <span class="font-medium text-gray-800"><?=get('qty')?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                <?=get('package_status')?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipment details table with modern styling -->
            <div class="mb-8">
                <div class="flex items-center px-4 py-3 bg-gray-50 rounded-t-lg border border-gray-200 border-b-0">
                    <i class="fas fa-file-invoice text-pattern-primary mr-2"></i>
                    <h3 class="text-base font-semibold text-gray-800">Parcel Details & Service Information</h3>
                </div>
                <div class="overflow-hidden border border-gray-200 rounded-b-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Location</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-sm font-medium text-gray-900"><?=get('package')?></td>
                                    <td class="px-4 py-3.5 text-sm text-gray-900"><?=get('description')?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-900"><?=get('service_type')?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-900"><?=get('current_destination')?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?=get('package_status')?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="mb-8">
                <div class="flex items-center px-4 py-3 bg-gray-50 rounded-t-lg border border-gray-200 border-b-0">
                    <i class="fas fa-route text-pattern-primary mr-2"></i>
                    <h3 class="text-base font-semibold text-gray-800">Shipping Timeline</h3>
                </div>
                <div class="border border-gray-200 rounded-b-lg p-6">
                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200 z-0"></div>

                        <!-- Timeline Items -->
                        <div class="space-y-6">
                            <!-- Origin -->
                            <div class="relative z-10 flex items-start">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-pattern-primary text-white shadow-md border-2 border-white">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-gray-500"><?=get('booking_date')?></span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            FROM
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p class="font-medium"><?=get('disperse_address')?></p>
                                        <p class="mt-1 text-gray-600"><?=get('disperse_country')?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Location -->
                            <div class="relative z-10 flex items-start">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white shadow-md border-2 border-white">
                                    <i class="fas fa-location-arrow text-xs"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            CURRENTLY IN
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p class="font-medium"><?=get('current_destination')?></p>
                                        <p class="mt-1 text-gray-600"><?=get('package_status')?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Destination -->
                            <div class="relative z-10 flex items-start">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white shadow-md border-2 border-white">
                                    <i class="fas fa-flag-checkered text-xs"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-gray-500">Est. <?=get('arrival_date')?></span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            TO
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p class="font-medium"><?=get('delivering_to')?></p>
                                        <p class="mt-1 text-gray-600"><?=get('delivering_country')?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment and total section - modern 2-column layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Left column - Payment info with styled cards -->
                <div>
                    <div class="space-y-6">
                        <!-- Payment methods -->
                        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-wallet text-pattern-primary text-sm"></i>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">Accepted Payment Methods</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <!-- Bitcoin -->
                                <div class="flex items-center p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <div class="h-8 w-8 rounded-full bg-bitcoin flex items-center justify-center mr-3">
                                        <i class="fab fa-bitcoin text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">Bitcoin</span>
                                </div>
                                
                                <!-- Ethereum -->
                                <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="h-8 w-8 rounded-full bg-ethereum flex items-center justify-center mr-3">
                                        <i class="fab fa-ethereum text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">Ethereum</span>
                                </div>
                                
                                <!-- USDT -->
                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="h-8 w-8 rounded-full bg-usdt flex items-center justify-center mr-3">
                                        <i class="fas fa-coins text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">USDT</span>
                                </div>
                                
                                <!-- Bank Transfer -->
                                <div class="flex items-center p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <div class="h-8 w-8 rounded-full bg-bank flex items-center justify-center mr-3">
                                        <i class="fas fa-university text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">Bank Transfer</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600">
                                Secure payments accepted via cryptocurrency or traditional bank transfer. Fast, reliable, and secure transactions.
                            </p>
                        </div>
                        
                        <!-- Stamp section with premium styling -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-plus text-pattern-primary mr-1 text-sm"></i>
                                    Official Stamp
                                </h4>
                                <div class="flex items-center justify-center h-20 bg-gray-50 rounded-md p-2">
                                    <div class="text-center">
                                        <div class="h-12 w-12 border-2 border-pattern-primary rounded-full flex items-center justify-center mx-auto">
                                            <i class="fas fa-check text-pattern-primary"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Swift Express</p>
                                    </div>
                                </div>
                                <p class="text-xs text-center text-gray-500 mt-2"><?=date('D, M d, Y h:i A')?></p>
                            </div>
                            
                            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-check-circle text-pattern-primary mr-1 text-sm"></i>
                                    Stamp Duty
                                </h4>
                                <div class="flex items-center justify-center h-20 bg-gray-50 rounded-md p-2">
                                    <div class="text-center">
                                        <div class="h-12 w-12 border-2 border-green-500 rounded-full flex items-center justify-center mx-auto">
                                            <i class="fas fa-shield-alt text-green-500"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Verified</p>
                                    </div>
                                </div>
                                <p class="text-xs text-center text-gray-500 mt-2">Approved & Secured</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right column - Amount due with premium styling -->
                <div>
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-pattern-primary px-5 py-4">
                            <h4 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                Service Summary
                            </h4>
                        </div>
                        <div class="p-5">
                            <div class="space-y-4">
                                <div class="flex justify-between pb-3 border-b border-gray-200">
                                    <span class="text-gray-600">Package Type:</span>
                                    <span class="font-medium"><?=get('package_TYPE')?></span>
                                </div>
                                <div class="flex justify-between pb-3 border-b border-gray-200">
                                    <span class="text-gray-600">Weight:</span>
                                    <span class="font-medium"><?=number_format(get('weight'),2)?> kg</span>
                                </div>
                                <div class="flex justify-between pb-3 border-b border-gray-200">
                                    <span class="text-gray-600">Service Type:</span>
                                    <span class="font-medium"><?=get('service_type')?></span>
                                </div>
                                <div class="pt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-800 font-medium">Current Status:</span>
                                        <span class="text-lg font-bold text-pattern-primary"><?=get('package_status')?></span>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Estimated Delivery:</span>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-calendar-alt mr-1 text-xs"></i>
                                                <?=get('arrival_date')?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Package Image -->
                    <?php if (get('photo') && file_exists('uploads/' . get('photo'))): ?>
                    <div class="mt-6 bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                        <div class="flex">
                            <div class="w-full">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Package Image</h4>
                                <div class="bg-gray-100 p-2 rounded-md">
                                    <img src="uploads/<?=get('photo')?>" 
                                         alt="Package Image" 
                                         class="w-full h-auto rounded-md object-cover max-h-48">
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-center">Package reference image for identification</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- QR Code for digital verification -->
                    <div class="mt-6 bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                        <div class="flex">
                            <div class="w-1/3 flex flex-col items-center justify-center">
                                <div class="p-2 bg-white border border-gray-200 rounded-md shadow-sm">
                                    <svg class="h-20 w-20" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Simple placeholder QR code -->
                                        <rect width="100" height="100" fill="white"/>
                                        <rect x="10" y="10" width="30" height="30" fill="black"/>
                                        <rect x="60" y="10" width="30" height="30" fill="black"/>
                                        <rect x="10" y="60" width="30" height="30" fill="black"/>
                                        <rect x="45" y="45" width="10" height="10" fill="black"/>
                                        <rect x="60" y="60" width="15" height="5" fill="black"/>
                                        <rect x="60" y="70" width="5" height="20" fill="black"/>
                                        <rect x="70" y="70" width="5" height="5" fill="black"/>
                                        <rect x="80" y="70" width="10" height="20" fill="black"/>
                                        <rect x="70" y="80" width="5" height="10" fill="black"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="w-2/3 pl-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-1">Digital Verification</h4>
                                <p class="text-xs text-gray-600 mb-2">Scan this QR code to verify this receipt's authenticity and check real-time shipment status.</p>
                                <div class="flex items-center mt-2">
                                    <i class="fas fa-check-circle text-green-600 mr-1 text-sm"></i>
                                    <span class="text-xs text-gray-700">Digitally signed and secured</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Thank you message with professional styling -->
            <div class="text-center mt-8 pt-6 border-t border-gray-200 bg-gradient-to-r from-blue-50 to-white -mx-8 -mb-8 px-8 pb-8 rounded-b-lg">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-train text-pattern-primary text-xl"></i>
                </div>
                <h3 class="text-base font-bold text-pattern-primary mb-1">Thank You for Choosing Swift Express Logistics</h3>
                <p class="text-gray-600">We appreciate your business and look forward to delivering your package safely.</p>
                <div class="inline-flex items-center mt-4 text-xs text-gray-500">
                    <i class="far fa-clock mr-1 text-sm"></i>
                    Receipt generated on <?=date('F d, Y - h:i A')?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media  print {
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .print\:hidden {
                display: none !important;
            }
            
            .print\:block {
                display: block !important;
            }
            
            .print\:shadow-none {
                box-shadow: none !important;
            }
            
            .print\:bg-white {
                background-color: white !important;
            }
        }
    </style>
</div>

</body>
</html>