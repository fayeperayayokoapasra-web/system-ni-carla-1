<?php
session_start();
$_SESSION["admin"] = 1;
include "c:/xampp/htdocs/system-ni-carla/cut-and-coat/functions/adminwalkins_logic.php";
foreach ($_SESSION["staff_status"] as $name => $status) {
    echo $name . " => " . $status . PHP_EOL;
}
