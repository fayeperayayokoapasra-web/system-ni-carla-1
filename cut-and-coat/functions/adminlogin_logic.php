<?php
session_start();

$error = "";

/* If already logged in */
if(isset($_SESSION['admin'])){
header("Location: admindashboard.php");
exit();
}

/* LOGIN CHECK */
if($_SERVER["REQUEST_METHOD"] == "POST"){

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

/* REQUIRED CREDENTIALS */
$valid_username = "cutandcoat";
$valid_password = "nailsalon";

if($username === $valid_username && $password === $valid_password){

$_SESSION['admin'] = $username;

header("Location: admindashboard.php");
exit();

} else {
$error = "Invalid username or password";
}
}
?>
