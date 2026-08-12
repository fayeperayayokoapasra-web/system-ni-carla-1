<?php
include 'functions/adminlogin_logic.php';

$message = "";
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])){
    $username = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if($username === ''){
        $error = 'Please enter your admin username.';
    } elseif($newPassword !== $confirmPassword){
        $error = 'Passwords do not match.';
    } else {
        $passwordErrors = validatePassword($newPassword);
        if(!empty($passwordErrors)){
            $error = implode(' ', $passwordErrors);
        } else {
            if(updateAdminPassword($username, $newPassword)){
                $message = 'Your password has been reset successfully. You can now log in.';
            } else {
                $error = 'Admin username not found. Please check your username.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Forgot Password</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/adminlogin.css">
</head>

<body>

<div class="topbar"></div>

<div class="box">

<button class="back-btn" onclick="window.location.href='adminlogin.php'">←</button>

<img src="assets/cutandcoatLogo/logo.jpg" class="logo" alt="Logo">

<div class="welcome">Admin Password Reset</div>

<?php if($message !== ""): ?>
<p class="msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<?php if($error !== ""): ?>
<p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form method="POST">

<label>Admin Username</label>
<input type="text" name="username" placeholder="Enter username" required>

<label>New Password</label>
<input type="password" name="new_password" placeholder="New password" required>

<label>Confirm Password</label>
<input type="password" name="confirm_password" placeholder="Confirm password" required>

<button type="submit" name="reset_password">Reset Password</button>

</form>

</div>

</body>
</html>