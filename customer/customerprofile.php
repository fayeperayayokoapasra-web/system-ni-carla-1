<?php
session_start();

if(!isset($_SESSION['customer'])){
    header("Location: customerlogin.php");
    exit();
}

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

function normalizePhilippinesContact($contact){
    $digits = preg_replace('/\D/', '', trim($contact));
    if(preg_match('/^09\d{9}$/', $digits)){
        return substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
    }
    return '';
}

function validatePassword($password){
    $errors = [];
    if($password === '') return $errors;
    if(strlen($password) < 8){
        $errors[] = 'Password must be at least 8 characters.';
    }
    if(!preg_match('/[a-z]/', $password)){
        $errors[] = 'Password must include a lowercase letter.';
    }
    if(!preg_match('/[A-Z]/', $password)){
        $errors[] = 'Password must include an uppercase letter.';
    }
    if(!preg_match('/\d/', $password)){
        $errors[] = 'Password must include a number.';
    }
    if(!preg_match('/[^a-zA-Z\d]/', $password)){
        $errors[] = 'Password must include a special character.';
    }
    return $errors;
}

$customers = loadCustomers($customersFile);
$customerEmail = strtolower($_SESSION['customer']);
$currentIndex = null;
$name = '';
$contact = '';
$email = $customerEmail;
$passwordHash = '';
foreach($customers as $i => $c){
    if(isset($c['email']) && strtolower($c['email']) === $customerEmail){
        $currentIndex = $i;
        $name = $c['name'] ?? '';
        $contact = $c['contact'] ?? '';
        $email = $c['email'] ?? $email;
        $passwordHash = $c['password'] ?? '';
        break;
    }
}

/* HANDLE SAVE */
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])){
    $newName = trim($_POST['name'] ?? '');
    $newContact = trim($_POST['contact'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    if($newName === ''){
        $errors[] = 'Name cannot be empty.';
    }

    $normContact = normalizePhilippinesContact($newContact);
    if($normContact === ''){
        $errors[] = 'Contact must be a valid Philippine mobile number like 09123456789.';
    }

    if(!filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'Please enter a valid email address.';
    }

    if($newPassword !== ''){
        if($newPassword !== $confirmPassword){
            $errors[] = 'Passwords do not match.';
        } else {
            $pwErrors = validatePassword($newPassword);
            if(!empty($pwErrors)){
                $errors = array_merge($errors, $pwErrors);
            }
        }
    }

    // prevent changing to an email used by another account
    foreach($customers as $i => $c){
        if($i === $currentIndex) continue;
        if(isset($c['email']) && strtolower($c['email']) === strtolower($newEmail)){
            $errors[] = 'Email is already used by another account.';
            break;
        }
    }

    if(empty($errors) && $currentIndex !== null){
        $customers[$currentIndex]['name'] = $newName;
        $customers[$currentIndex]['contact'] = $normContact;
        $customers[$currentIndex]['email'] = $newEmail;
        if($newPassword !== ''){
            $customers[$currentIndex]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        saveCustomers($customersFile, $customers);
        $_SESSION['customer'] = $newEmail;
        $name = $newName;
        $contact = $normContact;
        $email = $newEmail;
        $passwordHash = $customers[$currentIndex]['password'];
        $successMessage = 'Profile updated successfully.';
    } else {
        $errorMessage = implode(' ', $errors);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerprofile.css">
</head>

<body class="profile-page">

<!-- TOPBAR -->
<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<!-- MAIN -->
<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<!-- ✅ BACK BUTTON OUTSIDE -->
<div class="back" onclick="goBack()">← Back</div>
<div class="profile-box">

<h2>My Profile</h2>

<?php
// ensure messages are defined to avoid notices
$errorMessage = $errorMessage ?? '';
$successMessage = $successMessage ?? '';
?>

<?php if(!empty($errorMessage)): ?>
    <p class="msg error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<?php if(!empty($successMessage)): ?>
    <p class="msg"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form method="POST">

    <div class="field">
        <label>Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" disabled>
    </div>

    <div class="field">
        <label>Contact Number</label>
        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact, ENT_QUOTES, 'UTF-8'); ?>" disabled>
    </div>

    <div class="field">
        <label>Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" disabled>
    </div>

    <div class="field">
        <label>Password</label>
        <div id="passwordArea">
            <div class="password-display" id="passwordBox">●●●●●●●●</div>
            <div id="changePasswordFields" style="display:none;margin-top:8px;">
                <input type="password" name="new_password" id="new_password" placeholder="New password">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" style="margin-top:8px;">
            </div>
            <div style="margin-top:8px;"><button type="button" class="btn" onclick="toggleChangePassword()">Change Password</button></div>
        </div>
    </div>

    <div class="actions" style="margin-top:16px;">
        <button type="button" class="btn" id="editBtn" onclick="enableEdit()">Edit</button>
        <button type="submit" name="save_profile" class="btn save-btn-hidden" id="saveBtn">Save Changes</button>
    </div>

</form>

</div>

</div>

<script>

/* SIDEBAR */
/* BACK */
function goBack(){
window.location.href="customerdashboard.php";
}

/* PASSWORD TOGGLE */
let show=false;
function togglePassword(){
let box=document.getElementById("passwordBox");
if(!show){
    box.innerText = '●●●●●●●●';
    show = true;
} else {
    box.innerText = '●●●●●●●●';
    show = false;
}
}

function toggleChangePassword(){
    const fields = document.getElementById('changePasswordFields');
    if(fields.style.display === 'none' || fields.style.display === ''){
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

/* EDIT MODE */
function enableEdit(){
document.getElementById("name").disabled=false;
document.getElementById("contact").disabled=false;
document.getElementById("email").disabled=false;

document.getElementById("editBtn").style.display="none";
document.getElementById("saveBtn").style.display="inline-block";
}

</script>

</body>
</html>