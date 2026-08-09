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

function validatePassword($password){
    $errors = [];

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

if(isset($_POST['reset_password'])){
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = 'Please enter a valid email address.';
    } elseif($newPassword !== $confirmPassword){
        $message = 'Passwords do not match.';
    } else {
        $passwordErrors = validatePassword($newPassword);
        if(!empty($passwordErrors)){
            $message = implode(' ', $passwordErrors);
        } else {
            $customers = loadCustomers($customersFile);
            $updated = false;

            foreach($customers as &$customer){
                if(isset($customer['email']) && strtolower($customer['email']) === strtolower($email)){
                    $customer['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updated = true;
                    break;
                }
            }
            unset($customer);

            if($updated){
                saveCustomers($customersFile, $customers);
                $message = 'Your password has been reset successfully. You can now log in.';
            } else {
                $message = 'If an account exists for that email, your password has been reset.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Montserrat', sans-serif;
        background: #f7f2ea;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .container {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 420px;
    }
    h2 {
        font-family: 'Playfair Display', serif;
        margin-top: 0;
        margin-bottom: 10px;
        color: #4b2e1f;
    }
    p {
        color: #6b5a46;
        line-height: 1.5;
    }
    label {
        display: block;
        margin-top: 12px;
        font-weight: 600;
        color: #4b2e1f;
    }
    input {
        width: 100%;
        padding: 12px;
        margin-top: 6px;
        border: 1px solid #d8cfc3;
        border-radius: 8px;
        box-sizing: border-box;
    }
    button {
        margin-top: 18px;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: #a67c52;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
    }
    button:hover {
        background: #8f663e;
    }
    .msg {
        background: #fff7e6;
        border: 1px solid #f0d49c;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .link {
        display: block;
        margin-top: 16px;
        text-align: center;
        color: #8f663e;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="container">
    <?php if($message !== ""): ?>
        <p class="msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <h2>Reset Password</h2>
    <p>Enter the email linked to your account and choose a new password.</p>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="reset_password">Reset Password</button>
    </form>

    <a class="link" href="customerlogin.php">Back to Login</a>
</div>
</body>
</html>
