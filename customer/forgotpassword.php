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
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/forgotpassword.css">
</head>
<body class="forgot-password-page">
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
