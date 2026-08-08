<?php
session_start();

if(!isset($_SESSION['admin'])){
header("Location: adminlogin.php");
exit();
}

$contact = $_POST['Contact'];

if (!preg_match('/^09\d{9}$/', $contact)) {
    echo "Invalid contact number.";
}

$staffFile = __DIR__ . '/json/staff_data.json';
$staffDir = dirname($staffFile);
if(!is_dir($staffDir)){
    mkdir($staffDir, 0777, true);
}


if(!file_exists($staffFile)){
file_put_contents($staffFile, json_encode($defaultStaff, JSON_PRETTY_PRINT));
}

$staffMembers = json_decode(file_get_contents($staffFile), true);
if(!is_array($staffMembers) || empty($staffMembers)){
$staffMembers = $defaultStaff;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_staff'])){
$index = (int)($_POST['staff_index'] ?? -1);
if($index >= 0 && isset($staffMembers[$index])){
    unset($staffMembers[$index]);
    $staffMembers = array_values($staffMembers);
    file_put_contents($staffFile, json_encode($staffMembers, JSON_PRETTY_PRINT));
    header('Location: adminstaff.php'); 
    exit();
}
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status']) && isset($_POST['staff_status'])){
$index = (int)($_POST['staff_index'] ?? -1);
if($index >= 0 && isset($staffMembers[$index])){
    $newStatus = trim((string)($_POST['staff_status'] ?? 'Available'));
    $staffMembers[$index]['status'] = ($newStatus === 'Unavailable') ? 'Unavailable' : 'Available';
    file_put_contents($staffFile, json_encode($staffMembers, JSON_PRETTY_PRINT));
    header('Location: adminstaff.php');
    exit();
}
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_staff'])){
$index = (int)($_POST['staff_index'] ?? -1);
if($index >= 0 && isset($staffMembers[$index])){
    // Support both array-style inputs (from inline card forms) and scalar inputs (from modal)
    $staffMembers[$index]['name'] = trim(is_array($_POST['staff_name'] ?? null) ? ($_POST['staff_name'][$index] ?? '') : ($_POST['staff_name'] ?? ''));
    $staffMembers[$index]['age'] = trim(is_array($_POST['staff_age'] ?? null) ? ($_POST['staff_age'][$index] ?? '') : ($_POST['staff_age'] ?? ''));
    $staffMembers[$index]['contact'] = trim(is_array($_POST['staff_contact'] ?? null) ? ($_POST['staff_contact'][$index] ?? '') : ($_POST['staff_contact'] ?? ''));
    $staffMembers[$index]['address'] = trim(is_array($_POST['staff_address'] ?? null) ? ($_POST['staff_address'][$index] ?? '') : ($_POST['staff_address'] ?? ''));
    $staffMembers[$index]['joined'] = trim(is_array($_POST['staff_joined'] ?? null) ? ($_POST['staff_joined'][$index] ?? '') : ($_POST['staff_joined'] ?? ''));
    $staffMembers[$index]['skills'] = trim(is_array($_POST['staff_skills'] ?? null) ? ($_POST['staff_skills'][$index] ?? '') : ($_POST['staff_skills'] ?? ''));
    file_put_contents($staffFile, json_encode($staffMembers, JSON_PRETTY_PRINT));
    header('Location: adminstaff.php');
    exit();
}
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])){
$name = trim($_POST['name'] ?? '');
if($name !== ''){
$imageIndex = count($staffMembers) % 12 + 1;
$staffMembers[] = [
'name' => $name,
'age' => trim($_POST['age'] ?? ''),
'contact' => trim($_POST['contact'] ?? ''),
'address' => trim($_POST['address'] ?? ''),
'joined' => trim($_POST['joined'] ?? ''),
'skills' => trim($_POST['skills'] ?? ''),
'image' => 'assets/staff/staff' . $imageIndex . '.jpg',
'status' => 'Available'
];
file_put_contents($staffFile, json_encode($staffMembers, JSON_PRETTY_PRINT));
header('Location: adminstaff.php');
exit();
}
}
?>
