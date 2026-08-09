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
<link rel="stylesheet" href="assets/css/customerauth.css">
</head>

<body class="login-page">

<div class="container">

<?php if($message != ""): ?>
<p class="msg"><?php echo $message; ?></p>
<?php endif; ?>

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
<a href="customerregister.php">Create new account</a>
</div>

</div>

</body>
</html>
