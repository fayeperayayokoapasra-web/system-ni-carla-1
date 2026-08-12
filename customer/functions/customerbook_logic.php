<?php
session_start();

// If called via AJAX, start output buffering
if(!empty($_POST['ajax'])){
    ob_start();
}

if(!isset($_SESSION['customer_bookings'])){
    $_SESSION['customer_bookings'] = [];
}

$staffFile = __DIR__ . '/../../cut-and-coat/functions/json/staff_data.json';
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

$servicesFile = __DIR__ . '/../../cut-and-coat/functions/json/services_data.json';
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

$availabilityFile = __DIR__ . '/../../cut-and-coat/functions/json/availability_data.json';
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

$walkinsDataFile = __DIR__ . '/../../cut-and-coat/functions/json/walkins_data.json';
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
    if(!is_array($meta)) continue;
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

function normalizeBookingStaffName($staffString){
    $staffString = trim((string)$staffString);
    if(preg_match('/^(.+?)\s*\(/', $staffString, $matches)){
        return trim($matches[1]);
    }
    return $staffString;
}

function loadExistingStaffBookings(array $files){
    $bookings = [];
    foreach($files as $file){
        if(!file_exists($file)){
            continue;
        }
        $raw = json_decode(file_get_contents($file), true);
        if(!is_array($raw)){
            continue;
        }
        $entries = [];
        if(isset($raw['records']) && is_array($raw['records'])){
            $entries = $raw['records'];
        } else {
            $entries = $raw;
        }
        foreach($entries as $entry){
            if(!is_array($entry) || empty($entry['date']) || empty($entry['staff'])){
                continue;
            }
            $dt = strtotime($entry['date']);
            if($dt === false){
                continue;
            }
            $bookings[] = [
                'datetime' => date('Y-m-d H:i', $dt),
                'date' => date('Y-m-d', $dt),
                'staff' => normalizeBookingStaffName($entry['staff'])
            ];
        }
    }
    return $bookings;
}

function staffHasConflict(array $existingBookings, string $staffName, string $desiredDateTime): bool{
    $desired = date('Y-m-d H:i', strtotime($desiredDateTime));
    if(!$desired){
        return false;
    }
    foreach($existingBookings as $booking){
        if($booking['staff'] === $staffName && $booking['datetime'] === $desired){
            return true;
        }
    }
    return false;
}

