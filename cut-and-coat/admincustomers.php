<?php
include 'functions/admincustomers_logic.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Customers</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/admincustomers.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Customer History</div>

<!-- SEARCH -->
<div class="search-box">
<input type="text" id="searchInput" placeholder="Search customer..." onkeyup="searchTable()">
</div>

<div class="table-container">
<table id="customerTable">

<thead>
<tr>
<th>Name</th>
<th>Contact</th>
<th>Staff</th>
<th>Service</th>
<th>Date</th>
<th>Type</th>
<th>Payment</th>
</tr>
</thead>

<tbody>

<?php foreach($data as $d){ ?>
<tr>
<td><?php echo $d['name']; ?></td>
<td><?php echo $d['phone']; ?></td>
<td><?php echo $d['staff']; ?></td>
<td><?php echo $d['service']; ?></td>
<td><?php echo $d['datetime']; ?></td>
<td><?php echo $d['type']; ?></td>

<td><?php echo htmlspecialchars($d['payment']); ?></td>

</tr>
<?php } ?>

</tbody>
</table>
</div>

</div>

<script>
/* SEARCH FUNCTION */
function searchTable(){
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#customerTable tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>