<?php
session_start();

if(!isset($_SESSION['admin'])){
header("Location: adminlogin.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Feedback</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminfeedback.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<?php include 'sidebar.php'; ?>

<div class="main" id="main">
<div class="title">Customer Feedback</div>

<div class="table-container">
<table>
<tr>
<th>Rating</th>
<th>Comment</th>
<th>Suggestion</th>
</tr>

<?php
$feedbacks = [
["★★★★★","Excellent service and friendly staff!","Keep it up!"],
["★★★★☆","Very clean and relaxing.","Add more nail colors."],
["★★★★★","Loved my nails!","None"],
["★★★☆☆","Service was okay.","Improve waiting time."],
["★★★★★","Best salon experience!","More promos please."],
["★★★★☆","Staff are polite.","Extend operating hours."],
["★★★★★","Super satisfied!","None"],
["★★★☆☆","Good but slow service.","Hire more staff."],
["★★★★★","Amazing nail art!","More designs."],
["★★★★☆","Nice ambiance.","Music could be better."],
["★★★★★","Highly recommended!","None"],
["★★★☆☆","Average service.","Improve staff training."],
["★★★★★","Very professional.","Keep consistency."],
["★★★★☆","Clean place.","More seats."],
["★★★★★","Loved everything!","None"],
["★★★☆☆","Okay experience.","Faster service."],
["★★★★★","Staff are amazing!","None"],
["★★★★☆","Good value.","More discounts."],
["★★★★★","Perfect nails!","None"],
["★★★☆☆","Could be better.","Better scheduling."]
];

foreach($feedbacks as $f){
echo "<tr>
<td class='stars'>{$f[0]}</td>
<td>{$f[1]}</td>
<td>{$f[2]}</td>
</tr>";
}
?>

</table>
</div>

</div>

<script>

</script>

</body>
</html>