<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$bookingsFile = __DIR__ . '/functions/json/bookings_data.json';
$bookings = [];

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookings_data'])){
    $postedBookings = json_decode($_POST['bookings_data'], true);
    if(is_array($postedBookings)){
        if(!is_dir(dirname($bookingsFile))){
            mkdir(dirname($bookingsFile), 0777, true);
        }
        file_put_contents($bookingsFile, json_encode($postedBookings, JSON_PRETTY_PRINT));
        header('Location: adminsched.php');
        exit();
    }
}

if(file_exists($bookingsFile)){
    $bookings = json_decode(file_get_contents($bookingsFile), true);
    if(!is_array($bookings)){
        $bookings = [];
    }
}

$customersFile = __DIR__ . '/functions/json/customers_data.json';
if(file_exists($customersFile)){
    $customers = json_decode(file_get_contents($customersFile), true);
    if(is_array($customers)){
        foreach($customers as $customer){
            if(!is_array($customer) || empty($customer['datetime']) || empty($customer['name'])){
                continue;
            }

            $dtString = trim($customer['datetime']);
            $dateTime = DateTime::createFromFormat('Y-m-d h:i A', $dtString)
                ?: DateTime::createFromFormat('Y-m-d H:i', $dtString)
                ?: DateTime::createFromFormat('Y-m-d H:i A', $dtString)
                ?: DateTime::createFromFormat('Y-m-d h:i', $dtString)
                ?: DateTime::createFromFormat('Y-m-d H:i:s', $dtString);

            if(!$dateTime){
                continue;
            }

            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('g:i A');
            $name = trim($customer['name']);
            if($name === ''){
                $name = 'Guest';
            }

            if(!isset($bookings[$date]) || !is_array($bookings[$date])){
                $bookings[$date] = [];
            }

            $exists = false;
            foreach($bookings[$date] as $existing){
                if(isset($existing['name'], $existing['time']) && $existing['name'] === $name && $existing['time'] === $time){
                    $exists = true;
                    break;
                }
            }

            if(!$exists){
                $bookings[$date][] = ['name' => $name, 'time' => $time];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reservation Schedule</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminsched.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Reservation Schedule</div>

<div class="calendar-box">

<div class="cal-header">
<button onclick="prevMonth()">◀</button>
<h2 id="monthTitle"></h2>
<button onclick="nextMonth()">▶</button>
</div>

<div class="weekdays">
<div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
</div>

<div class="days" id="days"></div>

</div>

</div>

<!-- MODAL -->
<div class="modal" id="modal">
  <div class="modal-box">

    <h3 id="dateLabel">2026-04-07</h3>

    <div class="modal-bookings" id="modalBookings">
      <h4>Reserved for this date</h4>
      <div id="bookingsList" class="bookings-list">No reservations yet.</div>
    </div>

    <div class="modal-actions">
      <button class="cancel" onclick="closeModal()">Close</button>
    </div>

  </div>
</div>

<script>

/* 🔥 FIXED TOGGLE */
function toggleSidebar(){
document.getElementById("sidebar").classList.toggle("collapsed");
document.getElementById("main").classList.toggle("expand");
}

let bookings = <?php echo json_encode($bookings); ?>;
let selectedDate="";
let month=new Date().getMonth();
let year=new Date().getFullYear();

function isPastDate(dateString){
let today=new Date();
today.setHours(0,0,0,0);
let selected=new Date(dateString+"T00:00:00");
return selected < today;
}


/* CALENDAR */
function generate(){

let days=document.getElementById("days");
days.innerHTML="";

let first=new Date(year,month,1).getDay();
let total=new Date(year,month+1,0).getDate();

document.getElementById("monthTitle").innerText =
new Date(year,month).toLocaleString('default',{month:'long',year:'numeric'});

for(let i=0;i<first;i++) days.innerHTML+="<div></div>";

for(let d=1; d<=total; d++){

let date=year+"-"+String(month+1).padStart(2,'0')+"-"+String(d).padStart(2,'0');
let isPast=isPastDate(date);
let hasBookings = Array.isArray(bookings[date]) && bookings[date].length > 0;

let div=document.createElement("div");
div.className="day";

if(isPast && !hasBookings){
    div.classList.add("disabled");
}

let html = "<div class='date-num'>"+d+"</div>";

if(bookings[date]){

if(bookings[date].length>=12){
div.classList.add("full");
}

bookings[date].forEach(b=>{
html += "<span class='booking'>"+b.time+" - "+b.name+"</span>";
});
}

div.innerHTML = html;

if(!isPast || hasBookings){
    div.onclick=()=>openModal(date);
}

days.appendChild(div);
}
}

function prevMonth(){month--;generate();}
function nextMonth(){month++;generate();}

/* MODAL */
function openModal(date){
    selectedDate=date;
    document.getElementById("modal").style.display="flex";
    document.getElementById("dateLabel").innerText=date;
    renderBookingsForDate(date);
}

function closeModal(){
    document.getElementById("modal").style.display="none";
}

function renderBookingsForDate(date){
    const bookingsList = document.getElementById("bookingsList");
    const dateBookings = bookings[date] || [];
    if(dateBookings.length === 0){
        bookingsList.innerHTML = '<div class="empty">No reservations yet for this date.</div>';
        return;
    }
    bookingsList.innerHTML = dateBookings.map(b => {
        return `<div class="booking-entry"><span class="booking-time">${b.time}</span><span class="booking-name"><br>${b.name}<br></span></div>`;
    }).join('');
}

/* INIT */
generate();

</script>

</body></html>