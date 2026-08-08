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

<style>

body{
margin:0;
font-family:'Montserrat', sans-serif;
background:#f4fdf9;
overflow-x:hidden;
}

/* ===== PAGE LOAD SMOOTH ===== */
body{
opacity:0;
transition:0.4s;
}

/* TOPBAR */
.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:65px;
background:white;
display:flex;
align-items:center;
padding:0 15px;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
z-index:1000;
}

.topbar h3{
font-family:'Playfair Display', serif;
color:#064e3b;
margin:0;
font-size:18px;
}

/* ===== SIDEBAR ===== */
.sidebar{
width:200px;
height:100vh;
background:#064e3b;
position:fixed;
top:65px;
left:0;
padding:20px 10px;
transition:width .3s ease;
}

.sidebar.collapsed{
width:60px;
}

.sidebar a{
display:block;
color:white;
padding:12px 12px;
margin:5px 0;
border-radius:8px;
text-decoration:none;
font-size:13px;
transition:0.3s;
}

.sidebar a:hover{
background:#10b981;
transform:translateX(5px);
}

.sidebar.collapsed a{
font-size:0;
padding:12px 0;
text-align:center;
}

/* TOGGLE */
.toggle-btn{
background:#10b981;
border:none;
color:white;
width:28px;
height:28px;
border-radius:6px;
cursor:pointer;
margin-bottom:10px;
display:flex;
align-items:center;
justify-content:center;
}

/* MAIN */
.main{
margin-left:220px;
padding:90px 20px;
transition:all 0.4s ease;
animation:fadeMain 0.7s ease;
}

/* EXPAND */
.main.expanded{
margin-left:70px;
}

@keyframes fadeMain{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

.title{
font-family:'Playfair Display', serif;
font-size:28px;
color:#064e3b;
margin-bottom:20px;
}

.category-title{
font-family:'Playfair Display', serif;
font-size:20px;
color:#064e3b;
margin:25px 0 10px;

/* animation */
opacity:0;
transform:translateX(-15px);
animation:fadeLeft 0.6s ease forwards;
}

.category-title:nth-of-type(1){animation-delay:0.1s;}
.category-title:nth-of-type(2){animation-delay:0.2s;}
.category-title:nth-of-type(3){animation-delay:0.3s;}
.category-title:nth-of-type(4){animation-delay:0.4s;}

@keyframes fadeLeft{
to{
opacity:1;
transform:translateX(0);
}
}

/* GRID */
.grid{
display:grid;
grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));
gap:20px;
}

/* ===== CARD ===== */
.card{
position:relative;
background:linear-gradient(145deg,#ffffff,#f0fdf8);
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
border-top:4px solid #10b981;
padding:12px;
display:flex;
flex-direction:column;
justify-content:space-between;
text-align:center;
aspect-ratio:1/1;
transition:0.35s;
overflow:hidden;

/* animation */
opacity:0;
transform:translateY(20px) scale(0.98);
animation:cardFade 0.6s ease forwards;
}

/* stagger */
.card:nth-child(1){animation-delay:0.05s;}
.card:nth-child(2){animation-delay:0.1s;}
.card:nth-child(3){animation-delay:0.15s;}
.card:nth-child(4){animation-delay:0.2s;}
.card:nth-child(5){animation-delay:0.25s;}
.card:nth-child(6){animation-delay:0.3s;}
.card:nth-child(7){animation-delay:0.35s;}
.card:nth-child(8){animation-delay:0.4s;}
.card:nth-child(9){animation-delay:0.45s;}
.card:nth-child(10){animation-delay:0.5s;}

@keyframes cardFade{
to{
opacity:1;
transform:translateY(0) scale(1);
}
}

.card::before{
content:'';
position:absolute;
top:0;
left:-75%;
width:50%;
height:100%;
background:linear-gradient(120deg,transparent,rgba(255,255,255,0.6),transparent);
transform:skewX(-25deg);
transition:0.6s;
}

.card:hover::before{
left:130%;
}

.card:hover{
transform:translateY(-10px) scale(1.05);
box-shadow:0 25px 45px rgba(0,0,0,0.18);
}

.card img{
width:100%;
height:120px;
object-fit:cover;
border-radius:12px;
}

</style>
</head>

<body>

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