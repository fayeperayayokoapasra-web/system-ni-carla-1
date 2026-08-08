<?php
session_start();

$message = "";

/* SWITCH BETWEEN REGISTER & LOGIN */
$mode = isset($_GET['mode']) ? $_GET['mode'] : "register";

/* ================= REGISTER ================= */
if(isset($_POST['register'])){

    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password !== $confirm){
        $message = "Passwords do not match!";
    } else {

        $_SESSION['pending_user'] = [
            "name"=>$name,
            "contact"=>$contact,
            "email"=>$email,
            "password"=>$password
        ];

        $_SESSION['otp'] = "123456";

        header("Location: customerverification.php");
        exit();
    }
}

/* ================= LOGIN ================= */
if(isset($_POST['login'])){

    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    if($email == "cutandcoat@gmail.com" && $password == "123"){
        $_SESSION['customer'] = $email;
        header("Location: customerdashboard.php");
        exit();
    } else {
        $message = "Invalid login credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Customer Login</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
}
}
<link rel="stylesheet" href="assets/css/customer.css">
</head>

<body class="login-page">

<div class="container">

<?php if($message != ""): ?>
<p class="msg"><?php echo $message; ?></p>
<?php endif; ?>

<!-- ================= REGISTER ================= -->
<?php if($mode == "register"): ?>

<h2>Create Your Account</h2>

<form method="POST">

<label>Name</label>
<input type="text" name="name" required>

<label>Contact No.</label>
<input type="text" name="contact" required>

<label>Email Address</label>
<input type="email" name="email" required>

<label>Password</label>
<input type="password" name="password" required>

<label>Confirm Password</label>
<input type="password" name="confirm" required>

<button type="submit" name="register">Create Account</button>

</form>

<div class="toggle">
<a href="customerlogin.php?mode=login">Already have an account? Log in</a>
</div>

<?php endif; ?>

<!-- ================= LOGIN ================= -->
<?php if($mode == "login"): ?>

<h2>Customer Login</h2>

<form method="POST">

<label>Email Address</label>
<input type="email" name="login_email" required>

<label>Password</label>
<input type="password" name="login_password" required>

<button type="submit" name="login">Login</button>

</form>

<div class="toggle">
<a href="forgotpassword.php">Forgot Password?</a>
</div>

<div class="toggle">
<a href="customerlogin.php">Create new account</a>
</div>

<?php endif; ?>

</div>

</body>
</html>