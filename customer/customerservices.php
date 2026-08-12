<?php
session_start();

if(!isset($_SESSION['customer'])){
header("Location: customerlogin.php");
exit();
}

$servicesFile = dirname(__DIR__) . '/cut-and-coat/functions/json/services_data.json';
$serviceCategories = [];

if(file_exists($servicesFile)){
    $decodedServices = json_decode(file_get_contents($servicesFile), true);
    if(is_array($decodedServices)){
        $serviceCategories = $decodedServices;
    }
}

if(empty($serviceCategories)){
    $serviceCategories = [
        [
            'id' => 'default_services',
            'title' => 'Available Services',
            'services' => []
        ]
    ];
}

function customerServiceImageUrl($image) {
    if (empty($image)) {
        return 'https://via.placeholder.com/300';
    }

    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, 'data:') === 0) {
        return $image;
    }

    if (strpos($image, 'assets/services/') === 0) {
        return '../cut-and-coat/' . $image;
    }

    return $image;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Services</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/customer.css">
<link rel="stylesheet" href="assets/css/customerservices.css">
</head>

<body class="services-page">

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Our Services</div>

<?php foreach($serviceCategories as $category): ?>
    <div class="category-title"><?php echo htmlspecialchars($category['title'] ?? 'Services'); ?></div>
    <div class="grid">
        <?php if(!empty($category['services']) && is_array($category['services'])): ?>
            <?php foreach($category['services'] as $service): ?>
                <?php $serviceImage = customerServiceImageUrl($service['image'] ?? ''); ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($serviceImage); ?>" alt="<?php echo htmlspecialchars($service['name'] ?? 'Service'); ?>">
                    <h4><?php echo htmlspecialchars($service['name'] ?? 'Service'); ?></h4>
                    <div class="price">₱<?php echo htmlspecialchars($service['price'] ?? '0'); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="width: 100%; grid-column: 1 / -1;">
                <h4>No services available yet.</h4>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<script>
/* smooth page load */
window.addEventListener("load", ()=>{
document.body.style.opacity = "1";
});
</script>

</body>
</html>