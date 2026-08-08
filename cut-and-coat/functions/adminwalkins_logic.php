<?php
session_start();

// If called via AJAX, start output buffering to prevent accidental HTML/PHP warnings
if(!empty($_POST['ajax'])){
    ob_start();
}

if(!isset($_SESSION['admin'])){
header("Location: adminlogin.php");
exit();
}

if(!isset($_SESSION['walkins'])){
$_SESSION['walkins'] = [];
}

$staffFile = __DIR__ . '/json/staff_data.json';
$_SESSION['staff_status'] = [];

if(file_exists($staffFile)){
    $staffData = json_decode(file_get_contents($staffFile), true);
    if(is_array($staffData)){
        foreach($staffData as $staff){
            $name = trim((string)($staff['name'] ?? ''));
            if($name === '') continue;
            $_SESSION['staff_status'][$name] = isset($staff['status']) ? (string)$staff['status'] : 'Available';
        }
    }
}

if(empty($_SESSION['staff_status'])){
    $_SESSION['staff_status'] = [
        "Anna" => "Available",
        "Rhea" => "Busy",
        "Kim" => "Available"
    ];
}

$servicesFile = __DIR__ . '/json/services_data.json';
$servicePrices = [];
$serviceSections = [];
if(file_exists($servicesFile)){
    $serviceJson = json_decode(file_get_contents($servicesFile), true);
    if(is_array($serviceJson)){
        foreach($serviceJson as $section){
            if(isset($section['title']) && isset($section['services']) && is_array($section['services'])){
                $serviceSection = [
                    'title' => $section['title'],
                    'services' => []
                ];
                foreach($section['services'] as $service){
                    if(isset($service['name'], $service['price'])){
                        $serviceName = $service['name'];
                        $servicePrice = (int)$service['price'];
                        $servicePrices[$serviceName] = $servicePrice;
                        $serviceSection['services'][] = [
                            'name' => $serviceName,
                            'price' => $servicePrice
                        ];
                    }
                }
                if(!empty($serviceSection['services'])){
                    $serviceSections[] = $serviceSection;
                }
            }
        }
    }
}
if(empty($servicePrices)){
    $servicePrices = [
        "Classic Manicure"=>300,
        "Classic Pedicure"=>350,
        "Gel Manicure"=>500,
        "Gel Pedicure"=>550,
        "Nail Art Basic"=>600,
        "Nail Art Premium"=>900,
        "Acrylic Full Set"=>1200,
        "Gel Extensions"=>1500,
        "Foot Spa"=>400,
        "Hand Spa"=>350
    ];
    $serviceSections = [[
        'title' => 'Services',
        'services' => array_map(function($name, $price){ return ['name' => $name, 'price' => $price]; }, array_keys($servicePrices), $servicePrices)
    ]];
}

$total = 0;
$down = 0;
$balance = 0;
$serviceLabel = '';

$availabilityFile = __DIR__ . '/json/availability_data.json';
$availabilityData = [];
if(file_exists($availabilityFile)){
    $availabilityData = json_decode(file_get_contents($availabilityFile), true);
    if(!is_array($availabilityData)){
        $availabilityData = [];
    }
}

$timeSlots = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00'];

foreach($availabilityData as $date => $meta){
    if(!is_array($meta)){
        continue;
    }

    $bookedTimes = [];
    if(isset($meta['bookedTimes']) && is_array($meta['bookedTimes'])){
        foreach($meta['bookedTimes'] as $time){
            $cleanTime = trim((string)$time);
            if($cleanTime === '') continue;
            // Normalize to hour slot (e.g. 13:17 -> 13:00) so matching works with defined slots
            $ts = strtotime($cleanTime);
            if($ts !== false){
                $slot = date('H:00', $ts);
                $bookedTimes[] = $slot;
            } else {
                $bookedTimes[] = $cleanTime;
            }
        }
    } elseif(isset($meta['status']) && $meta['status'] === 'unavailable'){
        $bookedTimes = $timeSlots;
    }

    $bookedTimes = array_values(array_unique($bookedTimes));
    $availableTimes = array_values(array_diff($timeSlots, $bookedTimes));
    sort($bookedTimes);
    sort($availableTimes);

    $availabilityData[$date] = [
        'status' => empty($availableTimes) ? 'unavailable' : 'available',
        'bookedTimes' => $bookedTimes,
        'availableTimes' => $availableTimes,
        'reason' => empty($availableTimes) ? 'Fully booked' : 'Open'
    ];
}

