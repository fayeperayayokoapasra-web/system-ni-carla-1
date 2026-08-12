<?php
session_start();

if(!isset($_SESSION['admin'])){
header("Location: adminlogin.php");
exit();
}

include 'functions/admincustomers_logic.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Reservations</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminreservations.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="header">
<h2>Reservations List</h2>
</div>

<div class="search-box">
<input type="text" id="searchInput" placeholder="Search reservations...">
</div>

<!-- ✅ ONLY WRAPPER ADDED -->
<div class="table-container">
<form method="post" id="statusForm">
<input type="hidden" name="update_statuses" value="1">
<table id="reservationTable">

<tr>
<th>Name</th>
<th>Contact No.</th>
<th>Service</th>
<th>Staff</th>
<th>Booking Method</th>
<th>Payment</th>
<th>Date & Time</th>
<th>Status</th>
<th>Change Status</th>
</tr>

<?php
foreach($data as $index => $c){
    $name = isset($c['name']) ? htmlspecialchars($c['name']) : '';
    $phone = isset($c['phone']) ? htmlspecialchars($c['phone']) : '';
    $service = isset($c['service']) ? htmlspecialchars($c['service'], ENT_QUOTES) : '';
    $staff = isset($c['staff']) ? htmlspecialchars($c['staff']) : '';
    $type = isset($c['type']) ? strtolower(trim($c['type'])) : 'online';
    $payment = isset($c['payment']) ? htmlspecialchars($c['payment']) : '';
    $datetime = isset($c['datetime']) ? htmlspecialchars($c['datetime']) : '';
    $status = isset($c['status']) ? strtolower(trim($c['status'])) : 'upcoming';
    if(!in_array($status, ['upcoming','resched','cancelled','settled'], true)){
        $status = 'upcoming';
    }
    $bookingClass = strpos($type, 'walk') !== false ? 'walkin' : 'online';
    $bookingLabel = $type === 'walk-in' ? 'Walk-In' : ucfirst($type);

    $safeService = $service;
    echo "<tr>
    <td class='name'>$name</td>
    <td class='contact'>$phone</td>
    <td class='service-cell' title='$safeService'>$service</td>
    <td>$staff</td>
    <td><span class='booking $bookingClass'>$bookingLabel</span></td>
    <td>$payment</td>
    <td>$datetime</td>
    <td><span class='status $status'>" . ucfirst($status) . "</span></td>
    <td>
    <select name='status[$index]' onchange='this.form.submit()'>
    <option value=''>Select</option>
    <option value='resched'" . ($status === 'resched' ? ' selected' : '') . ">Re-sched</option>
    <option value='cancelled'" . ($status === 'cancelled' ? ' selected' : '') . ">Cancelled</option>
    <option value='settled'" . ($status === 'settled' ? ' selected' : '') . ">Settled</option>
    </select>
    </td>
    </tr>";
}
?>

</table>
</form>

</div>

</div>

<script>

function changeStatus(select){
let row = select.closest("tr");
let statusSpan = row.querySelector(".status");

let value = select.value;
if(value === "") return;

statusSpan.classList.remove("upcoming","resched","cancelled","settled");
statusSpan.classList.add(value);
statusSpan.innerText = value.charAt(0).toUpperCase() + value.slice(1);
}

document.getElementById("searchInput").addEventListener("keyup", function(){
let value = this.value.toLowerCase();
let rows = document.querySelectorAll("#reservationTable tr");

rows.forEach((row, index)=>{
if(index === 0) return;
row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
});
});

</script>

</body>
</html>