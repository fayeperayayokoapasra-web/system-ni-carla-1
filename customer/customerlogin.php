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
    $json = json_encode($customers, JSON_PRETTY_PRINT);
    if($json === false || file_put_contents($file, $json, LOCK_EX) === false){
        throw new RuntimeException('Unable to save customers data.');
    }
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
        $digits = preg_replace('/\D/', '', $contact);
        $normalizedContact = '';

        if(preg_match('/^09\d{9}$/', $digits)){
            $normalizedContact = substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
        } else {
            $message = "Please enter a Philippine mobile number like 09123456789.";
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
<input type="tel" name="contact" id="contactInput" placeholder="0912-345-6789" pattern="^\(?09\d{2}\)?[- ]?\d{3}[- ]?\d{4}$" title="Enter 09123456789 or 0912-345-6789" oninput="formatPhoneNumber(this); validateContact(this)" oninvalid="this.setCustomValidity('Use format: 0912-345-6789 or 09123456789')" required>

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

<script>
function formatPhoneNumber(input){
    let digits = input.value.replace(/\D/g, '').slice(0, 11);
    if(digits.length > 4){
        digits = digits.slice(0, 4) + '-' + digits.slice(4);
    }
    if(digits.length > 8){
        digits = digits.slice(0, 8) + '-' + digits.slice(8);
    }
    input.value = digits;
}

function validateContact(input){
    const re = /^\(?09\d{2}\)?[- ]?\d{3}[- ]?\d{4}$/;
    if(!re.test(input.value)){
        input.setCustomValidity('Use format: 0912-345-6789 or 09123456789');
    } else {
        input.setCustomValidity('');
    }
}
</script>
</body>
</html>
