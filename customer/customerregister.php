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
    $json = json_encode($customers, JSON_PRETTY_PRINT);
    if($json === false || file_put_contents($file, $json, LOCK_EX) === false){
        throw new RuntimeException('Unable to save customers data.');
    }
}

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
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Customer Register</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
</head>

<body class="login-page">

<div class="container">

<?php if($message != ""): ?>
<p class="msg"><?php echo $message; ?></p>
<?php endif; ?>

<h2>Create Your Account</h2>

<form method="POST">

<label>Name</label>
<input type="text" name="name" required>

<label>Contact No. (Philippine mobile)</label>
<input type="tel" name="contact" id="contactInput" inputmode="numeric" maxlength="13" placeholder="09123456789" pattern="^09\d{9}$|^09\d{2}-\d{3}-\d{4}$" title="Enter 09123456789 or 0912-345-6789" oninput="formatPhoneNumberInput(this)" onfocus="formatPhoneNumberFocus(this)" onblur="formatPhoneNumberOnBlur(this)" oninvalid="this.setCustomValidity('Use format: 09123456789 or 0912-345-6789')" required>

<label>Email Address</label>
<input type="email" name="email" required>

<label>Password</label>
<input type="password" name="password" required>

<label>Confirm Password</label>
<input type="password" name="confirm" required>

<button type="submit" name="register">Create Account</button>

</form>

<div class="toggle">
<a href="customerlogin.php">Already have an account? Log in</a>
</div>

</div>

<script>
function formatPhoneNumberInput(input) {
    const digits = input.value.replace(/\D/g, '');
    input.value = digits;
}

function formatPhoneNumberFocus(input) {
    const digits = input.value.replace(/\D/g, '');
    input.value = digits;
}

function formatPhoneNumberOnBlur(input) {
    let digits = input.value.replace(/\D/g, '').slice(0, 11);
    if (digits.length === 11 && digits.startsWith('09')) {
        input.value = digits.slice(0, 4) + '-' + digits.slice(4, 7) + '-' + digits.slice(7);
    } else {
        input.value = digits;
    }
}

function validateContact(input){
    const re = /^09\d{9}$|^09\d{2}-\d{3}-\d{4}$/;
    if(!re.test(input.value)){
        input.setCustomValidity('Use format: 09123456789 or 0912-345-6789');
    } else {
        input.setCustomValidity('');
    }
}
</script>
</body>
</html>