function assignStaffName(array $staffStatus, array $existingBookings, string $desiredDateTime): string{
    $desired = date('Y-m-d H:i', strtotime($desiredDateTime));
    if(!$desired){
        return '';
    }
    $day = substr($desired, 0, 10);
    $available = [];
    foreach($staffStatus as $name => $status){
        if(strtolower(trim($status)) === 'available'){
            $available[$name] = 0;
        }
    }
    if(empty($available)){
        foreach($staffStatus as $name => $status){
            $available[$name] = 0;
        }
    }
    $conflicts = [];
    foreach($existingBookings as $booking){
        if($booking['datetime'] === $desired){
            $conflicts[$booking['staff']] = true;
        }
        if($booking['date'] === $day && isset($available[$booking['staff']])){
            $available[$booking['staff']]++;
        }
    }
    $eligible = [];
    foreach($available as $name => $count){
        if(!isset($conflicts[$name])){
            $eligible[$name] = $count;
        }
    }
    if(empty($eligible)){
        $eligible = $available;
    }
    asort($eligible, SORT_NUMERIC);
    reset($eligible);
    return key($eligible) ?: '';
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
        $staffInput = trim((string)($_POST['staff'] ?? ''));
        $existingBookingFiles = [
            __DIR__ . '/../../cut-and-coat/functions/json/walkins_data.json',
            __DIR__ . '/../../cut-and-coat/functions/json/customers_data.json'
        ];
        $existingStaffBookings = loadExistingStaffBookings($existingBookingFiles);
        $assignedStaff = $staffInput;
        if($staffInput === '' || $staffInput === 'AUTO_ASSIGN'){
            $autoName = assignStaffName($_SESSION['staff_status'], $existingStaffBookings, $_POST['date'] ?? '');
            if($autoName !== ''){
                $assignedStaff = $autoName . ' (Available)';
            }
        } else {
            $requestedName = normalizeBookingStaffName($staffInput);
            $staffAvailable = isset($_SESSION['staff_status'][$requestedName]) && strtolower(trim($_SESSION['staff_status'][$requestedName])) === 'available';
            $staffConflict = staffHasConflict($existingStaffBookings, $requestedName, $_POST['date'] ?? '');
            if(!$staffAvailable || $staffConflict){
                $autoName = assignStaffName($_SESSION['staff_status'], $existingStaffBookings, $_POST['date'] ?? '');
                if($autoName !== ''){
                    $assignedStaff = $autoName . ' (Available)';
                }
            }
        }

        $booking = [
            "name" => $_POST['name'],
            "phone" => $_POST['phone'],
            "email" => $_POST['email'],
            "date" => $_POST['date'],
            "staff" => $assignedStaff,
            "service" => $serviceLabel,
            "services" => $serviceNames,
            "total" => $total,
            "payment" => $_POST['payment'],
            "reference" => $_POST['reference'],
            "status" => "upcoming",
            "type" => "Online",
            "method" => "customer"
        ];

        $_SESSION['customer_bookings'][] = $booking;

        $walkinsDir = dirname($walkinsDataFile);
        if(!is_dir($walkinsDir)) mkdir($walkinsDir, 0777, true);

        $existingWalkins = [];
        if(file_exists($walkinsDataFile)){
            $existingWalkins = json_decode(file_get_contents($walkinsDataFile), true);
            if(!is_array($existingWalkins)) $existingWalkins = [];
        }

        $existingWalkins[] = $booking;
        file_put_contents($walkinsDataFile, json_encode($existingWalkins, JSON_PRETTY_PRINT));

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

        $salesFile = __DIR__ . '/../../cut-and-coat/functions/json/sales_data.json';
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
            'type' => 'Customer Booking',
            'payment' => $_POST['payment'],
            'amount' => $total,
            'date' => $bookingDate
        ];
        $existingSales[] = $newSale;
        $currentTotalSales += $total;

        file_put_contents($salesFile, json_encode([
            'totalSales' => $currentTotalSales,
            'records' => $existingSales
        ], JSON_PRETTY_PRINT));

        // Append to customers file for record
        $customersFile = __DIR__ . '/../../cut-and-coat/functions/json/customers_data.json';
        if(!is_dir(dirname($customersFile))){ mkdir(dirname($customersFile), 0777, true); }

        $existingCustomers = [];
        if(file_exists($customersFile)){
            $existingCustomers = json_decode(file_get_contents($customersFile), true);
            if(!is_array($existingCustomers)) $existingCustomers = [];
        }

        $existingCustomers[] = [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'staff' => $_POST['staff'],
            'service' => $serviceLabel,
            'datetime' => $_POST['date'],
            'type' => 'Online',
            'payment' => $_POST['payment'],
            'status' => 'upcoming'
        ];

        file_put_contents($customersFile, json_encode($existingCustomers, JSON_PRETTY_PRINT));

        $bookingsDataFile = __DIR__ . '/../../cut-and-coat/functions/json/bookings_data.json';
        if(!is_dir(dirname($bookingsDataFile))){
            mkdir(dirname($bookingsDataFile), 0777, true);
        }

        $existingBookingsData = [];
        if(file_exists($bookingsDataFile)){
            $existingBookingsData = json_decode(file_get_contents($bookingsDataFile), true);
            if(!is_array($existingBookingsData)){
                $existingBookingsData = [];
            }
        }

        $bookingDateOnly = date('Y-m-d', strtotime($booking['date']));
        $bookingTime = date('g:i A', strtotime($booking['date']));
        if($bookingDateOnly !== false && $bookingTime !== false){
            if(!isset($existingBookingsData[$bookingDateOnly]) || !is_array($existingBookingsData[$bookingDateOnly])){
                $existingBookingsData[$bookingDateOnly] = [];
            }
            $alreadyExists = false;
            foreach($existingBookingsData[$bookingDateOnly] as $entry){
                if(isset($entry['name'], $entry['time']) && $entry['name'] === $booking['name'] && $entry['time'] === $bookingTime){
                    $alreadyExists = true;
                    break;
                }
            }
            if(!$alreadyExists){
                $existingBookingsData[$bookingDateOnly][] = [
                    'name' => $booking['name'],
                    'time' => $bookingTime
                ];
                file_put_contents($bookingsDataFile, json_encode($existingBookingsData, JSON_PRETTY_PRINT));
            }
        }
    }

    if(!empty($_POST['ajax'])){
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