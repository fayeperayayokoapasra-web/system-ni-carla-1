<?php
session_start();

$error = "";
$adminCredentialsFile = __DIR__ . '/json/admin_data.json';

function ensureJsonFile($file, $defaultData = []){
    $folder = dirname($file);
    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    if(!file_exists($file)){
        file_put_contents($file, json_encode($defaultData, JSON_PRETTY_PRINT));
    }
}

function getDefaultAdminData(){
    return [
        [
            'username' => 'cutandcoat',
            'password' => password_hash('nailsalon', PASSWORD_DEFAULT),
        ],
    ];
}

function loadAdminCredentials(){
    global $adminCredentialsFile;
    ensureJsonFile($adminCredentialsFile, getDefaultAdminData());
    $contents = @file_get_contents($adminCredentialsFile);
    $data = json_decode($contents, true);
    return is_array($data) ? $data : getDefaultAdminData();
}

function saveAdminCredentials(array $admins){
    global $adminCredentialsFile;
    ensureJsonFile($adminCredentialsFile, getDefaultAdminData());

    $json = json_encode($admins, JSON_PRETTY_PRINT);
    if($json === false || file_put_contents($adminCredentialsFile, $json, LOCK_EX) === false){
        throw new RuntimeException('Unable to save admin credentials.');
    }
}

function findAdminByUsername(string $username){
    $admins = loadAdminCredentials();
    foreach($admins as $admin){
        if(isset($admin['username']) && strtolower($admin['username']) === strtolower($username)){
            return $admin;
        }
    }
    return null;
}

function findAdminIndexByUsername(string $username){
    $admins = loadAdminCredentials();
    foreach($admins as $index => $admin){
        if(isset($admin['username']) && strtolower($admin['username']) === strtolower($username)){
            return $index;
        }
    }
    return null;
}

function updateAdminPassword(string $username, string $newPassword){
    $index = findAdminIndexByUsername($username);
    if($index === null){
        return false;
    }

    $admins = loadAdminCredentials();
    $admins[$index]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    saveAdminCredentials($admins);
    return true;
}

function validatePassword(string $password){
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

if(isset($_SESSION['admin'])){
    header("Location: admindashboard.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['reset_password'])){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $storedAdmin = findAdminByUsername($username);
    if($storedAdmin && password_verify($password, $storedAdmin['password'])){
        $_SESSION['admin'] = $storedAdmin['username'];
        header("Location: admindashboard.php");
        exit();
    }

    $error = "Invalid username or password";
}
?>
