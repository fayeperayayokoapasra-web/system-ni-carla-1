<?php
include 'functions/adminstaff_logic.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Staff</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminstaff.css">

</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
<div>
<button class="add-btn" id="addBtn" onclick="openAddModal()">+ Add Staff</button>
<button class="edit-btn" id="editToggleBtn" onclick="toggleEdit()">Edit Staff</button>
</div>
</div>



<?php include 'sidebar.php'; ?>

<div class="main" id="main">
<div class="title">Staff Management</div>

<div class="grid" id="grid">
<?php foreach($staffMembers as $index => $staff): ?>
<form method="POST" class="staff-card-form">
<input type="hidden" name="staff_index" value="<?php echo $index; ?>">
<input type="hidden" name="staff_status" value="<?php echo htmlspecialchars($staff['status'] ?? 'Available'); ?>">
<div class="card" onclick="cardClick(<?php echo $index; ?>)" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); cardClick(<?php echo $index; ?>);}">
<img src="<?php echo htmlspecialchars($staff['image'] ?? 'assets/staff/staff1.jpg'); ?>" alt="<?php echo htmlspecialchars($staff['name'] ?? 'Staff'); ?>">
<h4><?php echo htmlspecialchars($staff['name'] ?? 'Staff'); ?></h4>
<div class="info">Age: <?php echo htmlspecialchars($staff['age'] ?? ''); ?></div>
<div class="info">Contact: <?php echo htmlspecialchars($staff['contact'] ?? ''); ?></div>
<div class="info">Address: <?php echo htmlspecialchars($staff['address'] ?? ''); ?></div>
<div class="info">Joined: <?php echo htmlspecialchars($staff['joined'] ?? ''); ?></div>
<div class="info">Skills: <?php echo htmlspecialchars($staff['skills'] ?? ''); ?></div>
<button class="status-btn <?php echo (($staff['status'] ?? 'Available') === 'Unavailable') ? 'unavailable' : 'available'; ?>" onclick="toggleStatus(this, event)" type="button"><?php echo (($staff['status'] ?? 'Available') === 'Unavailable') ? 'Unavailable' : 'Available'; ?></button>
<button class="remove-btn" name="remove_staff" value="1" type="submit">x</button>
</div>
</form>
<?php endforeach; ?>
</div>
</div>

<!-- PREMIUM ADD MODAL -->
<div class="modal" id="addModal" onclick="closeAddModal(event)">
<div class="modal-content" onclick="event.stopPropagation()">
<button class="modal-close" type="button" onclick="closeAddModal()" aria-label="Close">×</button>
<h3>Add Staff</h3>
<form method="POST">
<input id="addName" name="name" placeholder="Full Name" required>
<input type="number" id="addAge" name="age" placeholder="Age" required>
<input type="text" id="addContact" name="contact" placeholder="Contact Number (0912-345-6789)" pattern="^\\(?[0-9]{4}\\)?[- ]?[0-9]{3}[- ]?[0-9]{4}$" title="Accepts: 09123456789, 0912-345-6789 or (0912) 345-6789" oninput="formatPhoneNumber(this); validateContact(this)" oninvalid="this.setCustomValidity('Use format: 0912-345-6789 or 09123456789')" required>
<input id="addAddress" name="address" placeholder="Address" required>
<input type="date" id="addJoined" name="joined" placeholder="Date Joined" required>
<input id="addSkills" name="skills" placeholder="Skills">
<input type="hidden" name="add_staff" value="1">
<button class="save-btn" type="submit">Add Staff</button>
</form>
</div>
</div>

<div class="modal" id="staffModal" onclick="closeStaffModal(event)">
<div class="modal-content" onclick="event.stopPropagation()">
<button class="modal-close" type="button" onclick="closeStaffModal()" aria-label="Close">×</button>
<h3>Edit Staff</h3>
<form method="POST">
<input type="hidden" name="staff_index" id="modalStaffIndex" value="">
<input type="text" id="modalStaffName" name="staff_name" placeholder="Full Name" required>
<input type="number" id="modalStaffAge" name="staff_age" placeholder="Age" required>
<input type="text" id="modalStaffContact" name="staff_contact" placeholder="Contact Number (0912-345-6789)" pattern="^\\(?[0-9]{4}\\)?[- ]?[0-9]{3}[- ]?[0-9]{4}$" title="Accepts: 09123456789, 0912-345-6789 or (0912) 345-6789" oninput="formatPhoneNumber(this); validateContact(this)" oninvalid="this.setCustomValidity('Use format: 0912-345-6789 or 09123456789')" required>
<input type="text" id="modalStaffAddress" name="staff_address" placeholder="Address" required>
<input type="date" id="modalStaffJoined" name="staff_joined" placeholder="Date Joined" required>
<input type="text" id="modalStaffSkills" name="staff_skills" placeholder="Skills">
<input type="hidden" name="save_staff" value="1">
<button class="save-btn" type="submit">Save Changes</button>
</form>
</div>
</div>

<script>

function toggleSidebar(){
document.getElementById("sidebar").classList.toggle("collapsed");
document.getElementById("main").classList.toggle("expanded");
}

