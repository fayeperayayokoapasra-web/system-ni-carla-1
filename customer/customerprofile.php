<?php
session_start();

if(!isset($_SESSION['customer'])){
    header("Location: customerlogin.php");
    exit();
}

/* SAMPLE DATA (replace with database later) */
$name = "Carla Dela Cruz";
$contact = "09123456789";
$email = "carla@email.com";
$password = "mypassword123";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
</head>

<body class="profile-page">

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<!-- MAIN -->
<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<!-- ✅ BACK BUTTON OUTSIDE -->
<div class="back" onclick="goBack()">← Back</div>

<div class="profile-box">

<h2>My Profile</h2>

<form method="POST">

<div class="field">
<label>Name</label>
<input type="text" id="name" value="<?php echo $name; ?>" disabled>
</div>

<div class="field">
<label>Contact Number</label>
<input type="text" id="contact" value="<?php echo $contact; ?>" disabled>
</div>

<div class="field">
<label>Email Address</label>
<input type="email" id="email" value="<?php echo $email; ?>" disabled>
</div>

<div class="field">
<label>Password</label>
<div class="password-display" id="passwordBox" onclick="togglePassword()">
<?php echo str_repeat("•", strlen($password)); ?>
</div>
</div>

<!-- BUTTON SWITCH -->
<button type="button" class="btn" id="editBtn" onclick="enableEdit()">Edit</button>
<button type="submit" class="btn" id="saveBtn" style="display:none;">Save Changes</button>

</form>

</div>

</div>

<script>

/* SIDEBAR */
/* BACK */
function goBack(){
window.location.href="customerdashboard.php";
}

/* PASSWORD TOGGLE */
let show=false;
function togglePassword(){
let box=document.getElementById("passwordBox");

if(!show){
box.innerText="<?php echo $password; ?>";
show=true;
}else{
box.innerText="<?php echo str_repeat('•', strlen($password)); ?>";
show=false;
}
}

/* EDIT MODE */
function enableEdit(){
document.getElementById("name").disabled=false;
document.getElementById("contact").disabled=false;
document.getElementById("email").disabled=false;

document.getElementById("editBtn").style.display="none";
document.getElementById("saveBtn").style.display="inline-block";
}

</script>

</body>
</html>