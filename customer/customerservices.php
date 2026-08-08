<?php
session_start();

if(!isset($_SESSION['customer'])){
header("Location: customerlogin.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Services</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

}
}
}
}
<link rel="stylesheet" href="assets/css/customer.css">
</head>

<body class="services-page">

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>



<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Our Services</div>
<!-- MANICURE -->
<div class="category-title">Manicure & Pedicure</div>
<div class="grid">

<div class="card"><img src="classicmanicure.jpg"><h4>Classic Manicure</h4><div class="price">₱150</div></div>
<div class="card"><img src="classicpedicure.jpg"><h4>Classic Pedicure</h4><div class="price">₱180</div></div>
<div class="card"><img src="gelmanicure.jpg"><h4>Gel Manicure</h4><div class="price">₱499</div></div>
<div class="card"><img src="gelpedicure.jpg"><h4>Gel Pedicure</h4><div class="price">₱599</div></div>
<div class="card"><img src="gelremoval.jpg"><h4>Gel Removal</h4><div class="price">₱100</div></div>
<div class="card"><img src="spamanicure.jpg"><h4>Spa Manicure</h4><div class="price">₱250</div></div>
<div class="card"><img src="spapedicure.jpg"><h4>Spa Pedicure</h4><div class="price">₱280</div></div>
<div class="card"><img src="footspa.jpg"><h4>Foot Spa</h4><div class="price">₱350</div></div>
<div class="card"><img src="deluxemanicure.jpg"><h4>Deluxe Manicure</h4><div class="price">₱350</div></div>
<div class="card"><img src="deluxepedicure.jpg"><h4>Deluxe Pedicure</h4><div class="price">₱380</div></div>

</div>

<!-- POLISH -->
<div class="category-title">Change Polish</div>
<div class="grid">

<div class="card"><img src="localpolish.jpg"><h4>Local Polish</h4><div class="price">₱49</div></div>
<div class="card"><img src="brandedpolish.jpg"><h4>Branded Polish</h4><div class="price">₱79</div></div>
<div class="card"><img src="frenchpolish.jpg"><h4>French Polish</h4><div class="price">₱99</div></div>
<div class="card"><img src="gelpolish.jpg"><h4>Gel Polish Change</h4><div class="price">₱199</div></div>
<div class="card"><img src="mattepolish.jpg"><h4>Matte Polish</h4><div class="price">₱89</div></div>

</div>

<!-- EXTENSIONS -->
<div class="category-title">Extensions</div>
<div class="grid">

<div class="card"><img src="softgel.jpg"><h4>Soft Gel Extension</h4><div class="price">₱799</div></div>
<div class="card"><img src="softremoval.jpg"><h4>Soft Gel Removal</h4><div class="price">₱300</div></div>
<div class="card"><img src="repair.jpg"><h4>Repair per nail</h4><div class="price">₱79</div></div>
<div class="card"><img src="acrylic.jpg"><h4>Acrylic Extension</h4><div class="price">₱899</div></div>
<div class="card"><img src="refill.jpg"><h4>Gel Refill</h4><div class="price">₱499</div></div>

</div>

<!-- NAIL ART -->
<div class="category-title">Nail Art</div>
<div class="grid">

<div class="card"><img src="plain.jpg"><h4>Plain</h4><div class="price">₱100</div></div>
<div class="card"><img src="chrome.jpg"><h4>Chrome</h4><div class="price">₱150</div></div>
<div class="card"><img src="ombre.jpg"><h4>Ombre</h4><div class="price">₱150</div></div>
<div class="card"><img src="frenchtip.jpg"><h4>French Tip</h4><div class="price">₱100</div></div>
<div class="card"><img src="cateye.jpg"><h4>Cat Eye</h4><div class="price">₱150</div></div>
<div class="card"><img src="glitter.jpg"><h4>Glitter Design</h4><div class="price">₱120</div></div>
<div class="card"><img src="3d.jpg"><h4>3D Nail Art</h4><div class="price">₱200</div></div>
<div class="card"><img src="marble.jpg"><h4>Marble Design</h4><div class="price">₱180</div></div>
<div class="card"><img src="foil.jpg"><h4>Foil Design</h4><div class="price">₱130</div></div>
<div class="card"><img src="minimalist.jpg"><h4>Minimalist Art</h4><div class="price">₱110</div></div>

</div>

<!-- MASSAGE -->
<div class="category-title">Massage</div>
<div class="grid">

<div class="card"><img src="classicmassage.jpg"><h4>Classic Massage</h4><div class="price">₱399</div></div>
<div class="card"><img src="signature.jpg"><h4>Signature Massage</h4><div class="price">₱499</div></div>
<div class="card"><img src="footmassage.jpg"><h4>Foot Massage</h4><div class="price">₱299</div></div>
<div class="card"><img src="back.jpg"><h4>Back Massage</h4><div class="price">₱349</div></div>
<div class="card"><img src="head.jpg"><h4>Head Massage</h4><div class="price">₱199</div></div>

</div>

<!-- ADD-ONS -->
<div class="category-title">Add-ons</div>
<div class="grid">

<div class="card"><img src="warmstone.jpg"><h4>Warm Stone</h4><div class="price">₱300</div></div>
<div class="card"><img src="ventosa.jpg"><h4>Ventosa</h4><div class="price">₱200</div></div>
<div class="card"><img src="compress.jpg"><h4>Hot Compress</h4><div class="price">₱150</div></div>
<div class="card"><img src="aroma.jpg"><h4>Aromatherapy Oil</h4><div class="price">₱180</div></div>
<div class="card"><img src="scrub.jpg"><h4>Foot Scrub</h4><div class="price">₱120</div></div>

</div>


<script>



/* smooth page load */
window.addEventListener("load", ()=>{
document.body.style.opacity = "1";
});

</script>

</body>
</html>