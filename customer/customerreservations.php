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

<style>

/* ===== BODY ===== */
body{
margin:0;
font-family:'Poppins', sans-serif;
background:#f4fdf9;
overflow:hidden;
}

/* ===== TOPBAR ===== */
.topbar{
width:100%;
height:65px;
background:white;
position:fixed;
top:0;
left:0;
z-index:1000;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 20px;
box-shadow:0 3px 12px rgba(0,0,0,0.08);
box-sizing:border-box;
}

.topbar h3{
font-family:'Playfair Display', serif;
color:#064e3b;
margin:0;
font-size:18px;
}

/* ===== SIDEBAR ===== */
.sidebar{
width:200px;
height:100vh;
background:#064e3b;
position:fixed;
top:65px;
left:0;
padding:20px 10px;
transition:0.3s;
}

.sidebar.collapsed{
width:60px;
}

.sidebar a{
display:block;
color:white;
padding:12px 12px;
margin:5px 0;
border-radius:8px;
text-decoration:none;
font-size:13px;
transition:0.3s;
}

.sidebar a:hover{
background:#10b981;
transform:translateX(5px);
}

.sidebar.collapsed a{
font-size:0;
padding:12px 0;
text-align:center;
}

.toggle-btn{
background:#10b981;
border:none;
color:white;
width:28px;
height:28px;
border-radius:6px;
cursor:pointer;
margin-bottom:10px;
display:flex;
align-items:center;
justify-content:center;
}

/* ===== MAIN ===== */
.main{
margin-left:220px;
padding:75px 25px 25px 25px;
height:calc(100vh - 65px);
transition:0.3s;

opacity:0;
transform:translateY(15px);
animation:fadeInAdmin .5s ease forwards;
}

.main.collapsed{
margin-left:70px;
}

@keyframes fadeInAdmin{
to{
opacity:1;
transform:translateY(0);
}
}

/* TITLE */
.title{
font-family:'Playfair Display', serif;
font-size:26px;
background:white;
padding:14px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
color:#064e3b;
margin-bottom:10px;
}

/* ===== FILTER BAR (NORMAL NOW) ===== */
.filter-bar{
display:flex;
gap:10px;
padding:10px;
background:#f4fdf9;
margin-bottom:10px;
}

.filter-bar input{
padding:10px;
border-radius:8px;
border:1px solid #ccc;
font-size:13px;
}

/* ===== TABLE CONTAINER (ONLY SCROLL) ===== */
.table-container{
height:calc(100vh - 230px);
overflow-y:auto;
scroll-behavior:smooth;
border-radius:12px;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
background:white;
border-radius:12px;
overflow:hidden;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
text-align:center;
}

th{
background:#065f46;
color:white;
padding:12px;
font-size:13px;
position:sticky;
top:0;
z-index:2;
}

td{
padding:12px;
border-bottom:1px solid #eee;
font-size:13px;
}

tr:hover{
background:#ecfdf5;
}

/* COLUMN WIDTH */
th:nth-child(1), td:nth-child(1){ width:18%; text-align:left; }
th:nth-child(2), td:nth-child(2){ width:20%; text-align:left; }
th:nth-child(3), td:nth-child(3){ width:15%; text-align:center; }
th:nth-child(4), td:nth-child(4){ width:15%; text-align:center; }
th:nth-child(5), td:nth-child(5){ width:16%; text-align:center; }
th:nth-child(6), td:nth-child(6){ width:16%; text-align:center; }

</style>
</head>

<body>

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