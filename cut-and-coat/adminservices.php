<?php
include 'functions/adminservices_logic.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Services</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminservices.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<div class="top-actions">
    <button class="add-service-btn" id="addServiceBtn" onclick="openAddModal()">+ Add Services</button>

    <button class="edit-btn" id="editToggleBtn" onclick="enableEdit()">Edit Services</button>

    <button class="confirm-btn" id="confirmBtn" onclick="confirmChanges()">Confirm Changes</button>
</div>

<!-- Add Service Modal -->
<div class="modal" id="addServiceModal" onclick="closeAddModal(event)">

    <div class="modal-content" onclick="event.stopPropagation()">

        <button class="modal-close" onclick="closeAddModal()">&times;</button>

        <h3>Add New Service</h3>

        <form method="POST" enctype="multipart/form-data">

            <label>Category</label>
            <div id="serviceCategoryContainer">
                <select id="serviceCategory" name="category">
                <?php foreach($serviceCategories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['title']); ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            
            <label>Service Name</label>
            <input
                type="text"
                id="serviceName"
                name="name"
                placeholder="Enter service name"
                required>

            <label>Price</label>
            <input
                type="number"
                id="servicePrice"
                name="price"
                placeholder="Enter service price"
                required>

            <label>Service Image</label>
            <div class="image-upload-wrapper">
                <label for="serviceImage" class="image-upload-box" id="serviceImageBox">
                    <span class="upload-placeholder">Insert Image</span>
                    <img id="serviceImagePreview" alt="" hidden>
                </label>
                <input
                    type="file"
                    id="serviceImage"
                    name="image"
                    accept="image/*"
                    hidden>
            </div>

            <input type="hidden" name="add_service" value="1">

            <button type="submit" class="save-btn">
                Add Service
            </button>

        </form>

    </div>

</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">
<div class="title">Services Management</div>

<?php foreach($serviceCategories as $category): ?>
<div class="category-header">
<div class="category-title"><?php echo htmlspecialchars($category['title']); ?></div>
</div>
<div class="grid" id="<?php echo htmlspecialchars($category['id']); ?>" data-category-id="<?php echo htmlspecialchars($category['id']); ?>">

<?php foreach($category['services'] as $serviceIndex => $service): ?>
<div class="card" data-image="<?php echo htmlspecialchars($service['image'] ?? ''); ?>">
    <div class="image-container">
        <img class="service-image" src="<?php echo htmlspecialchars($service['image'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($service['name'] ?? 'Service'); ?>">
        <div class="edit-image-label">Edit Image</div>
        <input type="file" class="editable image-input" accept="image/*" data-service-key="<?php echo htmlspecialchars($category['id']); ?>::<?php echo $serviceIndex; ?>" hidden>
    </div>
    <h4><?php echo htmlspecialchars($service['name'] ?? 'Service'); ?></h4>
    <input class="editable name-input" value="<?php echo htmlspecialchars($service['name'] ?? ''); ?>" style="display:none;">
    <div class="price">₱<?php echo htmlspecialchars($service['price'] ?? '0'); ?></div>
    <input class="editable price-input" value="<?php echo htmlspecialchars($service['price'] ?? '0'); ?>" style="display:none;">
    <form method="POST" class="remove-service-form">
    <input type="hidden" name="remove_service" value="1">
    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['id']); ?>">
    <input type="hidden" name="service_index" value="<?php echo $serviceIndex; ?>">
    <button class="remove-btn" type="submit">x</button>
    </form>
</div>
<?php endforeach; ?>

</div>
<?php endforeach; ?>

</div>

<div class="modal" id="saveModal" onclick="closeSaveModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <h3>Updated Successfully!</h3>
        <p>Your service changes have been saved.</p>
        <button class="save-btn" type="button" onclick="closeSaveModal()">OK</button>
    </div>
</div>

<form id="saveServicesForm" method="POST" style="display:none;">
    <input type="hidden" name="save_services" value="1">
    <input type="hidden" name="services_json" id="servicesJsonInput" value="">
</form>

<script>

window.addEventListener("load", ()=>{
document.body.classList.add("loaded");

const serviceImageInput = document.getElementById("serviceImage");
const serviceImagePreview = document.getElementById("serviceImagePreview");
const serviceImageBox = document.getElementById("serviceImageBox");

if (serviceImageInput && serviceImagePreview && serviceImageBox) {
    serviceImageInput.addEventListener("change", function () {
        const file = this.files && this.files[0];

        if (!file) {
            serviceImagePreview.hidden = true;
            serviceImagePreview.removeAttribute("src");
            serviceImageBox.classList.remove("has-image");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            serviceImagePreview.src = event.target.result;
            serviceImagePreview.hidden = false;
            serviceImageBox.classList.add("has-image");
        };
        reader.readAsDataURL(file);
    });
}

const imageInputs = document.querySelectorAll('.image-input');
imageInputs.forEach(function(input) {
    input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        const card = this.closest('.card');
        const previewImg = card ? card.querySelector('.service-image') : null;
        if (!previewImg) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            previewImg.src = event.target.result;
            card.dataset.image = event.target.result;
        };
        reader.readAsDataURL(file);
    });
});

