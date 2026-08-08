<?php
session_start();
$_SESSION["admin"] = 1;
$_POST["staff_index"] = 0;
$_POST["staff_status"] = "Unavailable";
include "c:/xampp/htdocs/system-ni-carla/cut-and-coat/functions/adminstaff_logic.php";
$data = json_decode(file_get_contents("c:/xampp/htdocs/system-ni-carla/cut-and-coat/functions/json/staff_data.json"), true);
echo $data[0]["status"];