$walkinsDataFile = __DIR__ . '/json/walkins_data.json';
if(file_exists($walkinsDataFile)){
    $existingWalkins = json_decode(file_get_contents($walkinsDataFile), true);
    if(is_array($existingWalkins)){
        foreach($existingWalkins as $booking){
            if(isset($booking['date']) && !empty($booking['date'])){
                $bookingDateTime = trim((string)$booking['date']);
                $bookingDate = $bookingDateTime;
                $bookingTime = '';

                if(preg_match('/^(\d{4}-\d{2}-\d{2})\s+(\d{1,2}:\d{2})$/', $bookingDateTime, $matches)){
                    $bookingDate = $matches[1];
                    $bookingTime = $matches[2];
                } else {
                    $bookingDate = preg_replace('/\s+.+$/', '', $bookingDateTime);
                }

                if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $bookingDate)){
                    if(!isset($availabilityData[$bookingDate])){
                        $availabilityData[$bookingDate] = [
                            'status' => 'available',
                            'bookedTimes' => [],
                            'availableTimes' => $timeSlots,
                            'reason' => 'Open'
                        ];
                    }

                    if($bookingTime !== ''){
                        // normalize booking time to nearest hour slot
                        $btTs = strtotime($bookingTime);
                        if($btTs !== false){
                            $slotTime = date('H:00', $btTs);
                        } else {
                            $slotTime = $bookingTime;
                        }
                        $availabilityData[$bookingDate]['bookedTimes'][] = $slotTime;
                    } else {
                        $availabilityData[$bookingDate]['bookedTimes'] = $timeSlots;
                    }
                }
            }
        }
    }
}

foreach($availabilityData as $date => $meta){
    if(!is_array($meta)){
        continue;
    }

    $bookedTimes = $meta['bookedTimes'] ?? [];
    $bookedTimes = array_values(array_unique(array_filter(array_map(function($time){ return trim((string)$time); }, $bookedTimes), function($time){ return $time !== ''; })));
    $availableTimes = array_values(array_diff($timeSlots, $bookedTimes));
    sort($bookedTimes);
    sort($availableTimes);

    $availabilityData[$date] = [
        'status' => empty($availableTimes) ? 'unavailable' : 'available',
        'bookedTimes' => $bookedTimes,
        'availableTimes' => $availableTimes,
        'reason' => empty($availableTimes) ? 'Fully booked' : 'Open'
    ];
}

if(!empty($availabilityData)){
    file_put_contents($availabilityFile, json_encode($availabilityData, JSON_PRETTY_PRINT));
}

$availabilityLookup = [];
foreach($availabilityData as $date => $meta){
    $availabilityLookup[$date] = $meta;
}

