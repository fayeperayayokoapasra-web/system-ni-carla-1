<?php
session_start();

if(!isset($_SESSION['admin'])){
header("Location: adminlogin.php");
exit();
}

$salesFile = __DIR__ . '/json/sales_data.json';
$defaultSales = [
    ["type"=>"Online","payment"=>"GCash","amount"=>499],
    ["type"=>"Online","payment"=>"Maya","amount"=>599],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>180],
    ["type"=>"Online","payment"=>"Card","amount"=>799],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>150],
    ["type"=>"Online","payment"=>"Maya","amount"=>300],
    ["type"=>"Walk-In","payment"=>"Card","amount"=>400],
    ["type"=>"Online","payment"=>"GCash","amount"=>499],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>180],
    ["type"=>"Online","payment"=>"Card","amount"=>599],
    ["type"=>"Online","payment"=>"GCash","amount"=>799],
    ["type"=>"Walk-In","payment"=>"Cash","amount"=>300],
    ["type"=>"Online","payment"=>"Maya","amount"=>499],
    ["type"=>"Walk-In","payment"=>"Card","amount"=>150],
    ["type"=>"Online","payment"=>"GCash","amount"=>599],
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
    if(empty($data)){
        $data = $defaultSales;
    }
} else {
    if(!is_dir(dirname($salesFile))){
        mkdir(dirname($salesFile), 0777, true);
    }
    $data = $defaultSales;
    file_put_contents($salesFile, json_encode(['totalSales' => array_sum(array_column($defaultSales, 'amount')), 'records' => $data], JSON_PRETTY_PRINT));
}

/* ================= FIXED LOGIC ================= */

$total = 0;
$online = 0;
$walkin = 0;

$onlinePayments = 0;
$card = 0;
$cash = 0;

foreach($data as $d){

$amount = $d["amount"];
$type = strtolower($d["type"]);
$payment = strtolower($d["payment"]);

$total += $amount;

/* TYPE */
if($type == "online"){
$online += $amount;
}else{
$walkin += $amount;
}

/* PAYMENT */
if($payment == "card"){
$card += $amount;
}
elseif($payment == "gcash" || $payment == "maya"){
$onlinePayments += $amount;
}
elseif($payment == "cash"){
$cash += $amount;
}
}
?>