document.querySelectorAll('.service-image').forEach(function(img) {
    img.addEventListener('click', function () {
        if (!isEditMode) return;
        const input = this.parentElement.querySelector('.image-input');
        if (input) input.click();
    });
});

<?php if(isset($_GET['saved']) || isset($_GET['added']) || isset($_GET['removed'])): ?>
showSaveModal();
<?php endif; ?>
});

function openAddModal(){

    document.getElementById("addServiceModal")
            .classList.add("open");

}

function closeAddModal(event){

    const modal = document.getElementById("addServiceModal");
    if(event && event.target && event.target.id !== "addServiceModal"){
        return;
    }
    modal.classList.remove("open");

}

let isEditMode = false;

function enableEdit() {

    isEditMode = !isEditMode;

    const editBtn = document.getElementById("editToggleBtn");
    const confirmBtn = document.getElementById("confirmBtn");
    const addBtn = document.getElementById("addServiceBtn");

    if (isEditMode) {
        editBtn.innerText = "Exit";
        document.body.classList.add('editing');

        document.querySelectorAll(".price").forEach(price => {
            price.style.display = "none";
        });

        document.querySelectorAll(".editable").forEach(input => {
            input.style.display = "block";
        });

        document.querySelectorAll(".remove-btn").forEach(btn => {
            btn.style.display = "flex";
        });

        document.querySelectorAll(".service-image").forEach(img => {
            img.style.cursor = "pointer";
            img.style.filter = "brightness(0.96)";
        });

        if (addBtn) addBtn.style.display = "none";
        confirmBtn.style.display = "block";

    } else {
        editBtn.innerText = "Edit Services";
        document.body.classList.remove('editing');

        document.querySelectorAll(".price").forEach(price => {
            price.style.display = "block";
        });

        document.querySelectorAll(".editable").forEach(input => {
            input.style.display = "none";
        });

        document.querySelectorAll(".remove-btn").forEach(btn => {
            btn.style.display = "none";
        });

        document.querySelectorAll(".service-image").forEach(img => {
            img.style.cursor = "default";
            img.style.filter = "none";
        });

        if (addBtn) addBtn.style.display = "";
        confirmBtn.style.display = "none";
    }
}

function collectServicesData(){
    const categories = [];

    document.querySelectorAll(".grid[data-category-id]").forEach(grid => {
        const category = {
            id: grid.dataset.categoryId,
            title: grid.previousElementSibling.querySelector(".category-title").innerText.trim(),
            services: []
        };

        grid.querySelectorAll(".card").forEach(card => {
            const nameInput = card.querySelector(".name-input");
            const priceInput = card.querySelector(".price-input");
            const previewImage = card.querySelector(".service-image");

            category.services.push({
                name: nameInput ? nameInput.value.trim() : card.querySelector("h4").innerText.trim(),
                price: priceInput ? priceInput.value.trim() : "0",
                image: previewImage ? previewImage.getAttribute("src") : (card.dataset.image || '')
            });
        });

        categories.push(category);
    });

    return categories;
}

function confirmChanges(){
    document.querySelectorAll(".card").forEach(card => {
        let nameInput = card.querySelector(".name-input");
        let priceInput = card.querySelector(".price-input");

        if(nameInput)
            card.querySelector("h4").innerText = nameInput.value;

        if(priceInput)
            card.querySelector(".price").innerText = "₱" + priceInput.value;
    });

    const selectedFiles = Array.from(document.querySelectorAll('.image-input')).filter(input => input.files && input.files[0]);

    if (selectedFiles.length > 0) {
        const formData = new FormData();
        formData.append('save_services', '1');
        formData.append('services_json', JSON.stringify(collectServicesData()));

        selectedFiles.forEach(input => {
            formData.append('new_images[]', input.files[0]);
            formData.append('new_image_service[]', input.dataset.serviceKey);
        });

        fetch(window.location.href.split('?')[0], {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        }).then(function () {
            window.location.href = 'adminservices.php?saved=1';
        });
        return;
    }

    document.getElementById("servicesJsonInput").value = JSON.stringify(collectServicesData());
    document.getElementById("saveServicesForm").submit();
}

function showSaveModal(){
    const modal = document.getElementById('saveModal');
    if(modal) modal.classList.add('open');
}

function closeSaveModal(event){
    const modal = document.getElementById('saveModal');
    if(!modal) return;
    if(event && event.target && event.target.id !== 'saveModal'){
        modal.classList.remove('open');
        return;
    }
    modal.classList.remove('open');
}

function toggleSidebar(){
document.getElementById("sidebar").classList.toggle("collapsed");
document.getElementById("main").classList.toggle("expanded");
}

</script>

</body>
</html>
