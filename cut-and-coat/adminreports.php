<?php
include 'functions/adminreports_logic.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminreports.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Monthly Sales Report</div>

<div class="grid">

<div class="card">
<h2>₱<?php echo number_format($total,2); ?></h2>
<p>Total Sales</p>
</div>

<div class="card">
<h2>₱<?php echo number_format($online,2); ?></h2>
<p>Online Sales</p>
</div>

<div class="card">
<h2>₱<?php echo number_format($walkin,2); ?></h2>
<p>Walk-In Sales</p>
</div>

<div class="card">
<h2>₱<?php echo number_format($card,2); ?></h2>
<p>Card Payments</p>
</div>

<div class="card">
<h2>₱<?php echo number_format($onlinePayments,2); ?></h2>
<p>Online Payments</p>
</div>

<div class="card">
<h2>₱<?php echo number_format($cash,2); ?></h2>
<p>Cash Payments</p>
</div>

</div>

<table>
<tr>
<th>Type</th>
<th>Payment</th>
<th>Amount</th>
</tr>

<?php foreach($data as $d){ ?>
<tr>
<td><?php echo $d["type"]; ?></td>
<td><?php echo $d["payment"]; ?></td>
<td>₱<?php echo number_format($d["amount"],2); ?></td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>