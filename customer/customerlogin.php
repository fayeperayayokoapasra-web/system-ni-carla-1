<?php
session_start();

$message = "";
$customersFile = __DIR__ . '/assets/json/customers_data.json';

function ensureJsonFile($file){
    $folder = dirname($file);
    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    if(!file_exists($file)){
        file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
    }
}

function loadCustomers($file){
    ensureJsonFile($file);
    $contents = @file_get_contents($file);
    $data = json_decode($contents, true);
    return is_array($data) ? $data : [];
}

function saveCustomers($file, $customers){
    ensureJsonFile($file);
    file_put_contents($file, json_encode($customers, JSON_PRETTY_PRINT));
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
        $normalizedContact = preg_replace('/[^0-9+]/', '', $contact);

        if(preg_match('/^09\d{9}$/', $normalizedContact)){
            $normalizedContact = '+63' . substr($normalizedContact, 1);
        } elseif(!preg_match('/^\+639\d{9}$/', $normalizedContact)){
            $message = "Please enter a Philippine mobile number like 09123456789 or +639123456789.";
        }

        if($message === ""){
            $customers = loadCustomers($customersFile);
            $existing = array_filter($customers, function($customer) use ($email){
                return strtolower($customer['email']) === strtolower($email);
            });

            if(!empty($existing)){
                $message = "This email is already registered.";
            } else {
                $_SESSION['pending_user'] = [
                    "name" => $name,
                    "contact" => $normalizedContact,
                    "email" => $email,
                    "password" => password_hash($password, PASSWORD_DEFAULT)
                ];

                $_SESSION['otp'] = "123456";

                header("Location: customerverification.php");
                exit();
            }
        }
    }
}

/* ================= LOGIN ================= */
if(isset($_POST['login'])){

    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];
    $customers = loadCustomers($customersFile);
    $customerFound = null;

    foreach($customers as $customer){
        if(strtolower($customer['email']) === strtolower($email)){
            $customerFound = $customer;
            break;
        }
    }

    if($customerFound && password_verify($password, $customerFound['password'])){
        $_SESSION['customer'] = $customerFound['email'];
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

<label>Contact No. (Philippine mobile)</label>
<input type="text" name="contact" placeholder="09123456789 or +639123456789" required>

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
