<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: adminlogin.php");
    exit();
}

$totalCustomersToday = 24;
$salesToday = 0;
$salesFile = __DIR__ . '/functions/json/sales_data.json';
$today = date('Y-m-d');
if(file_exists($salesFile)){
    $salesRaw = json_decode(file_get_contents($salesFile), true);
    if(is_array($salesRaw)){
        $salesRecords = [];
        if(isset($salesRaw['records']) && is_array($salesRaw['records'])){
            $salesRecords = $salesRaw['records'];
        } else {
            $salesRecords = $salesRaw;
        }

        foreach($salesRecords as $sale){
            $amount = isset($sale['amount']) ? (int) $sale['amount'] : 0;
            $saleDate = isset($sale['date']) ? trim((string) $sale['date']) : '';

            if($saleDate !== $today){
                continue;
            }

            $salesToday += $amount;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/admindashboard.css">
</head>

<body>

<div class="topbar">
    <h3>Cut & Coat Nail Salon</h3>

    <div class="topbar-right">
        <div class="message" id="messageIcon" onclick="openMessages()"></div>
        <span>Welcome, Admin</span>
        <a class="logout" href="?logout=true">Logout</a>
    </div>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">
    <div class="content">
        <div class="stats">
            <div class="card"><h1><?php echo $totalCustomersToday; ?></h1><p>Total Customers Today</p></div>
            <div class="card"><h1 id="salesTodayValue">₱<?php echo number_format($salesToday); ?></h1><p>Total Sales Today</p></div>
            <div class="card"><h1>18</h1><p>Completed Appointments</p></div>
        </div>

        <div class="actions">
            <div class="action-box" onclick="location.href='adminreports.php'"><h3>View Reports</h3></div>
            <div class="action-box" onclick="location.href='adminreservations.php'"><h3>View Reservations</h3></div>
            <div class="action-box" onclick="location.href='adminwalkins.php'"><h3>View Walk-Ins</h3></div>
        </div>
    </div>
</div>

<div class="message-page" id="messagePage">
    <div class="back" onclick="goBack()">← Back</div>
    <div class="chat-box" id="chatList"></div>
</div>

<div class="chat-panel" id="chatPanel">
    <div class="chat-header">
        <span id="chatName"></span>
        <span onclick="closeChat()">✖</span>
    </div>

    <div class="chat-messages" id="chatMessages"></div>

    <div class="chat-input">
        <input type="text" id="replyInput">
        <button onclick="sendReply()">Send</button>
    </div>
</div>

<script>
const sidebar = document.getElementById("sidebar");
const main = document.getElementById("main");
const messagePage = document.getElementById("messagePage");
const messageIcon = document.getElementById("messageIcon");
const chatPanel = document.getElementById("chatPanel");
const chatName = document.getElementById("chatName");
const chatMessages = document.getElementById("chatMessages");
const replyInput = document.getElementById("replyInput");

const messages = [
    ["Carla", "Hi I want to book"],
    ["Anna", "Gel polish?"],
    ["Jane", "Reschedule"],
    ["Maria", "Open hours?"],
    ["Bea", "Walk-in?"],
    ["Lisa", "Price?"],
    ["Joy", "Promo?"],
    ["Kim", "Artist?"],
    ["Ella", "Acrylic?"],
    ["Nina", "Duration?"],
    ["Lara", "Parking?"],
    ["Kate", "Cancel?"],
    ["Mia", "GCash?"],
    ["Rose", "Sunday?"],
    ["Anne", "Friend?"],
    ["Zoe", "Nail art?"],
    ["Ivy", "Waiting?"],
    ["Elle", "Walk-in later?"],
    ["Gia", "Location?"],
    ["Tina", "Thank you"]
];

function loadMessages() {
    const list = document.getElementById("chatList");
    list.innerHTML = "";
    messages.forEach((m, i) => {
        const div = document.createElement("div");
        div.className = "msg";
        div.innerHTML = "<strong>" + m[0] + ":</strong> " + m[1];
        div.onclick = function() { openChat(i, div); };
        list.appendChild(div);
    });
}

function toggleSidebar() {
    sidebar.classList.toggle("collapsed");
    main.classList.toggle("expanded");
    messagePage.classList.toggle("expanded");
}

function openMessages() {
    main.style.display = "none";
    messagePage.classList.add("active");
    messageIcon.classList.add("noNotif");
    document.body.classList.add("no-scroll");
}

function goBack() {
    main.style.display = "block";
    messagePage.classList.remove("active");
    document.body.classList.remove("no-scroll");
}

function openChat(index, el) {
    chatPanel.style.display = "flex";
    chatName.innerText = messages[index][0];
    chatMessages.innerHTML = '<div class="customer-msg">' + messages[index][1] + '</div>';
    el.classList.add("read");
}

function closeChat() {
    chatPanel.style.display = "none";
}

function sendReply() {
    const input = replyInput.value.trim();
    if (input !== "") {
        const msg = document.createElement("div");
        msg.className = "admin-msg";
        msg.innerText = input;
        chatMessages.appendChild(msg);
        replyInput.value = "";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadMessages();

    const salesValueEl = document.getElementById('salesTodayValue');
    if (salesValueEl) {
        const todayKey = 'cutAndCoatSalesToday_' + new Date().toISOString().slice(0, 10);
        const displayed = salesValueEl.textContent.replace(/[^\d]/g, '');
        const numericSales = Number(displayed || 0);

        if (!Number.isNaN(numericSales)) {
            localStorage.setItem(todayKey, String(numericSales));
        }

        const savedSales = localStorage.getItem(todayKey);
        if (savedSales && !Number.isNaN(Number(savedSales))) {
            salesValueEl.textContent = '₱' + Number(savedSales).toLocaleString();
        }
    }
});
</script>

</body>
</html>
