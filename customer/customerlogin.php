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

<style>

/* ================= GLOBAL ================= */
body{
margin:0;
font-family:'Montserrat', sans-serif;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
}

/* OVERLAY SAME AS INDEX */
body::before{
content:"";
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(6,78,59,0.70);
z-index:-1;
}

/* ================= BOX ================= */
.container{
width:420px;
background:white;
padding:40px 35px;
border-radius:20px;

box-shadow:
0 20px 50px rgba(0,0,0,0.15),
0 5px 15px rgba(0,0,0,0.08);

text-align:center;
position:relative;
overflow:hidden;

/* animation */
animation:fadeIn 0.5s ease;
}

.container:hover::before{
left:130%;
}

/* ================= TITLE ================= */
h2{
font-family:'Playfair Display', serif;
color:#064e3b;
margin-bottom:20px;
font-size:26px;
}

/* ================= INPUT ================= */
label{
display:block;
text-align:left;
font-size:13px;
margin-top:12px;
color:#064e3b;
font-weight:500;
}

input{
width:100%;
padding:12px;
margin-top:5px;
border:1px solid #ddd;
border-radius:10px;
outline:none;
transition:0.3s;
font-size:13px;
}

input:focus{
border-color:#10b981;
box-shadow:0 0 10px rgba(16,185,129,0.25);
}

/* ================= BUTTON ================= */
button{
width:100%;
padding:13px;
margin-top:22px;

background:linear-gradient(135deg,#10b981,#059669);
color:white;
border:none;
border-radius:25px;

font-weight:600;
cursor:pointer;
transition:0.3s;

position:relative;
overflow:hidden;
}

/* BUTTON SHINE */
button::before{
content:"";
position:absolute;
top:0;
left:-75%;
width:50%;
height:100%;
background:rgba(255,255,255,0.3);
transform:skewX(-25deg);
transition:0.5s;
}

button:hover::before{
left:130%;
}

button:hover{
transform:translateY(-3px) scale(1.03);
box-shadow:0 10px 25px rgba(16,185,129,0.4);
}

/* ================= LINKS ================= */
.toggle{
margin-top:15px;
font-size:13px;
}

.toggle a{
color:#064e3b;
text-decoration:none;
font-weight:500;
}

.toggle a:hover{
text-decoration:underline;
}

/* ================= MESSAGE ================= */
.msg{
color:red;
font-size:13px;
margin-bottom:10px;
}

/* ================= ANIMATION ================= */
@keyframes fadeIn{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

</style>
</head>

<body>

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