if(isset($_POST['submit'])){

$selectedServices = $_POST['services'] ?? [];
$selectedServices = array_values(array_filter(array_map('trim', (array) $selectedServices), function($value){ return $value !== ''; }));

$serviceNames = [];
foreach($selectedServices as $serviceName){
    if(isset($servicePrices[$serviceName])){
        $serviceNames[] = $serviceName;
        $total += $servicePrices[$serviceName];
    }
}

$serviceLabel = implode(', ', $serviceNames);
$down = $total * 0.5;
$balance = $total - $down;

if(!empty($serviceNames)){
    $booking = [
        "name" => $_POST['name'],
        "phone" => $_POST['phone'],
        "email" => $_POST['email'],
        "date" => $_POST['date'],
        "staff" => $_POST['staff'],
        "service" => $serviceLabel,
        "services" => $serviceNames,
        "total" => $total,
        "payment" => $_POST['payment'],
        "reference" => $_POST['reference'],
        "status" => "upcoming",
        "method" => "walkin"
    ];

    $_SESSION['walkins'][] = $booking;

    $walkinsFile = __DIR__ . '/json/walkins_data.json';
    $walkinsDir = dirname($walkinsFile);
    if(!is_dir($walkinsDir)){
        mkdir($walkinsDir, 0777, true);
    }

    $existingWalkins = [];
    if(file_exists($walkinsFile)){
        $existingWalkins = json_decode(file_get_contents($walkinsFile), true);
        if(!is_array($existingWalkins)){
            $existingWalkins = [];
        }
    }

    $existingWalkins[] = $booking;
    file_put_contents($walkinsFile, json_encode($existingWalkins, JSON_PRETTY_PRINT));

    $bookingDate = date('Y-m-d', strtotime($booking['date']));
    $bookingTime = date('H:i', strtotime($booking['date']));

    if(!isset($availabilityData[$bookingDate])){
        $availabilityData[$bookingDate] = [
            'status' => 'available',
            'bookedTimes' => [],
            'availableTimes' => $timeSlots,
            'reason' => 'Open'
        ];
    }

    // normalize booking time to hour slot when storing
    $btTs = strtotime($bookingTime);
    if($btTs !== false){
        $slot = date('H:00', $btTs);
    } else {
        $slot = $bookingTime;
    }
    $availabilityData[$bookingDate]['bookedTimes'][] = $slot;
    $bookedTimes = array_values(array_unique(array_filter(array_map(function($time){ return trim((string)$time); }, $availabilityData[$bookingDate]['bookedTimes']), function($time){ return $time !== ''; })));
    $availableTimes = array_values(array_diff($timeSlots, $bookedTimes));
    sort($bookedTimes);
    sort($availableTimes);

    $availabilityData[$bookingDate] = [
        'status' => empty($availableTimes) ? 'unavailable' : 'available',
        'bookedTimes' => $bookedTimes,
        'availableTimes' => $availableTimes,
        'reason' => empty($availableTimes) ? 'Fully booked' : 'Open'
    ];
    file_put_contents($availabilityFile, json_encode($availabilityData, JSON_PRETTY_PRINT));

    $salesFile = __DIR__ . '/json/sales_data.json';
    $existingSales = [];
    $currentTotalSales = 0;
    if(file_exists($salesFile)){
        $salesRaw = json_decode(file_get_contents($salesFile), true);
        if(is_array($salesRaw)){
            if(isset($salesRaw['records']) && is_array($salesRaw['records'])){
                $existingSales = $salesRaw['records'];
                $currentTotalSales = isset($salesRaw['totalSales']) ? (int)$salesRaw['totalSales'] : 0;
                if($currentTotalSales === 0){
                    foreach($existingSales as $sale){
                        $currentTotalSales += isset($sale['amount']) ? (int)$sale['amount'] : 0;
                    }
                }
            } else {
                $existingSales = $salesRaw;
                foreach($existingSales as $sale){
                    $currentTotalSales += isset($sale['amount']) ? (int)$sale['amount'] : 0;
                }
            }
        }
    }

    $newSale = [
        "type" => "Walk-In",
        "payment" => $_POST['payment'],
        "amount" => $total,
        "date" => $bookingDate
    ];
    $existingSales[] = $newSale;
    $currentTotalSales += $total;

    file_put_contents($salesFile, json_encode([
        'totalSales' => $currentTotalSales,
        'records' => $existingSales
    ], JSON_PRETTY_PRINT));

    $customersFile = __DIR__ . '/json/customers_data.json';
    if(!is_dir(dirname($customersFile))){
        mkdir(dirname($customersFile), 0777, true);
    }

    $existingCustomers = [];
    if(file_exists($customersFile)){
        $existingCustomers = json_decode(file_get_contents($customersFile), true);
        if(!is_array($existingCustomers)){
            $existingCustomers = [];
        }
    }

    $existingCustomers[] = [
        "name" => $_POST['name'],
        "phone" => $_POST['phone'],
        "staff" => $_POST['staff'],
        "service" => $serviceLabel,
        "datetime" => $_POST['date'],
        "type" => "Walk-In",
        "payment" => $_POST['payment']
    ];

    file_put_contents($customersFile, json_encode($existingCustomers, JSON_PRETTY_PRINT));
}
    // If this request was made via AJAX, return receipt JSON and stop further output
    if(!empty($_POST['ajax'])){
        // Clean any buffered output (warnings, notices) so we return pure JSON
        if(ob_get_length() !== false){ @ob_end_clean(); }

        $receiptResponse = [
            'name' => $booking['name'],
            'phone' => $booking['phone'],
            'email' => $booking['email'],
            'staff' => $booking['staff'],
            'date' => $booking['date'],
            'payment' => $booking['payment'],
            'reference' => $booking['reference'],
            'services' => $booking['services'],
            'total' => $booking['total'],
            'down' => $booking['total'] * 0.5,
            'balance' => $booking['total'] - ($booking['total'] * 0.5)
        ];
        header('Content-Type: application/json');
        echo json_encode($receiptResponse);
        exit();
    }
}
?>
