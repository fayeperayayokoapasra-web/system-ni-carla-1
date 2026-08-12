<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$salesFile = __DIR__ . '/json/sales_data.json';
$customerFile = __DIR__ . '/json/customers_data.json';

$reportDate = date('Y-m-d');
$reportMonth = date('Y-m');
if(isset($_GET['report_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['report_date'])){
    $reportDate = $_GET['report_date'];
}
if(isset($_GET['report_month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['report_month'])){
    $reportMonth = $_GET['report_month'];
}

$defaultSales = [
    ["type"=>"Online","payment"=>"GCash","amount"=>499,"date"=>date('Y-m-d', strtotime('-9 days'))],
    ["type"=>"Online","payment"=>"Maya","amount"=>599,"date"=>date('Y-m-d', strtotime('-8 days'))],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>180,"date"=>date('Y-m-d', strtotime('-7 days'))],
    ["type"=>"Online","payment"=>"Card","amount"=>799,"date"=>date('Y-m-d', strtotime('-7 days'))],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>150,"date"=>date('Y-m-d', strtotime('-6 days'))],
    ["type"=>"Online","payment"=>"Maya","amount"=>300,"date"=>date('Y-m-d', strtotime('-6 days'))],
    ["type"=>"Walk-In","payment"=>"Card","amount"=>400,"date"=>date('Y-m-d', strtotime('-5 days'))],
    ["type"=>"Online","payment"=>"GCash","amount"=>499,"date"=>date('Y-m-d', strtotime('-5 days'))],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>180,"date"=>date('Y-m-d', strtotime('-4 days'))],
    ["type"=>"Online","payment"=>"Card","amount"=>599,"date"=>date('Y-m-d', strtotime('-4 days'))],
    ["type"=>"Online","payment"=>"GCash","amount"=>799,"date"=>date('Y-m-d', strtotime('-3 days'))],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>300,"date"=>date('Y-m-d', strtotime('-3 days'))],
    ["type"=>"Online","payment"=>"Maya","amount"=>499,"date"=>date('Y-m-d', strtotime('-2 days'))],
    ["type"=>"Walk-In","payment"=>"Card","amount"=>150,"date"=>date('Y-m-d', strtotime('-2 days'))],
    ["type"=>"Online","payment"=>"GCash","amount"=>599,"date"=>date('Y-m-d', strtotime('-1 day'))],
];

$data = [];
if(file_exists($salesFile)){
    $salesRaw = json_decode(file_get_contents($salesFile), true);
    if(is_array($salesRaw)){
        if(isset($salesRaw['records']) && is_array($salesRaw['records'])){
            $data = $salesRaw['records'];
        } else {
            $data = $salesRaw;
        }
    }
}

if(empty($data)){
    if(!is_dir(dirname($salesFile))){
        mkdir(dirname($salesFile), 0777, true);
    }
    $data = $defaultSales;
    file_put_contents($salesFile, json_encode(['totalSales' => array_sum(array_column($data, 'amount')), 'records' => $data], JSON_PRETTY_PRINT));
}

function normalizeDate($value){
    $date = trim((string)$value);
    $timestamp = strtotime($date);
    return $timestamp !== false ? date('Y-m-d', $timestamp) : '';
}

$hasChanges = false;
foreach($data as &$sale){
    if(!isset($sale['date']) || trim((string)$sale['date']) === ''){
        $sale['date'] = date('Y-m-d');
        $hasChanges = true;
    }
    if(!isset($sale['type']) || trim((string)$sale['type']) === ''){
        $sale['type'] = 'Online';
        $hasChanges = true;
    }
    if(!isset($sale['payment']) || trim((string)$sale['payment']) === ''){
        $sale['payment'] = 'Cash';
        $hasChanges = true;
    }
    $sale['date'] = normalizeDate($sale['date']);
}
unset($sale);

if($hasChanges){
    file_put_contents($salesFile, json_encode(['totalSales' => array_sum(array_column($data, 'amount')), 'records' => $data], JSON_PRETTY_PRINT));
}

