<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$servicesFile = __DIR__ . '/json/services_data.json';
$servicesDir = dirname($servicesFile);
$uploadDir = dirname(__DIR__) . '/assets/services';

if(!is_dir($servicesDir)){
    mkdir($servicesDir, 0777, true);
}

$defaultServices = json_decode(@file_get_contents($servicesFile), true);
if(!is_array($defaultServices) || empty($defaultServices)){
    $defaultServices = [];
}

if(!file_exists($servicesFile) && !empty($defaultServices)){
    file_put_contents($servicesFile, json_encode($defaultServices, JSON_PRETTY_PRINT));
}

$serviceCategories = json_decode(@file_get_contents($servicesFile), true);
if(!is_array($serviceCategories) || empty($serviceCategories)){
    $serviceCategories = $defaultServices;
    if(!empty($serviceCategories)){
        file_put_contents($servicesFile, json_encode($serviceCategories, JSON_PRETTY_PRINT));
    }
}

function findCategoryIndex(array $categories, string $categoryId): int {
    foreach($categories as $index => $category){
        if(($category['id'] ?? '') === $categoryId){
            return $index;
        }
    }
    return -1;
}

function saveServicesToFile(string $file, array $categories): void {
    file_put_contents($file, json_encode(array_values($categories), JSON_PRETTY_PRINT));
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_service'])){
    $categoryId = trim($_POST['category_id'] ?? '');
    $serviceIndex = (int)($_POST['service_index'] ?? -1);
    $categoryIndex = findCategoryIndex($serviceCategories, $categoryId);

    if($categoryIndex >= 0 && $serviceIndex >= 0 && isset($serviceCategories[$categoryIndex]['services'][$serviceIndex])){
        unset($serviceCategories[$categoryIndex]['services'][$serviceIndex]);
        $serviceCategories[$categoryIndex]['services'] = array_values($serviceCategories[$categoryIndex]['services']);
        saveServicesToFile($servicesFile, $serviceCategories);
        header('Location: adminservices.php?removed=1');
        exit();
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_services'])){
    $payload = json_decode($_POST['services_json'] ?? '', true);
    if(is_array($payload)){
        $uploadedImages = $_POST['new_image_service'] ?? [];
        $uploadedFiles = $_FILES['new_images'] ?? [];

        if(is_array($uploadedImages) && !empty($uploadedImages) && is_array($uploadedFiles) && isset($uploadedFiles['name'])){
            $replacementImages = [];
            $fileNames = $uploadedFiles['name'] ?? [];
            $tmpNames = $uploadedFiles['tmp_name'] ?? [];
            $errors = $uploadedFiles['error'] ?? [];

            foreach ($uploadedImages as $index => $serviceKey) {
                if (!isset($fileNames[$index]) || $fileNames[$index] === '') {
                    continue;
                }

                if (($errors[$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }

                $extension = strtolower(pathinfo($fileNames[$index], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($extension, $allowed, true)) {
                    continue;
                }

                $filename = 'service_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $destination = $uploadDir . '/' . $filename;

                if (move_uploaded_file($tmpNames[$index], $destination)) {
                    $replacementImages[(string)$serviceKey] = 'assets/services/' . $filename;
                }
            }

            foreach ($payload as $categoryIndex => $category) {
                if (!isset($category['services']) || !is_array($category['services'])) {
                    continue;
                }

                foreach ($category['services'] as $serviceIndex => $service) {
                    $serviceKey = (string)($category['id'] ?? '') . '::' . (string)$serviceIndex;
                    if (isset($replacementImages[$serviceKey])) {
                        $payload[$categoryIndex]['services'][$serviceIndex]['image'] = $replacementImages[$serviceKey];
                    }
                }
            }
        }

        saveServicesToFile($servicesFile, $payload);
        header('Location: adminservices.php?saved=1');
        exit();
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])){
    $categoryId = trim($_POST['category'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if($categoryId !== '' && $name !== '' && $price !== ''){
        $categoryIndex = findCategoryIndex($serviceCategories, $categoryId);
        if($categoryIndex >= 0){
            $image = 'https://via.placeholder.com/300';

            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
                if(!is_dir($uploadDir)){
                    mkdir($uploadDir, 0777, true);
                }

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if(in_array($extension, $allowed, true)){
                    $filename = 'service_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                    $destination = $uploadDir . '/' . $filename;

                    if(move_uploaded_file($_FILES['image']['tmp_name'], $destination)){
                        $image = 'assets/services/' . $filename;
                    }
                }
            }

            $serviceCategories[$categoryIndex]['services'][] = [
                'name' => $name,
                'price' => $price,
                'image' => $image
            ];

            saveServicesToFile($servicesFile, $serviceCategories);
            header('Location: adminservices.php?added=1');
            exit();
        }
    }
}
?>
