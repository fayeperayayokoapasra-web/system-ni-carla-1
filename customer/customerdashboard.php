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

<style>

/* ===== BODY ===== */
body{
margin:0;
font-family:'Poppins', sans-serif;
background:#f4fdf9;
overflow:hidden;
}


/* ===== TOPBAR (FIXED + NO SHIFT) ===== */
.topbar{
position:fixed;
top:0;
left:10;
width:100%;
height:65px;
background:white;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 20px;
box-shadow:0 3px 12px rgba(0,0,0,0.08);
z-index:1000;
box-sizing:border-box;
}

.topbar h3{
font-family:'Playfair Display', serif;
color:#064e3b;
margin:0;
font-size:18px;
}

/* ICONS FIX */
.icons{
display:flex;
align-items:center;
gap:12px;
flex-shrink:0;
}

/* ICON BASE */
.icon{
width:22px;
height:22px;
cursor:pointer;
position:relative;
display:flex;
align-items:center;
justify-content:center;
}

/* 🔔 BELL */
.bell{
position:relative;
width:18px;
height:18px;
border:2px solid #10b981;
border-radius:50% 50% 45% 45%;
}

.bell::before{
content:'';
position:absolute;
bottom:-5px;
left:50%;
transform:translateX(-50%);
width:6px;
height:6px;
background:#10b981;
border-radius:50%;
}

/* 🔴 RED DOT */
.bell::after{
content:'';
position:absolute;
top:-3px;
right:-3px;
width:7px;
height:7px;
background:red;
display: none;
border-radius:50%;
}

/* ✅ SHOW DOT ONLY WHEN HAS NOTIF */
.bell.hasNotif::after{
display:block;
}

/* MESSAGE ICON */
.message{
width:18px;
height:18px;
border:2px solid #10b981;
border-radius:6px;
}

.message::after{
content:'';
position:absolute;
bottom:-4px;
left:4px;
width:8px;
height:8px;
background:#10b981;
transform:rotate(45deg);
}

/* PROFILE */
.profile{
color:#065f46;
text-decoration:none;
font-weight:600;
font-size:12px;
padding:6px 10px;
white-space:nowrap;
}

/* LOGOUT (FIX POSITION) */
.logout{
background:#10b981;
color:white;
padding:6px 10px;
border-radius:8px;
text-decoration:none;
font-size:12px;
margin-left:5px;
white-space:nowrap;
}

.logout:hover{ background:#059669; }

.logout:hover{ background:#059669; }

/* ===== SIDEBAR ===== */
.sidebar{
width:200px;
height:100vh;
background:#064e3b;
position:fixed;
top:65px;
left:0;
padding:20px 10px;
transition:width .3s ease;
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

/* TOGGLE */
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
padding:90px 25px;
transition:margin-left .3s ease;
animation:fadeMain .5s ease;
}

.main.collapsed{
margin-left:80px;
}

@keyframes fadeMain{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

/* TITLE */
.welcome{
font-family:'Playfair Display', serif;
font-size:26px;
color:#064e3b;
margin-bottom:25px;
}

/* ===== CARDS (UPDATED TO MATCH ADMIN) ===== */
.top-cards{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:25px;
}

.bottom-cards{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
}

.card{
background:linear-gradient(135deg,#10b981,#059669);
padding:25px;
border-radius:18px;
color:white;
text-align:center;
position:relative;
overflow:hidden;
box-shadow:0 10px 25px rgba(16,185,129,0.3);
transition:0.3s;
}

/* SHINE */
.card::before{
content:"";
position:absolute;
top:0;
left:-75%;
width:50%;
height:100%;
background:rgba(255,255,255,0.3);
transform:skewX(-25deg);
transition:0.6s;
}

.card:hover::before{ left:125%; }

.card:hover{
transform:translateY(-6px) scale(1.02);
}

/* ✅ TITLE (small like admin) */
.card h4{
margin:0;
font-size:13px;
font-weight:400;
letter-spacing:0.3px;
opacity:0.9;
}

/* ✅ VALUE (BIG + PLAYFAIR LIKE ADMIN) */
.card p{
margin-top:10px;
font-size:28px;
font-family:'Playfair Display', serif;
font-weight:700;
}

/* BUTTON */
.card-btn{
margin-top:15px;
background:white;
color:#059669;
padding:10px 20px;
border-radius:20px;
font-size:12px;
text-decoration:none;
font-weight:600;
}

/* ===== NOTIFICATION DROPDOWN (RESTORED PREMIUM) ===== */
.dropdown{
position:fixed;
top:70px;
right:20px;
width:260px;
background:white;
border-radius:16px;
box-shadow:0 15px 40px rgba(0,0,0,0.15);
display:none;
z-index:2000;
overflow:hidden;
}

.dropdown-header{
padding:12px 15px;
font-weight:600;
border-bottom:1px solid #eee;
color:#064e3b;
}

.notification-item{
padding:12px 15px;
font-size:13px;
border-bottom:1px solid #f0f0f0;
cursor:pointer;
transition:0.2s;
}

.notification-item:hover{
background:#f0fdf9;
}

/* ===== MESSAGE PAGE (RESTORED DESIGN) ===== */
.message-page{
display:none;
margin-left:220px;
padding:90px 20px;
transition:margin-left .3s ease;
}

.message-page.active{
display:block;
}

/* ✅ RESPONSIVE WHEN SIDEBAR COLLAPSED */
.message-page.collapsed{
margin-left:80px;
}

.chat-box{
background:white;
border-radius:16px;
padding:15px;
height:420px;
overflow-y:auto;
display:flex;
flex-direction:column;
gap:10px;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.msg{
padding:12px;
border-radius:14px;
max-width:70%;
font-size:13px;
}

.customer{
background:linear-gradient(135deg,#10b981,#059669);
color:white;
align-self:flex-end;
}

.salon{
background:#ecfdf5;
}

.input-box{
display:flex;
gap:10px;
margin-top:10px;
}

.input-box input{
flex:1;
padding:10px;
border-radius:10px;
border:1px solid #ccc;
}

.send-btn{
background:#10b981;
border:none;
color:white;
padding:10px;
border-radius:10px;
cursor:pointer;
}

/* RESPONSIVE */
@media(max-width:900px){
.top-cards{ grid-template-columns:repeat(2,1fr); }
.bottom-cards{ grid-template-columns:1fr; }
}

</style>
</head>

<body>

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