<?php
include 'functions/adminlogin_logic.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/adminlogin.css">
</head>

<body>

<div class="topbar"></div>

<div class="box">

<!-- ✅ PURE ARROW ONLY -->
<button class="back-btn" onclick="window.location.href='index.php'">←</button>

<img src="assets/cutandcoatLogo/logo.jpg" class="logo" alt="Logo">

<div class="welcome">Welcome, Admin</div>

<?php if($error != ""): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">

<label>Username</label>
<input type="text" name="username" placeholder="Enter username" required>

<label>Password</label>
<input type="password" name="password" placeholder="Enter password" required>

<button type="submit">Log In</button>

</form>

<div class="forgot">
<a href="#">Forgot Password?</a>
</div>

</div>

</body>
</html>