$reportMonthLabel = date('F Y', strtotime($reportMonth . '-01'));
$reportMonthKey = substr($reportMonth, 0, 7);

function monthDays($yearMonth){
    $dt = DateTime::createFromFormat('Y-m', $yearMonth);
    if(!$dt){
        return [];
    }
    $days = [];
    $totalDays = (int)$dt->format('t');
    for($day = 1; $day <= $totalDays; $day++){
        $days[] = $dt->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
    }
    return $days;
}

$dailyTotals = ['total' => 0, 'online' => 0, 'walkin' => 0, 'card' => 0, 'onlinePayments' => 0, 'cash' => 0];
$monthlyTotals = ['total' => 0, 'online' => 0, 'walkin' => 0, 'card' => 0, 'onlinePayments' => 0, 'cash' => 0];
$dailyPaymentBreakdown = ['Cash' => 0, 'GCash' => 0, 'Maya' => 0, 'Card' => 0];
$dailyTypeBreakdown = ['Online' => 0, 'Walk-In' => 0];
$monthlyTotalsByDay = array_fill_keys(monthDays($reportMonthKey), 0);
$dailyRecords = [];
$monthlyRecords = [];

foreach($data as $sale){
    $amount = isset($sale['amount']) ? (float)$sale['amount'] : 0;
    $type = strtolower(trim((string)$sale['type']));
    $payment = strtolower(trim((string)$sale['payment']));
    $date = normalizeDate($sale['date']);
    $month = substr($date, 0, 7);

    if($date === $reportDate){
        $dailyTotals['total'] += $amount;
        if($type === 'online'){
            $dailyTotals['online'] += $amount;
        } else {
            $dailyTotals['walkin'] += $amount;
        }
        if($payment === 'card'){
            $dailyTotals['card'] += $amount;
            $dailyPaymentBreakdown['Card'] += $amount;
        } elseif($payment === 'gcash' || $payment === 'maya'){
            $dailyTotals['onlinePayments'] += $amount;
            $dailyPaymentBreakdown[ucfirst($payment)] += $amount;
        } elseif($payment === 'cash'){
            $dailyTotals['cash'] += $amount;
            $dailyPaymentBreakdown['Cash'] += $amount;
        }
        $dailyRecords[] = $sale;
    }

    if($month === $reportMonthKey){
        $monthlyTotals['total'] += $amount;
        if($type === 'online'){
            $monthlyTotals['online'] += $amount;
        } else {
            $monthlyTotals['walkin'] += $amount;
        }
        if($payment === 'card'){
            $monthlyTotals['card'] += $amount;
        } elseif($payment === 'gcash' || $payment === 'maya'){
            $monthlyTotals['onlinePayments'] += $amount;
        } elseif($payment === 'cash'){
            $monthlyTotals['cash'] += $amount;
        }
        if(isset($monthlyTotalsByDay[$date])){
            $monthlyTotalsByDay[$date] += $amount;
        }
        $monthlyRecords[] = $sale;
    }
}

$dailyChartLabels = array_keys($dailyPaymentBreakdown);
$dailyChartValues = array_values($dailyPaymentBreakdown);
$monthlyChartLabels = array_keys($monthlyTotalsByDay);
$monthlyChartValues = array_values($monthlyTotalsByDay);

$customerRecords = [];
$customerCount = 0;
$customerCountToday = 0;
$customerCountMonth = 0;
$recentCustomers = [];
if(file_exists($customerFile)){
    $customerData = json_decode(file_get_contents($customerFile), true);
    if(is_array($customerData)){
        $customerRecords = $customerData;
    }
}

foreach($customerRecords as $customer){
    $customerDate = normalizeDate($customer['datetime'] ?? $customer['date'] ?? '');
    if($customerDate === $reportDate){
        $customerCountToday++;
    }
    if(substr($customerDate, 0, 7) === $reportMonthKey){
        $customerCountMonth++;
    }
}
$customerCount = count($customerRecords);
$recentCustomers = array_slice(array_reverse($customerRecords), 0, 10);
?>
