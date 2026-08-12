<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$bookingsFile = __DIR__ . '/functions/json/bookings_data.json';
$bookings = [];
$staffSchedule = [];
$staffMembers = [];

$staffFile = __DIR__ . '/functions/json/staff_data.json';
if(file_exists($staffFile)){
    $staffData = json_decode(file_get_contents($staffFile), true);
    if(is_array($staffData)){
        foreach($staffData as $staff){
            if(!is_array($staff) || empty($staff['name'])){
                continue;
            }
            $staffMembers[] = trim((string)$staff['name']);
        }
    }
}
if(empty($staffMembers)){
    $staffMembers = ['Anna', 'Rhea', 'Kim'];
}

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

function normalizeStaffName($staffString){
    $staffString = trim((string)$staffString);
    if(preg_match('/^(.+?)\s*\(/', $staffString, $matches)){
        return trim($matches[1]);
    }
    return $staffString;
}

function parseBookingDateTime($dateString){
    $dateString = trim((string)$dateString);
    $formats = ['Y-m-d h:i A','Y-m-d H:i','Y-m-d H:i A','Y-m-d h:i','Y-m-d H:i:s'];
    foreach($formats as $format){
        $dt = DateTime::createFromFormat($format, $dateString);
        if($dt){
            return $dt;
        }
    }
    $timestamp = strtotime($dateString);
    return $timestamp !== false ? new DateTime('@' . $timestamp) : false;
}

function recordStaffBooking(array &$staffSchedule, string $date, string $staff, string $time, string $name, string $source){
    if($staff === ''){
        return;
    }
    if(!isset($staffSchedule[$date])){
        $staffSchedule[$date] = [];
    }
    $staffSchedule[$date][] = [
        'staff' => $staff,
        'time' => $time,
        'name' => $name,
        'source' => $source,
    ];
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
            $dateTime = parseBookingDateTime($dtString);
            if(!$dateTime){
                continue;
            }

            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('g:i A');
            $name = trim($customer['name']);
            if($name === ''){
                $name = 'Guest';
            }
            $staff = normalizeStaffName($customer['staff'] ?? '');

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
            recordStaffBooking($staffSchedule, $date, $staff, $time, $name, $customer['type'] ?? 'Online');
        }
    }
}

$walkinsFile = __DIR__ . '/functions/json/walkins_data.json';
if(file_exists($walkinsFile)){
    $walkins = json_decode(file_get_contents($walkinsFile), true);
    if(is_array($walkins)){
        foreach($walkins as $walkin){
            if(!is_array($walkin) || empty($walkin['date']) || empty($walkin['name'])){
                continue;
            }

            $dtString = trim($walkin['date']);
            $dateTime = parseBookingDateTime($dtString);
            if(!$dateTime){
                continue;
            }

            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('g:i A');
            $name = trim($walkin['name']);
            if($name === ''){
                $name = 'Guest';
            }
            $staff = normalizeStaffName($walkin['staff'] ?? '');

            recordStaffBooking($staffSchedule, $date, $staff, $time, $name, 'Walk-In');
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

    <div class="modal-staff-schedule" id="modalStaffSchedule">
      <h4>Staff schedule for this date</h4>
      <div id="staffScheduleList" class="bookings-list">No staff assignments yet.</div>
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
let staffSchedule = <?php echo json_encode($staffSchedule); ?>;
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
    const staffScheduleList = document.getElementById("staffScheduleList");
    const dateBookings = bookings[date] || [];
    const dateStaffSchedule = staffSchedule[date] || [];

    if(dateBookings.length === 0){
        bookingsList.innerHTML = '<div class="empty">No reservations yet for this date.</div>';
    } else {
        bookingsList.innerHTML = dateBookings.map(b => {
            return `<div class="booking-entry"><span class="booking-time">${b.time}</span><span class="booking-name"><br>${b.name}<br></span></div>`;
        }).join('');
    }

    if(dateStaffSchedule.length === 0){
        staffScheduleList.innerHTML = '<div class="empty">No staff assignments yet.</div>';
    } else {
        staffScheduleList.innerHTML = dateStaffSchedule.map(b => {
            const source = b.source ? ` <span class="booking-source">(${b.source})</span>` : '';
            return `<div class="booking-entry"><span class="booking-time">${b.time}</span><span class="booking-name">${b.staff}</span><span class="booking-name">${b.name}${source}</span></div>`;
        }).join('');
    }
}

/* INIT */
generate();

</script>

</body></html>