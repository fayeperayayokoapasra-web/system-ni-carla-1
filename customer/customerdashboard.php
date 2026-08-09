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
<title>Customer Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerdashboard.css">
</head>

<body class="dashboard-page">

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>

<div class="icons">
<div class="icon bell hasNotif" id="bell" onclick="toggleNotif()"></div>
<div class="icon message" onclick="openMessages()"></div>
<a href="customerprofile.php" class="profile">My Profile</a>
<a href="index.php" class="logout">Logout</a>
</div>
</div>



<!-- NOTIFICATIONS -->
<div class="dropdown" id="notifBox">
<div class="notification-item">Upcoming Appointment on April 20 at 2PM</div>
<div class="notification-item">Promo: 20% off Gel Polish</div>
</div>

<!-- MAIN -->
<?php include 'sidebar.php'; ?>

<div class="main" id="main">
<div class="welcome">Welcome, Carla!</div>

<div class="top-cards">
<div class="card"><h4>Frequent Service</h4><p>Gel Polish</p></div>
<div class="card"><h4>Total Visits</h4><p>15</p></div>
<div class="card"><h4>Loyalty Points</h4><p>3,500</p></div>
<div class="card"><h4>Membership</h4><p>Platinum</p></div>
</div>

<div class="bottom-cards">
<div class="card">
<h4>Appointments</h4>
<p>View Records</p>
<a href="customerreservations.php" class="card-btn">View</a>
</div>

<div class="card">
<h4>Book Visit</h4>
<p>Schedule Now</p>
<a href="customerbook.php" class="card-btn">Book</a>
</div>

<div class="card">
<h4>Services</h4>
<p>Explore Offers</p>
<a href="customerservices.php" class="card-btn">Browse</a>
</div>
</div>
</div>

<!-- MESSAGE PAGE -->
<div class="message-page" id="messagePage">
<div class="back" onclick="goBack()">← Back</div>

<h2>Message Salon</h2>

<div class="chat-box" id="chatBox">
<div class="msg salon">Hello! How can we help you?</div>
</div>

<div class="input-box">
<input type="text" id="msgInput">
<button class="send-btn" onclick="sendMessage()">Send</button>
</div>
</div>

<script>

/* NOTIFICATION TOGGLE */
function toggleNotif(){
let box = document.getElementById("notifBox");
let bell = document.getElementById("bell");

/* toggle dropdown */
box.style.display = (box.style.display==="block") ? "none" : "block";

/* ✅ REMOVE RED DOT WHEN CLICKED */
bell.classList.remove("hasNotif");
}

/* MESSAGE NAVIGATION */
function openMessages(){
document.getElementById("main").style.display="none";
document.getElementById("messagePage").classList.add("active");
}

function goBack(){
document.getElementById("main").style.display="block";
document.getElementById("messagePage").classList.remove("active");
}

/* SEND MESSAGE */
function sendMessage(){
let input=document.getElementById("msgInput");
let chat=document.getElementById("chatBox");

if(input.value.trim()!==""){
let msg=document.createElement("div");
msg.className="msg customer";
msg.innerText=input.value;
chat.appendChild(msg);
input.value="";
}
}

</script>

</body>
</html>