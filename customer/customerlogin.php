<?php
session_start();

$message = "";
$accountsFile = __DIR__ . '/accounts_data.json';

function loadCustomerAccounts($file){
    if(!file_exists($file)){
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function saveCustomerAccounts($file, $accounts){
    file_put_contents($file, json_encode(array_values($accounts), JSON_PRETTY_PRINT));
}

function findAccountByEmail($accounts, $email){
    foreach($accounts as $account){
        if(isset($account['email']) && strtolower($account['email']) === strtolower($email)){
            return $account;
        }
    }
    return null;
}

/* SWITCH BETWEEN REGISTER & LOGIN */
$mode = isset($_GET['mode']) ? $_GET['mode'] : "register";

/* ================= REGISTER ================= */
if(isset($_POST['register'])){

    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password !== $confirm){
        $message = "Passwords do not match!";
    } else {
        $accounts = loadCustomerAccounts($accountsFile);

        if(findAccountByEmail($accounts, $email) !== null){
            $message = "This email is already registered. Please log in instead.";
        } else {
            $_SESSION['pending_user'] = [
                "name" => $name,
                "contact" => $contact,
                "email" => $email,
                "password" => $password
            ];

            $_SESSION['otp'] = "123456";

            header("Location: customerverification.php");
            exit();
        }
    }
}

/* ================= LOGIN ================= */
if(isset($_POST['login'])){

    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];
    $accounts = loadCustomerAccounts($accountsFile);
    $account = findAccountByEmail($accounts, $email);

    if($account !== null){
        $storedPassword = $account['password'] ?? '';
        $authenticated = false;

        if(password_verify($password, $storedPassword)){
            $authenticated = true;
        } elseif($password === $storedPassword) {
            $authenticated = true;
        }

        if($authenticated){
            $_SESSION['customer'] = $email;
            header("Location: customerdashboard.php");
            exit();
        }
    }

    $message = "Invalid login credentials!";
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