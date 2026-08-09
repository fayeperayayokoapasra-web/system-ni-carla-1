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
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerfeedback.css">
</head>

<body class="feedback-page">

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