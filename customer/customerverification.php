<?php
session_start();

$message = "";
$success = "";
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

function normalizePhilippinesContact($contact){
    $digits = preg_replace('/\D/', '', trim($contact));

    if(preg_match('/^09\d{9}$/', $digits)){
        return substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
    }

    return '';
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

/* GET CONTACT NUMBER */
$contact = isset($_SESSION['pending_user']['contact']) 
    ? normalizePhilippinesContact($_SESSION['pending_user']['contact'])
    : "your mobile number";

/* FIXED OTP (HIDDEN) */
$_SESSION['otp'] = "123456";

/* VERIFY */
if(isset($_POST['verify'])){

    $input = $_POST['code'];

    if($input === $_SESSION['otp']){
        $pendingUser = $_SESSION['pending_user'] ?? null;
        if(!$pendingUser || empty($pendingUser['email'])){
            $message = "Unable to complete verification. Please register again.";
        } else {
            $customers = loadCustomers($customersFile);
            $existing = array_filter($customers, function($customer) use ($pendingUser){
                return strtolower($customer['email']) === strtolower($pendingUser['email']);
            });

            $storedContact = normalizePhilippinesContact($pendingUser['contact']);
            if(empty($storedContact)){
                $message = "Unable to save contact number in Philippine format. Please register again.";
            } else {
                if(empty($existing)){
                    $customers[] = [
                        "name" => $pendingUser['name'],
                        "contact" => $storedContact,
                        "email" => $pendingUser['email'],
                        "password" => $pendingUser['password']
                    ];
                    saveCustomers($customersFile, $customers);
                }

                unset($_SESSION['pending_user']);
                $success = "Your account has been verified and created successfully. Please log in to continue.";
            }
        }
    } else {
        $message = "Invalid OTP code!";
    }
}

/* RESEND */
if(isset($_POST['resend'])){
    $message = "OTP has been resent!";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Verification</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customerverification.css">
</head>

<body>

<div class="container">

<h2>Verify your Account</h2>

<p>
Enter the OTP sent to <b><?php echo $contact; ?></b>
</p>

<?php if($message != ""): ?>
<p class="msg"><?php echo $message; ?></p>
<?php endif; ?>

<?php if($success != ""): ?>
<p class="success"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" onsubmit="combineOTP()">

<div class="otp-container">
<input type="text" maxlength="1" class="otp">
<input type="text" maxlength="1" class="otp">
<input type="text" maxlength="1" class="otp">
<input type="text" maxlength="1" class="otp">
<input type="text" maxlength="1" class="otp">
<input type="text" maxlength="1" class="otp">
</div>

<input type="hidden" name="code" id="fullcode">

<button type="submit" name="verify">Verify</button>

</form>

<div class="resend">
<form method="POST">
<button type="submit" name="resend">Didn't get code? Resend</button>
</form>
</div>

</div>

<script>

const inputs = document.querySelectorAll(".otp");

inputs.forEach((input, index) => {

input.addEventListener("input", () => {
if(input.value.length === 1 && index < inputs.length - 1){
inputs[index + 1].focus();
}
});

input.addEventListener("keydown", (e) => {
if(e.key === "Backspace" && input.value === "" && index > 0){
inputs[index - 1].focus();
}
});

});

function combineOTP(){
let code = "";
inputs.forEach(input => {
code += input.value;
});
document.getElementById("fullcode").value = code;
}

</script>

<?php if($success !== ""): ?>
<script>
    setTimeout(function(){ window.location.href = 'customerlogin.php'; }, 2500);
</script>
<?php endif; ?>

</body>
</html>