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

<style>

body{
margin:0;
font-family:'Montserrat', sans-serif;
background:#f0fdf9;
}

/* TOPBAR */
.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:65px;
background:white;
display:flex;
align-items:center;
padding:0 20px;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
z-index:1000;
}

.topbar h3{
font-family:'Playfair Display', serif;
color:#064e3b;
font-size:18px;
}

/* SIDEBAR */
.sidebar{
width:200px;
height:100vh;
background:#064e3b;
position:fixed;
top:65px;
padding:15px 10px;
transition:0.3s;
}

.sidebar.collapsed{
width:60px;
}

.sidebar.collapsed a{
display:none;
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
}

.sidebar a{
display:block;
color:white;
padding:10px;
margin:4px 0;
border-radius:8px;
text-decoration:none;
font-size:13px;
}

.sidebar a:hover{
background:#10b981;
}

/* MAIN */
.main{
margin-left:240px;
padding:90px 30px;
transition:0.3s;
}

.main.collapsed{
margin-left:90px;
}

/* BACK BUTTON (OUTSIDE BOX) */
.back{
font-size:14px;
color:#065f46;
cursor:pointer;
margin-bottom:15px;
font-weight:600;
}

/* PROFILE BOX */
.profile-box{
background:white;
padding:25px;
border-radius:18px;
max-width:500px;

box-shadow:
0 10px 25px rgba(16,185,129,0.15),
0 2px 6px rgba(0,0,0,0.05);
}

.profile-box h2{
margin-top:0;
color:#065f46;
}

/* INPUT */
.field{
margin-bottom:15px;
}

.field label{
display:block;
font-size:13px;
margin-bottom:5px;
color:#065f46;
}

.field input{
width:100%;
padding:10px;
border-radius:10px;
border:1px solid #ccc;
font-size:13px;
}

/* PASSWORD DISPLAY */
.password-display{
padding:10px;
border-radius:10px;
border:1px solid #ccc;
cursor:pointer;
font-size:13px;
}

/* BUTTON */
.btn{
margin-top:10px;
background:linear-gradient(135deg,#10b981,#059669);
color:white;
border:none;
padding:10px 20px;
border-radius:25px;
cursor:pointer;
font-size:13px;
}

</style>
</head>

<body>

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