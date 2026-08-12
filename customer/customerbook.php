<?php
include 'functions/customerbook_logic.php';

// Prefill name/email from session customer if available
if(isset($_SESSION['customer'])){
    $sessionEmail = strtolower($_SESSION['customer']);
    $defaultName = '';
    foreach($serviceSections as $sec){} // noop to avoid unused warning
} else {
    $sessionEmail = '';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300,400,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../cut-and-coat/assets/css/sidebar.css">
<link rel="stylesheet" href="../cut-and-coat/assets/css/adminwalkins.css">
<link rel="stylesheet" href="assets/css/customerbook.css">
</head>

<body id="bodyTag">

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="form-box">

<div class="page-title">Book Appointment</div>

<form id="bookingForm" method="POST">

<label>Name <span class="required">*</span></label>
<input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

<label>Contact No. <span class="required">*</span></label>
<input type="text" name="phone" id="phoneInput" inputmode="numeric" maxlength="13" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" oninput="formatPhoneInput(this)" onblur="formatPhoneInput(this)" onpaste="setTimeout(() => formatPhoneInput(this), 0)">

<label>Email</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

<label>Date & Time <span class="required">*</span></label>
<input type="text" id="datetime_display" readonly onclick="openCalendar()" required value="<?php echo htmlspecialchars(!empty($_POST['date']) ? date('Y-m-d h:i A', strtotime($_POST['date'])) : ''); ?>">
<input type="hidden" id="datetime" name="date" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">

<label>Preferred Staff</label>
<select name="staff">
<option value="AUTO_ASSIGN" <?php echo (!isset($_POST['staff']) || $_POST['staff'] === 'AUTO_ASSIGN') ? 'selected' : ''; ?>>Auto-assign available staff</option>
<option value="" disabled <?php echo empty($_POST['staff']) ? 'selected' : ''; ?>>Select Staff</option>
<?php
foreach($_SESSION['staff_status'] as $name => $status){
    $optionValue = "$name ($status)";
    $disabled = ($status !== "Available") ? "disabled" : "";
    $selected = (isset($_POST['staff']) && $_POST['staff'] === $optionValue) ? 'selected' : '';
    echo "<option value=\"" . htmlspecialchars($optionValue) . "\" $disabled $selected>" . htmlspecialchars($optionValue) . "</option>";
}
?>
</select>

<label>Services <span class="required">*</span></label>
<div class="service-select-toggle" id="serviceSelectToggle" onclick="toggleServiceCheckboxes()">Select Services</div>
<div class="service-categories" id="serviceCategories" style="display:none;">
<?php foreach($serviceSections as $index => $section){ ?>
    <div class="service-category">
        <button type="button" class="category-header" onclick="toggleCategory(<?php echo $index; ?>)"><?php echo htmlspecialchars($section['title']); ?></button>
        <div class="category-services" id="categoryServices<?php echo $index; ?>" style="display:none;">
            <?php foreach($section['services'] as $service){ ?>
            <?php $checkedService = in_array($service['name'], $_POST['services'] ?? [], true) ? 'checked' : ''; ?>
            <label class="service-option">
                <input type="checkbox" name="services[]" value="<?php echo htmlspecialchars($service['name']); ?>" data-price="<?php echo $service['price']; ?>" onchange="updateTotal()" <?php echo $checkedService; ?>>
                <span><?php echo htmlspecialchars($service['name']); ?> — ₱<?php echo $service['price']; ?></span>
            </label>
            <?php } ?>
        </div>
    </div>
<?php } ?>
</div>

<div class="total-box">
Total: ₱<span id="totalDisplay">0</span>
</div>

<label>Payment <span class="required">*</span></label>
<select name="payment" id="payment" onchange="toggleReference()" required>
<option value="" disabled <?php echo empty($_POST['payment']) ? 'selected' : ''; ?>>Select Payment</option>
<option value="Cash" <?php echo (isset($_POST['payment']) && $_POST['payment'] === 'Cash') ? 'selected' : ''; ?>>Cash</option>
<option value="GCash" <?php echo (isset($_POST['payment']) && $_POST['payment'] === 'GCash') ? 'selected' : ''; ?>>GCash</option>
<option value="Maya" <?php echo (isset($_POST['payment']) && $_POST['payment'] === 'Maya') ? 'selected' : ''; ?>>Maya</option>
<option value="Card" <?php echo (isset($_POST['payment']) && $_POST['payment'] === 'Card') ? 'selected' : ''; ?>>Card</option>
</select>

<div id="referenceBox" style="display:<?php echo (isset($_POST['payment']) && in_array($_POST['payment'], ['GCash', 'Maya'], true)) ? 'block' : 'none'; ?>;">
<label>Reference Number <span class="required">*</span></label>
<input type="text" name="reference" id="referenceInput" value="<?php echo htmlspecialchars($_POST['reference'] ?? ''); ?>">
</div>

<div class="form-error" id="formError" aria-live="polite"></div>
<button type="submit" name="submit">Confirm Booking</button>
<button type="button" id="debugShowReceiptBtn" style="margin-top:8px;background:#f59e0b;" onclick="debugShowReceipt()">Show Receipt (debug)</button>

</form>

</div>

</div>

<!-- MODAL -->
<div class="modal" id="modal" onclick="if(event.target === this) closeCalendar()">
<div class="modal-box" onclick="event.stopPropagation()">
<button type="button" class="modal-close-btn" onclick="closeCalendar()" aria-label="Close calendar">×</button>

<div class="calendar-header">
<button onclick="prevMonth()">◀</button>
<h4 id="monthTitle"></h4>
<button onclick="nextMonth()">▶</button>
</div>

<div class="calendar" id="calendar"></div>

<select id="timeInput" class="time-input">
<option value="">Select time</option>
</select>
<div id="availableTimesText" class="available-times-text"></div>

<button type="button" onclick="confirmDate()">Confirm</button>

</div>
</div>

<script>


const today = new Date();
today.setHours(0,0,0,0);
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let selectedDate = "";
let selectedDayElement = null;
const availabilityData = <?php echo json_encode($availabilityLookup ?? []); ?>;
const defaultTimeSlots = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00'];

function normalizeSlot(time){
    if(!time) return '';
    let parts = time.trim().split(':');
    if(parts.length < 1) return time.trim();
    let hh = parseInt(parts[0], 10);
    let mm = parts.length > 1 ? parts[1] : '00';
    if(isNaN(hh)) return time.trim();
    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
}

function getDayAvailability(date){
    let dayInfo = availabilityData[date] || {};
    let bookedTimes = Array.isArray(dayInfo.bookedTimes) ? dayInfo.bookedTimes.map(function(time){
        let clean = time.trim();
        let normalized = normalizeSlot(clean);
        return normalized.length === 0 ? null : normalized;
    }).filter(Boolean) : [];
    bookedTimes = Array.from(new Set(bookedTimes));
    let availableTimes = defaultTimeSlots.filter(function(slot){
        return bookedTimes.indexOf(slot) === -1;
    });
    return {
        bookedTimes: bookedTimes,
        availableTimes: availableTimes,
        isFullyBooked: availableTimes.length === 0
    };
}

function generateCalendar(){
    let calendar = document.getElementById("calendar");
    calendar.innerHTML="";

    let days = new Date(currentYear, currentMonth+1,0).getDate();

    document.getElementById("monthTitle").innerText =
        new Date(currentYear,currentMonth).toLocaleString('default',{month:'long',year:'numeric'});

    for(let i=1;i<=days;i++){
        let date = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
        let dayDate = new Date(currentYear, currentMonth, i);
        dayDate.setHours(0,0,0,0);

        let div = document.createElement("div");
        div.className="day";
        div.innerText=i;

        const availability = getDayAvailability(date);
        const explicitAvailability = availabilityData[date] || {};
        const hasExplicitAvailableTimes = Array.isArray(explicitAvailability.availableTimes)
            ? explicitAvailability.availableTimes.length > 0
            : !availability.isFullyBooked;

        if(dayDate < today){
            div.classList.add("unavailable");
        } else if(availability.isFullyBooked){
            div.classList.add("booked");
        } else {
            div.onclick=function(){
                selectedDate=date;
                populateTimeOptions(date);
                showDayAvailability(date);
                    document.getElementById("datetime_display").value = selectedDate;
            }
        }

        calendar.appendChild(div);
    }
}

function populateTimeOptions(date){
    let select = document.getElementById("timeInput");
    let availability = getDayAvailability(date);
    let bookedTimes = availability.bookedTimes;
    let availableTimes = availability.availableTimes;

    select.innerHTML = '<option value="">Select time</option>';

    defaultTimeSlots.forEach(function(time){
        let option = document.createElement('option');
        option.value = time;
        if(bookedTimes.indexOf(time) !== -1){
            option.disabled = true;
            option.textContent = formatTo12Hour(time) + ' (Unavailable)';
        } else {
            option.textContent = formatTo12Hour(time);
        }
        select.appendChild(option);
    });

    select.value = "";
    select.disabled = availableTimes.length === 0;
}

function formatTo12Hour(time24){
    if(!time24) return '';
    let parts = time24.split(':');
    if(parts.length < 1) return time24;
    let hh = parseInt(parts[0],10);
    let mm = parts.length>1? parts[1] : '00';
    let suffix = hh >= 12 ? 'PM' : 'AM';
    let hour12 = hh % 12;
    if(hour12 === 0) hour12 = 12;
    return hour12 + ':' + mm + ' ' + suffix;
}

function showDayAvailability(date){
    let availability = getDayAvailability(date);
    let textBox = document.getElementById("availableTimesText");
    if(availability.availableTimes.length){
        let prettyAvailable = availability.availableTimes.map(formatTo12Hour);
        textBox.innerText = 'Available times: ' + prettyAvailable.join(', ');
    } else {
        let prettyBooked = availability.bookedTimes.map(formatTo12Hour);
        textBox.innerText = 'All slots are unavailable. Booked times: ' + prettyBooked.join(', ');
    }
}

function openCalendar(){
document.getElementById("modal").style.display="flex";
generateCalendar();
}

function closeCalendar(){
document.getElementById("modal").style.display="none";
}

function confirmDate(){
let time=document.getElementById("timeInput").value;
if(!selectedDate || !time){
    alert("Please select a date and an available time.");
    return;
}
let displayTime = formatTo12Hour(time);
document.getElementById("datetime_display").value = selectedDate+" "+displayTime;
document.getElementById("datetime").value = selectedDate+" "+time;
document.getElementById("modal").style.display="none";
}

document.getElementById('bookingForm').addEventListener('submit', function(event){
    event.preventDefault();
    let errorText = '';
    let serviceChecked = document.querySelectorAll('input[name="services[]"]:checked').length > 0;
    let dateValue = document.getElementById('datetime').value.trim();
    if(!serviceChecked){
        errorText = 'Please select at least one service.';
    } else if(!dateValue){
        errorText = 'Please choose a date and time.';
    }

    let formError = document.getElementById('formError');
    if(errorText){
        formError.innerText = errorText;
        return false;
    }
    formError.innerText = '';

    let form = document.getElementById('bookingForm');
    let fd = new FormData(form);
    fd.append('ajax', '1');
    fd.append('submit', '1');

    fetch('functions/customerbook_logic.php', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(res){
        if(!res.ok) throw new Error('Network error');
        return res.text();
    })
    .then(function(text){
        var data = null;
        try{ data = JSON.parse(text); }catch(err){ console.error('Failed to parse JSON response, response:', text); }
        if(!data){
            var formEl = document.getElementById('bookingForm');
            var fd2 = new FormData(formEl);
            var services = fd2.getAll('services[]');
            data = {
                name: fd2.get('name') || '',
                phone: fd2.get('phone') || '',
                email: fd2.get('email') || '',
                staff: fd2.get('staff') || '',
                date: fd2.get('date') || '',
                payment: fd2.get('payment') || '',
                reference: fd2.get('reference') || '',
                services: services,
                total: document.getElementById('totalDisplay') ? Number(document.getElementById('totalDisplay').innerText||0) : 0
            };
            data.down = data.total * 0.5;
            data.balance = data.total - data.down;
        }

        (function(data){
            var setText = function(id, value){ var el = document.getElementById(id); if(el) el.innerText = value || ''; };
            setText('mr_name', data.name);
            setText('mr_phone', data.phone);
            setText('mr_email', data.email);
            setText('mr_staff', data.staff);
            setText('mr_date', data.date);
            setText('mr_payment', data.payment);
            setText('mr_ref', data.reference || 'N/A');
            setText('mr_total', '₱' + (data.total || 0));
            setText('mr_down', '₱' + (data.down || 0));
            setText('mr_balance', '₱' + (data.balance || 0));

            var mserviceList = document.getElementById('mr_service'); if(mserviceList) mserviceList.innerHTML = '';
            if(Array.isArray(data.services)){
                data.services.forEach(function(s){ var li = document.createElement('li'); li.innerText = s; if(mserviceList) mserviceList.appendChild(li); });
            }

            showReceiptModal();
        })(data);
    })
    .catch(function(err){
        formError.innerText = 'Failed to submit booking. Try again.';
        console.error(err);
    });

    return false;
});

function prevMonth(){ currentMonth--; generateCalendar(); }
function nextMonth(){ currentMonth++; generateCalendar(); }

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

function toggleServiceCheckboxes(){
let box = document.getElementById("serviceCategories");
let toggle = document.getElementById("serviceSelectToggle");
if(!box || !toggle) return;
if(box.style.display === "flex" || box.style.display === "block"){
    box.style.display = "none";
    toggle.innerText = "Select Services";
}else{
    box.style.display = "block";
    toggle.innerText = "Hide Services";
}
}

document.addEventListener('DOMContentLoaded', function(){
    updateTotal();
    toggleReference();
});

function toggleCategory(index){
let category = document.getElementById("categoryServices" + index);
if(!category) return;
if(category.style.display === "block"){
    category.style.display = "none";
}else{
    category.style.display = "block";
}
}

function updateTotal(){
let total = 0;
document.querySelectorAll('input[name="services[]"]:checked').forEach(function(checkbox){
    total += Number(checkbox.getAttribute('data-price')) || 0;
});
document.getElementById("totalDisplay").innerText = total;
}

function formatPhoneInput(input){
    if(!input) return;
    let start = input.selectionStart;
    let value = input.value.replace(/[^0-9]/g, '');
    if(value.length > 11){ value = value.slice(0, 11); }
    let formatted = value;
    if(value.startsWith('0')){
        if(value.length <= 4){ formatted = value; }
        else if(value.length <= 7){ formatted = value.slice(0, 4) + '-' + value.slice(4); }
        else { formatted = value.slice(0, 4) + '-' + value.slice(4, 7) + '-' + value.slice(7); }
    }
    let previous = input.value;
    input.value = formatted;
}

// Receipt modal controls
function showReceiptModal(){
    var modal = document.getElementById('receiptModal');
    if(!modal) return;
    modal.style.setProperty('display', 'flex', 'important');
    modal.style.setProperty('visibility', 'visible', 'important');
    modal.style.setProperty('opacity', '1', 'important');
    modal.style.setProperty('z-index', '99999', 'important');
    modal.classList.add('show');
}

function debugShowReceipt(){
    try{
        var formEl = document.getElementById('bookingForm');
        var fd = new FormData(formEl);
        var services = fd.getAll('services[]');
        var data = {
            name: fd.get('name') || '',
            phone: fd.get('phone') || '',
            email: fd.get('email') || '',
            staff: fd.get('staff') || '',
            date: fd.get('date') || '',
            payment: fd.get('payment') || '',
            reference: fd.get('reference') || '',
            services: services,
            total: document.getElementById('totalDisplay') ? Number(document.getElementById('totalDisplay').innerText||0) : 0
        };
        data.down = data.total * 0.5;
        data.balance = data.total - data.down;

        var setText = function(id, value){ var el = document.getElementById(id); if(el) el.innerText = value || ''; };
        setText('mr_name', data.name);
        setText('mr_phone', data.phone);
        setText('mr_email', data.email);
        setText('mr_staff', data.staff);
        setText('mr_date', data.date);
        setText('mr_payment', data.payment);
        setText('mr_ref', data.reference || 'N/A');
        setText('mr_total', '₱' + (data.total || 0));
        setText('mr_down', '₱' + (data.down || 0));
        setText('mr_balance', '₱' + (data.balance || 0));

        var mserviceList = document.getElementById('mr_service'); if(mserviceList) mserviceList.innerHTML = '';
        if(Array.isArray(data.services)){
            data.services.forEach(function(s){ var li = document.createElement('li'); li.innerText = s; if(mserviceList) mserviceList.appendChild(li); });
        }

        showReceiptModal();
    }catch(e){ console.error('debugShowReceipt error', e); }
}

</script>

<!-- RECEIPT POPUP PANEL -->
<div class="receipt-modal" id="receiptModal">
    <div class="receipt-content" role="dialog" aria-modal="true">
        <div class="receipt-header">
            <img src="assets/cutandcoatFiles/logo.jpg" alt="Cut & Coat Logo" class="receipt-logo">
            <h2>Cut & Coat Receipt</h2>
        </div>
        <div class="line"></div>
        <p><span>Name:</span><span id="mr_name"></span></p>
        <p><span>Phone:</span><span id="mr_phone"></span></p>
        <p><span>Email:</span><span id="mr_email"></span></p>
        <div class="receipt-services">
            <span>Service:</span>
            <ul id="mr_service"></ul>
        </div>
        <p><span>Staff:</span><span id="mr_staff"></span></p>
        <p><span>Date:</span><span id="mr_date"></span></p>
        <div class="line"></div>
        <p class="total"><span>Total:</span><span id="mr_total">₱0</span></p>
        <p class="total"><span>Downpayment:</span><span id="mr_down">₱0</span></p>
        <p class="total"><span>Balance:</span><span id="mr_balance">₱0</span></p>
        <div class="line"></div>
        <p><span>Payment:</span><span id="mr_payment"></span></p>
        <p><span>Reference:</span><span id="mr_ref"></span></p>
        <div class="receipt-note">✔ 50% down payment</div>
        <div class="receipt-note">✔ Receipt will be sent to mobile number</div>
        <button class="submit-again" onclick="location.href='customerbook.php'">Submit Another</button>
    </div>
</div>

</body>
</html>