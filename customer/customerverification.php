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
    file_put_contents($file, json_encode($customers, JSON_PRETTY_PRINT));
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

                $_SESSION['customer'] = $pendingUser['email'];
                unset($_SESSION['pending_user']);

                header("Location: customerdashboard.php");
                exit();
            }

            header("Location: customerdashboard.php");
            exit();
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

<style>

body{
margin:0;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#064e3b,#10b981);
font-family:'Montserrat', sans-serif;
}

.container{
background:white;
width:400px;
padding:30px;
border-radius:16px;
box-shadow:0 15px 40px rgba(0,0,0,0.25);
text-align:center;
animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

h2{
font-family:'Playfair Display', serif;
color:#064e3b;
margin-bottom:10px;
}

p{
font-size:13px;
color:#064e3b;
margin-bottom:15px;
}

.otp-container{
display:flex;
justify-content:center;
gap:8px;
margin-bottom:15px;
}

.otp-container input{
width:40px;
height:45px;
text-align:center;
font-size:18px;
border:1px solid #ddd;
border-radius:8px;
outline:none;
}

.otp-container input:focus{
border-color:#10b981;
box-shadow:0 0 6px rgba(16,185,129,0.3);
}

button{
width:100%;
padding:12px;
margin-top:10px;
background:#10b981;
border:none;
color:white;
border-radius:10px;
font-weight:600;
cursor:pointer;
transition:0.3s;
}

button:hover{
background:#059669;
transform:scale(1.03);
}

.resend{
margin-top:10px;
font-size:13px;
}

.resend button{
background:none;
color:#064e3b;
border:none;
cursor:pointer;
text-decoration:underline;
padding:0;
}

.msg{
color:red;
font-size:13px;
margin-bottom:10px;
}

</style>
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

</body>
</html>