function toggleStatus(btn, event){
    event.stopPropagation();
    const form = btn.closest('form');
    const statusInput = form ? form.querySelector('input[name="staff_status"]') : null;
    const newStatus = btn.classList.contains('unavailable') ? 'Available' : 'Unavailable';

    if(statusInput){
        statusInput.value = newStatus;
    }
    btn.classList.toggle('available', newStatus === 'Available');
    btn.classList.toggle('unavailable', newStatus === 'Unavailable');
    btn.innerText = newStatus;

    if(form){
        const hiddenToggle = document.createElement('input');
        hiddenToggle.type = 'hidden';
        hiddenToggle.name = 'toggle_status';
        hiddenToggle.value = '1';
        form.appendChild(hiddenToggle);
        form.submit();
    }
}

function toggleEdit(){
    let editBtn = document.getElementById("editToggleBtn");
    let isEditing = editBtn.innerText === "Done";
    let shouldEdit = !isEditing;

    editBtn.innerText = shouldEdit ? "Done" : "Edit Staff";
    // store mode globally
    window.isEditMode = shouldEdit;
    // hide or show Add button while editing
    const addBtn = document.getElementById('addBtn');
    if(addBtn) addBtn.style.display = shouldEdit ? 'none' : '';

    document.querySelectorAll(".remove-btn").forEach(btn=>{
        btn.style.display = shouldEdit ? "flex" : "none";
    });
    document.querySelectorAll(".save-edit-btn").forEach(btn=>{
        btn.style.display = shouldEdit ? "inline-flex" : "none";
    });
    // visual cue: add class to grid so CSS can show pointer
    document.getElementById('grid').classList.toggle('editing', shouldEdit);
}

function cardClick(index){
    // only open edit modal when in edit mode
    if(!window.isEditMode) return;
    if(typeof openStaffModal === 'function') openStaffModal(index);
}

function openAddModal(){
document.getElementById("addModal").style.display="flex";
}

function closeAddModal(event){
const modal = document.getElementById("addModal");
if (event && event.target && event.target.id === "addModal") {
modal.style.display = "none";
return;
}
modal.style.display = "none";
}

function openStaffModal(index){
const staffData = <?php echo json_encode($staffMembers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const staff = staffData[index];
if (!staff) return;

document.getElementById('modalStaffIndex').value = index;
document.getElementById('modalStaffName').value = staff.name || '';
document.getElementById('modalStaffAge').value = staff.age || '';
document.getElementById('modalStaffContact').value = staff.contact || '';
document.getElementById('modalStaffAddress').value = staff.address || '';
document.getElementById('modalStaffJoined').value = staff.joined || '';
document.getElementById('modalStaffSkills').value = staff.skills || '';

document.getElementById('staffModal').style.display = 'flex';
}

function closeStaffModal(event){
const modal = document.getElementById('staffModal');
if (event && event.target && event.target.id === 'staffModal') {
modal.style.display = 'none';
return;
}
modal.style.display = 'none';
}

function formatPhoneNumber(input){
let digits = input.value.replace(/\D/g, '').slice(0, 11);

if (digits.length > 4) {
    digits = digits.slice(0, 4) + '-' + digits.slice(4);
}
if (digits.length > 8) {
    digits = digits.slice(0, 8) + '-' + digits.slice(8);
}

input.value = digits;
}

function validateContact(input){
    // allow 09123456789 or 0912-345-6789 or (0912) 345-6789
    const re = /^\(?[0-9]{4}\)?[- ]?[0-9]{3}[- ]?[0-9]{4}$/;
    if(!re.test(input.value)){
        input.setCustomValidity('Use format: 0912-345-6789 or 09123456789');
    } else {
        input.setCustomValidity('');
    }
}

function saveStateBeforeSubmit(){
    try{
        // save scroll
        sessionStorage.setItem('adminstaff-scroll', String(window.scrollY || window.pageYOffset || 0));
        // save edit mode
        sessionStorage.setItem('adminstaff-edit', window.isEditMode ? '1' : '0');
    }catch(e){/* ignore */}
}

function restoreStateAfterLoad(){
    try{
        const v = sessionStorage.getItem('adminstaff-scroll');
        if(v !== null){
            window.scrollTo(0, parseInt(v,10) || 0);
            sessionStorage.removeItem('adminstaff-scroll');
        }
        const e = sessionStorage.getItem('adminstaff-edit');
        if(e === '1'){
            // re-enable edit mode UI
            window.isEditMode = true;
            const editBtn = document.getElementById('editToggleBtn');
            if(editBtn) editBtn.innerText = 'Done';
            const addBtn = document.getElementById('addBtn');
            if(addBtn) addBtn.style.display = 'none';
            document.querySelectorAll('.remove-btn').forEach(btn=> btn.style.display = 'flex');
            document.querySelectorAll('.save-edit-btn').forEach(btn=> btn.style.display = 'inline-flex');
            document.getElementById('grid').classList.add('editing');
            sessionStorage.removeItem('adminstaff-edit');
        }
    }catch(e){/* ignore */}
}

function setDateMinToToday(){
    const today = new Date().toISOString().split('T')[0];
    const addJoined = document.getElementById('addJoined');
    const modalStaffJoined = document.getElementById('modalStaffJoined');
    
    if(addJoined) addJoined.min = today;
    if(modalStaffJoined) modalStaffJoined.min = today;
}

document.addEventListener('DOMContentLoaded', function(){
    // attach submit handler to save state before navigation
    document.querySelectorAll('form').forEach(f=>{
        f.addEventListener('submit', saveStateBeforeSubmit, {capture:true});
    });
    restoreStateAfterLoad();
    setDateMinToToday();
});

</script>

</body>
</html>