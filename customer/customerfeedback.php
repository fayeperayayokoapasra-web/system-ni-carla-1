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
<title>Customer Feedback</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

/* ===== BODY ===== */
body{
margin:0;
font-family:'Poppins', sans-serif;
background:linear-gradient(135deg,#f0fdf9,#ecfdf5);
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
padding:75px 25px 25px 25px;
height:calc(100vh - 65px);
transition:0.3s;

opacity:0;
transform:translateY(15px);
animation:fadeInAdmin .5s ease forwards;
}

.main.collapsed{
margin-left:80px; /* ✅ FIXED */
}

@keyframes fadeInAdmin{
to{
opacity:1;
transform:translateY(0);
}
}

/* ===== TITLE ===== */
.title{
width:100%;
max-width:1200px;
font-family:'Playfair Display', serif;
font-size:28px;
background:white;
padding:20px;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
color:#064e3b;
margin-bottom:15px;
}

/* ===== INTRO ===== */
.subtitle{
width:100%;
max-width:1200px;
background:linear-gradient(135deg,#10b981,#059669);
color:white;
padding:20px;
border-radius:16px;
margin-bottom:25px;
font-size:15px;
line-height:1.6;
box-shadow:0 10px 25px rgba(16,185,129,0.3);
}

/* ===== FORM BOX ===== */
.form-box{
width:100%;
max-width:1200px;
background:rgba(255,255,255,0.95);
backdrop-filter:blur(10px);
padding:30px;
border-radius:20px;
box-shadow:0 20px 40px rgba(0,0,0,0.12);
transition:0.3s;
}

.form-box:hover{
transform:translateY(-4px);
}

/* ===== NOTE ===== */
.note{
text-align:center;
font-size:13px;
color:#6b7280;
margin-bottom:15px;
}

/* ===== STARS ===== */
.stars{
display:flex;
justify-content:center;
gap:12px;
font-size:34px;
cursor:pointer;
margin-bottom:20px;
}

.star{
color:#d1d5db;
transition:0.2s;
}

.star.active{
color:#facc15;
transform:scale(1.15);
}

/* ===== LABEL ===== */
.label{
font-size:14px;
color:#065f46;
font-weight:600;
margin-bottom:6px;
display:block;
}

/* ===== TEXTAREA ===== */
textarea{
width:97%;
padding:14px;
border-radius:12px;
border:1px solid #ccc;
resize:none;
height:120px;
font-size:14px;
margin-bottom:15px;
transition:0.2s;
}

textarea:focus{
border:1px solid #10b981;
outline:none;
box-shadow:0 0 0 2px rgba(16,185,129,0.2);
}

/* ===== BUTTON ===== */
.submit-btn{
background:linear-gradient(135deg,#10b981,#059669);
color:white;
border:none;
padding:14px;
width:100%;
border-radius:14px;
cursor:pointer;
font-weight:600;
font-size:15px;
transition:0.3s;
}

.submit-btn:hover{
transform:translateY(-2px);
box-shadow:0 10px 25px rgba(16,185,129,0.3);
}

/* ===== SUCCESS ===== */
.success{
margin-top:15px;
padding:12px;
background:#ecfdf5;
color:#065f46;
border-radius:10px;
display:none;
text-align:center;
border:1px solid #bbf7d0;
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

<div class="main" id="main"> <!-- ✅ FIXED -->

<div class="title">Customer Feedback</div>

<div class="subtitle">
💚 <b>We’d love to hear from you!</b><br><br>
Your feedback helps us improve our services and give you the best experience possible at Cut & Coat Nail Salon.  
Feel free to share your honest thoughts, suggestions, or concerns — your voice matters to us.
</div>

<div class="form-box">

<div class="note">This feedback is completely anonymous.</div>

<div class="stars">
<span class="star" onclick="rate(1)">★</span>
<span class="star" onclick="rate(2)">★</span>
<span class="star" onclick="rate(3)">★</span>
<span class="star" onclick="rate(4)">★</span>
<span class="star" onclick="rate(5)">★</span>
</div>

<label class="label">Comments</label>
<textarea id="comment" placeholder="Share your experience..."></textarea>

<label class="label">Suggestions</label>
<textarea id="suggestion" placeholder="Tell us how we can improve..."></textarea>

<button class="submit-btn" onclick="submitFeedback()">Submit Feedback</button>

<div class="success" id="successMsg">
Thank you for helping us improve!
</div>

</div>

</div>

<script>

let rating = 0;

function rate(value){
rating = value;

let stars = document.querySelectorAll(".star");
stars.forEach((star, index)=>{
star.classList.toggle("active", index < value);
});
}

function submitFeedback(){

let comment = document.getElementById("comment");
let suggestion = document.getElementById("suggestion");

if(rating === 0){
alert("Please select a rating.");
return;
}

if(comment.value.trim() === "" && suggestion.value.trim() === ""){
alert("Please enter a comment or suggestion.");
return;
}

document.getElementById("successMsg").style.display="block";

/* RESET */
rating = 0;
document.querySelectorAll(".star").forEach(star=>star.classList.remove("active"));
comment.value = "";
suggestion.value = "";

}

</script>

</body>
</html>