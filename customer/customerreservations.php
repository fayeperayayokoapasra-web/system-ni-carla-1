<?php
session_start();

if(!isset($_SESSION['customer'])){
    header("Location: customerlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Reservations</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerreservations.css">
</head>

<body class="reservations-page">

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<!-- SIDEBAR -->


<!-- MAIN -->
<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">My Reservation History</div>

<!-- FILTER -->
<div class="filter-bar">
<input type="text" id="searchInput" placeholder="Search service...">
<input type="date" id="dateFilter">
</div>

<!-- TABLE -->
<div class="table-container">
<table id="reservationTable">
<thead>
<tr>
<th>Date & Time</th>
<th>Service</th>
<th>Total</th>
<th>Points Earned</th>
<th>Payment Method</th>
<th>Staff Assisted</th>
</tr>
</thead>

<tbody>

<tr><td>2026-04-01 2:00 PM</td><td>Gel Polish</td><td>₱500</td><td>50</td><td>GCash</td><td>Anna</td></tr>
<tr><td>2026-04-03 4:00 PM</td><td>Manicure</td><td>₱300</td><td>30</td><td>Maya</td><td>Carla</td></tr>
<tr><td>2026-04-05 1:00 PM</td><td>Pedicure</td><td>₱350</td><td>35</td><td>GCash</td><td>Bea</td></tr>
<tr><td>2026-04-07 3:00 PM</td><td>Nail Art</td><td>₱600</td><td>60</td><td>Maya</td><td>Joy</td></tr>
<tr><td>2026-04-10 11:00 AM</td><td>Gel Polish + Art</td><td>₱800</td><td>80</td><td>GCash</td><td>Lisa</td></tr>
<tr><td>2026-04-12 5:00 PM</td><td>Foot Spa</td><td>₱400</td><td>40</td><td>Maya</td><td>Nina</td></tr>
<tr><td>2026-04-14 2:30 PM</td><td>Hand Spa</td><td>₱450</td><td>45</td><td>GCash</td><td>Kate</td></tr>
<tr><td>2026-04-15 6:00 PM</td><td>Gel Polish</td><td>₱500</td><td>50</td><td>Maya</td><td>Mia</td></tr>
<tr><td>2026-04-16 1:30 PM</td><td>Pedicure + Spa</td><td>₱700</td><td>70</td><td>GCash</td><td>Rose</td></tr>
<tr><td>2026-04-17 4:30 PM</td><td>Full Package</td><td>₱1200</td><td>120</td><td>Maya</td><td>Anne</td></tr>

</tbody>
</table>
</div>

</div>

<script>

/* SEARCH */
document.getElementById("searchInput").addEventListener("keyup", function(){
let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("#reservationTable tbody tr");

rows.forEach(row=>{
let text = row.innerText.toLowerCase();
row.style.display = text.includes(filter) ? "" : "none";
});
});

/* DATE FILTER */
document.getElementById("dateFilter").addEventListener("change", function(){
let selected = this.value;
let rows = document.querySelectorAll("#reservationTable tbody tr");

rows.forEach(row=>{
let date = row.cells[0].innerText.split(" ")[0];
row.style.display = (selected === "" || date === selected) ? "" : "none";
});
});

</script>

</body>
</html>