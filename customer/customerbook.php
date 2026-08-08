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

<style>

body{
margin:0;
font-family:'Montserrat',sans-serif;
background:#f4fdf9;
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

/* SMOOTH SIDEBAR */
.sidebar{
transition:all 0.4s ease;
}

/* COLLAPSED SIDEBAR */
.sidebar.collapsed{
width:60px;
}

/* HIDE TEXT WHEN COLLAPSED */
.sidebar.collapsed a{
font-size:0;
padding:12px 0;
text-align:center;
}

/* TOGGLE BUTTON */
.toggle-btn{
background:#10b981;
border:none;
color:white;
width:28px;
height:28px;
border-radius:6px;
cursor:pointer;
font-size:14px;
display:flex;
align-items:center;
justify-content:center;
margin-right:10px;
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

/* MAIN SMOOTH EXPAND */
.main{
margin-left:220px;
transition:all 0.4s ease;
}

/* EXPANDED MAIN */
.main.expanded{
margin-left:70px;
}


@keyframes fadeMain{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

/* ===== FORM ===== */
.form-box{
background:white;
padding:25px;
border-radius:12px;
width:50%;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.page-title{
font-family:'Playfair Display';
font-size:26px;
color:#064e3b;
}

label{
font-size:13px;
font-weight:600;
color:#064e3b;
}

.required{color:red;}

input,select{
width:100%;
padding:10px;
margin:6px 0 12px;
border-radius:8px;
border:1px solid #ccc;
}

/* ===== IOS TOGGLE ===== */
.ios-toggle{
width:45px;
height:25px;
background:#ccc;
border-radius:20px;
position:relative;
cursor:pointer;
}

.ios-toggle::after{
content:'';
width:20px;
height:20px;
background:white;
position:absolute;
top:2.5px;
left:3px;
border-radius:50%;
transition:.3s;
}

.ios-toggle.active{
background:#10b981;
}
.ios-toggle.active::after{
left:22px;
}

/* ===== TOTAL ===== */
.total-box{
background:#ecfdf5;
padding:10px;
border-radius:8px;
font-weight:600;
}

/* ===== BUTTON ===== */
button{
width:100%;
padding:13px;
background:linear-gradient(135deg,#10b981,#059669);
color:white;
border:none;
border-radius:30px;
cursor:pointer;
font-weight:600;
}

.receipt{
width:40%;
background:white;
padding:25px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
display:none;
}

.receipt h2{
font-family:'Playfair Display', serif;
text-align:center;
color:#064e3b;
}

.line{
border-bottom:1px dashed #ccc;
margin:10px 0;
}

.receipt p{
display:flex;
justify-content:space-between;
font-size:14px;
margin:6px 0;
}

.total{
font-weight:bold;
color:#064e3b;
}

.submit-again{
margin-top:15px;
background:linear-gradient(135deg,#059669,#047857);
}

.receipt-note{
margin-top:12px;
font-size:13px;
color:#065f46;
}

/* ✅ ADDED: STOP ALL ANIMATIONS AFTER SUBMIT */
.no-anim *{
animation:none !important;
transition:none !important;
}

.receipt p{
display:flex;
justify-content:space-between;
}

/* ===== CHECKBOX FIX ===== */
.checkbox-wrap{
display:flex;
align-items:center;
gap:8px;
margin-top:5px;
justify-content:flex-start; /* LEFT FIX */
}

.checkbox-wrap input{
width:auto;
margin:0;
}

/* ===== CALENDAR MODAL ===== */
.modal{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.4);
display:none;
justify-content:center;
align-items:center;
}

.modal-box{
background:white;
padding:25px;
border-radius:20px;
width:360px;
}

/* HEADER FIX (MATCH IMAGE) */
.calendar-header{
display:flex;
align-items:center;
justify-content:space-between;
margin-bottom:10px;
}

.nav-btn{
background:#16a34a;
color:white;
border:none;
padding:10px 20px;
border-radius:20px;
cursor:pointer;
}

#monthTitle{
text-align:center;
font-weight:600;
color:#064e3b;
}

/* SMALL BUTTONS */
.calendar-header button{
background:#10b981;
border:none;
color:white;
width:25px;
height:25px;
border-radius:7px;
cursor:pointer;
font-size:13px;
display:flex;
align-items:center;
justify-content:center;
padding:0;
transition:all 0.15s ease;
}

/* GRID */
.calendar{
display:grid;
grid-template-columns:repeat(7,1fr);
gap:6px;
}

.day{
padding:10px;
background:#ecfdf5;
border-radius:8px;
text-align:center;
cursor:pointer;
}

/* UNAVAILABLE */
.unavailable{
background:#fbcfe8;
color:#9d174d;
cursor:not-allowed;
}

.selected{
background:#10b981;
color:white;
}

</style>
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
<span><?php echo $loyaltyPoints; ?> pts</span>
</div>

<label>Voucher</label>
<input type="text" name="voucher">

<div class="total-box">
Total: ₱<span id="totalDisplay">0</span>
</div>

<label>Amount to Pay</label>
<input type="number" name="amount">

<label>Payment <span class="required">*</span></label>
<select name="payment" id="payment" onchange="toggleReference()" required>
<option value="" disabled selected>Select Payment</option>
<option value="GCash">GCash</option>
<option value="Maya">Maya</option>
<option value="Card">Card</option>
</select>

<div id="referenceBox" style="display:none;">
<label>Reference Number <span class="required">*</span></label>
<input type="text" name="reference" id="referenceInput">
</div>

<!-- TERMS -->
<div style="font-size:12px;">
• 50% down payment required<br>
• 30 mins late = cancelled<br>
• Rescheduling allowed<br>
• Non-refundable<br>
• No-shows restricted<br>
• Cannot cancel once started<br>
• Arrive 10 mins early
</div>

<div class="checkbox-wrap">
<input type="checkbox" required>
<label>I agree to terms</label>
</div>

<button type="submit" name="submit">Confirm Booking</button>

</form>
</div>

<!-- RECEIPT -->
<div class="receipt" id="receiptBox">

<h2>Cut & Coat Receipt</h2>

<div class="line"></div>

<p><span>Name:</span><span><?php echo $_POST['name'] ?? ''; ?></span></p>
<p><span>Phone:</span><span><?php echo $_POST['phone'] ?? ''; ?></span></p>
<p><span>Service:</span><span><?php echo $_POST['service'] ?? ''; ?></span></p>
<p><span>Date:</span><span><?php echo $_POST['date'] ?? ''; ?></span></p>

<div class="line"></div>

<p><span>Total:</span><span>₱<?php echo $total; ?></span></p>
<p><span>Downpayment:</span><span>₱<?php echo $down; ?></span></p>
<p><span>Balance:</span><span>₱<?php echo $balance; ?></span></p>

<div class="line"></div>

<p><span>Payment:</span><span><?php echo $_POST['payment'] ?? ''; ?></span></p>

<div>✔ 50% down payment</div>
<div>✔ Receipt will be sent to mobile number</div>

<button onclick="location.reload()">Submit Another</button>

</div>

</div>

<!-- CALENDAR -->
<div class="modal" id="modal">
<div class="modal-box">

<div class="calendar-header">
<button class="nav-btn" onclick="prevMonth()">◀</button>
<div id="monthTitle"></div>
<button class="nav-btn" onclick="nextMonth()">▶</button>
</div>

<div class="calendar" id="calendar"></div>

<input type="time" id="timeInput">

<button onclick="confirmDate()">Confirm</button>

</div>
</div>

<script>

let currentMonth=4;
let currentYear=2026;
let selectedDate="";
let usePoints=false;

/* SAMPLE UNAVAILABLE */
let unavailableDates=["2026-05-05","2026-05-10"];

/* TOGGLE */
function togglePoints(){
usePoints=!usePoints;
document.getElementById("toggle").classList.toggle("active");
document.getElementById("usePoints").value=usePoints?1:"";
updateTotal();
}

function updateTotal(){
let select=document.getElementById("serviceSelect");
let price=select.options[select.selectedIndex]?.dataset.price||0;
price=parseInt(price);
if(usePoints) price-=50;
if(price<0) price=0;
document.getElementById("totalDisplay").innerText=price;
}

function toggleReference(){
let p=document.getElementById("payment").value;
let refBox=document.getElementById("referenceBox");
let refInput=document.getElementById("referenceInput");

if(p==="GCash"||p==="Maya"){
refBox.style.display="block";
refInput.setAttribute("required","required");
}else{
refBox.style.display="none";
refInput.removeAttribute("required");
}
}

/* CALENDAR */
function generateCalendar(){
let cal=document.getElementById("calendar");
cal.innerHTML="";
let days=new Date(currentYear,currentMonth+1,0).getDate();

document.getElementById("monthTitle").innerText=
new Date(currentYear,currentMonth).toLocaleString('default',{month:'long',year:'numeric'});

for(let i=1;i<=days;i++){
let date=`${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
let d=document.createElement("div");
d.className="day";
d.innerText=i;

if(unavailableDates.includes(date)){
d.classList.add("unavailable");
}else{
d.onclick=()=>{ 
selectedDate=date;
document.querySelectorAll(".day").forEach(x=>x.classList.remove("selected"));
d.classList.add("selected");
};
}

cal.appendChild(d);
}
}

function openCalendar(){
document.getElementById("modal").style.display="flex";
generateCalendar();
}

function confirmDate(){
let t=document.getElementById("timeInput").value;
document.getElementById("datetime").value=selectedDate+" "+t;
document.getElementById("modal").style.display="none";
}

function prevMonth(){currentMonth--;generateCalendar();}
function nextMonth(){currentMonth++;generateCalendar();}

/* SHOW RECEIPT ONLY AFTER SUBMIT */
<?php if(isset($_POST['submit'])){ ?>
document.getElementById("receiptBox").style.display="block";
<?php } ?>


</script>

</body>
</html>