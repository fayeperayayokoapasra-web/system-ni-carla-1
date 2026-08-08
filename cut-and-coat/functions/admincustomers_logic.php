<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$customersFile = __DIR__ . '/json/customers_data.json';

if(!file_exists($customersFile)){
    $defaultCustomers = [
        ["name"=>"Maria Santos","phone"=>"09171234567","staff"=>"Anna","service"=>"Gel Polish","datetime"=>"2026-04-01 10:00","type"=>"Online","payment"=>"GCash"],
        ["name"=>"John Cruz","phone"=>"09181234567","staff"=>"Kim","service"=>"Manicure","datetime"=>"2026-04-01 11:30","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Ana Reyes","phone"=>"09221234567","staff"=>"Anna","service"=>"Pedicure","datetime"=>"2026-04-02 09:00","type"=>"Online","payment"=>"Card"],
        ["name"=>"Mark Dela Cruz","phone"=>"09331234567","staff"=>"Kim","service"=>"Nail Art","datetime"=>"2026-04-02 13:00","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Sofia Garcia","phone"=>"09441234567","staff"=>"Anna","service"=>"Extension","datetime"=>"2026-04-03 10:30","type"=>"Online","payment"=>"Maya"],
        ["name"=>"Luis Navarro","phone"=>"09551234567","staff"=>"Kim","service"=>"Gel Polish","datetime"=>"2026-04-03 14:00","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Ella Cruz","phone"=>"09661234567","staff"=>"Anna","service"=>"Manicure","datetime"=>"2026-04-04 09:30","type"=>"Online","payment"=>"GCash"],
        ["name"=>"James Lim","phone"=>"09771234567","staff"=>"Kim","service"=>"Pedicure","datetime"=>"2026-04-04 11:00","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Chloe Tan","phone"=>"09881234567","staff"=>"Anna","service"=>"Nail Art","datetime"=>"2026-04-05 15:00","type"=>"Online","payment"=>"Card"],
        ["name"=>"Miguel Torres","phone"=>"09991234567","staff"=>"Kim","service"=>"Extension","datetime"=>"2026-04-05 16:30","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Princess Cruz","phone"=>"09192345678","staff"=>"Anna","service"=>"Gel Polish","datetime"=>"2026-04-06 10:00","type"=>"Online","payment"=>"Maya"],
        ["name"=>"Carlos Reyes","phone"=>"09203456789","staff"=>"Kim","service"=>"Manicure","datetime"=>"2026-04-06 12:00","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Angel Dela Cruz","phone"=>"09314567890","staff"=>"Anna","service"=>"Pedicure","datetime"=>"2026-04-07 09:30","type"=>"Online","payment"=>"GCash"],
        ["name"=>"Patrick Lim","phone"=>"09425678901","staff"=>"Kim","service"=>"Nail Art","datetime"=>"2026-04-07 13:30","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Hannah Garcia","phone"=>"09536789012","staff"=>"Anna","service"=>"Extension","datetime"=>"2026-04-08 10:15","type"=>"Online","payment"=>"Card"],
        ["name"=>"Kevin Santos","phone"=>"09647890123","staff"=>"Kim","service"=>"Gel Polish","datetime"=>"2026-04-08 11:45","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Liza Navarro","phone"=>"09758901234","staff"=>"Anna","service"=>"Manicure","datetime"=>"2026-04-09 09:00","type"=>"Online","payment"=>"Maya"],
        ["name"=>"Daniel Cruz","phone"=>"09869012345","staff"=>"Kim","service"=>"Pedicure","datetime"=>"2026-04-09 12:30","type"=>"Walk-In","payment"=>"Cash"],
        ["name"=>"Mia Reyes","phone"=>"09970123456","staff"=>"Anna","service"=>"Nail Art","datetime"=>"2026-04-10 14:00","type"=>"Online","payment"=>"GCash"],
        ["name"=>"Ethan Lim","phone"=>"09181239876","staff"=>"Kim","service"=>"Extension","datetime"=>"2026-04-10 16:00","type"=>"Walk-In","payment"=>"Cash"]
    ];

    if(!is_dir(dirname($customersFile))){
        mkdir(dirname($customersFile), 0777, true);
    }
    file_put_contents($customersFile, json_encode($defaultCustomers, JSON_PRETTY_PRINT));
}

$customers = json_decode(file_get_contents($customersFile), true);
if(!is_array($customers)){
    $customers = [];
}

// Normalize missing status values.
if(is_array($customers)){
    foreach($customers as &$customer){
        if(!isset($customer['status']) || !is_string($customer['status'])){
            $customer['status'] = 'upcoming';
        }
    }
    unset($customer);
}

$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && is_array($_POST['status'])){
    $updated = false;
    $allowedStatuses = ['upcoming', 'resched', 'cancelled', 'settled'];
    foreach($_POST['status'] as $index => $newStatus){
        $index = intval($index);
        $newStatus = trim((string)$newStatus);
        if(isset($customers[$index]) && in_array($newStatus, $allowedStatuses, true)){
            if(($customers[$index]['status'] ?? 'upcoming') !== $newStatus){
                $customers[$index]['status'] = $newStatus;
                $updated = true;
            }
        }
    }
    if($updated){
        file_put_contents($customersFile, json_encode($customers, JSON_PRETTY_PRINT));
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customers'])){
    $selected = $_POST['selected_customers'] ?? [];
    if(is_array($selected) && !empty($selected)){
        $indexes = array_map('intval', $selected);
        rsort($indexes);
        $deleted = 0;
        foreach($indexes as $index){
            if(isset($customers[$index])){
                unset($customers[$index]);
                $deleted++;
            }
        }
        if($deleted > 0){
            $customers = array_values($customers);
            file_put_contents($customersFile, json_encode($customers, JSON_PRETTY_PRINT));
            $message = "$deleted customer(s) deleted.";
        }
    }
}
$data = $customers;
