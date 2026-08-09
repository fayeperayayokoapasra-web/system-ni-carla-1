<?php
session_start();

if(!isset($_SESSION['customer'])){
    header("Location: customerlogin.php");
    exit();
}

$loyaltyPoints = 250;

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

$total=0; $down=0; $balance=0;

if(isset($_POST['submit'])){
$service=$_POST['service'];
$total=$servicePrices[$service] ?? 0;

if(isset($_POST['use_points'])) $total-=50;
if($total<0) $total=0;

$down=$total*0.5;
$balance=$total-$down;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerbook.css">
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<?php include 'sidebar.php'; ?>

<div class="main">

<!-- FORM -->
<div class="form-box">
<div class="page-title">Book Appointment</div>

<form method="POST">

<label>Name <span class="required">*</span></label>
<input type="text" name="name" required>

<label>Contact No. <span class="required">*</span></label>
<input type="text" name="phone" required>

<label>Email</label>
<input type="email" name="email">

<label>Date & Time</label>
<input type="text" id="datetime" name="date" readonly onclick="openCalendar()">

<label>Preferred Staff</label>
<select name="staff">
<option value="" disabled selected>Select Staff</option>
<?php
foreach($_SESSION['staff_status'] as $name => $status){
$disabled = ($status !== "Available") ? "disabled" : "";
echo "<option $disabled>$name ($status)</option>";
}
?>
</select>

<label>Service <span class="required">*</span></label>
<select name="service" id="serviceSelect" onchange="updateTotal()" required>
<option value="" disabled selected>Select Service</option>
<?php foreach($servicePrices as $s => $p){
echo "<option value='$s' data-price='$p'>$s</option>";
} ?>
</select>

<!-- LOYALTY -->
<label>Loyalty Points</label>
<div style="display:flex;gap:10px;align-items:center;">
<div class="ios-toggle" id="toggle" onclick="togglePoints()"></div>
<input type="hidden" name="use_points" id="usePoints">