<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cut and Coat Nail Salon</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */
body{
margin:0;
font-family:'Montserrat', sans-serif;
background:#f4fdf9;
}

/* ================= NAVBAR ================= */
.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:0 20px;
height:60px;
background:white;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
position:fixed;
top:0;
left:0;
width:100%;
z-index:1000;
box-sizing:border-box;
}

.nav-links{
display:flex;
gap:15px;
}

.navbar a{
text-decoration:none;
color:#064e3b;
font-weight:600;
font-size:14px;
padding:6px 12px;
border-radius:8px;
transition:0.3s;
}

.navbar a:hover{
background:#ecfdf5;
color:#10b981;
transform:translateY(-2px);
}

/* ================= HERO ================= */
.hero{
min-height:130vh;
background:url('salon.jpg') center/cover no-repeat;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center;
text-align:center;
color:white;
position:relative;
overflow:hidden;
padding-top:120px;
padding-bottom:80px;
}

.overlay{
position:absolute;
width:100%;
height:100%;
background:rgba(6,78,59,0.70);
}

@keyframes fadeUp{
from{opacity:0; transform:translateY(40px);}
to{opacity:1; transform:translateY(0);}
}

.content{
position:relative;
z-index:2;
max-width:800px;
padding:20px;
animation:fadeUp 1s ease;
transform:translateY(-30px);
}

/* LOGO */
.center-logo{
width:120px;
height:120px;
border-radius:50%;
object-fit:cover;
margin-bottom:20px;
box-shadow:0 15px 35px rgba(0,0,0,0.45);
border:3px solid rgba(255,255,255,0.5);
}

/* TEXT */
h1{
font-family:'Playfair Display', serif;
font-size:72px;
margin:0;
}

h2{
font-family:'Playfair Display', serif;
font-style:italic;
color:#d1fae5;
margin-top:10px;
font-size:22px;
}

p{
margin-top:18px;
line-height:1.9;
font-size:17px;
color:#ecfdf5;
}

/* BUTTON */
button{
margin-top:30px;
padding:14px 35px;
background:linear-gradient(135deg,#10b981,#059669);
color:white;
border:none;
border-radius:30px;
cursor:pointer;
transition:0.3s;
font-weight:600;
box-shadow:0 8px 20px rgba(16,185,129,0.4);
position:relative;
overflow:hidden;
}

button::before{
content:"";
position:absolute;
top:0;
left:-75%;
width:50%;
height:100%;
background:rgba(255,255,255,0.3);
transform:skewX(-25deg);
transition:0.5s;
}

button:hover::before{
left:125%;
}

button:hover{
transform:translateY(-3px) scale(1.05);
}

/* ================= CARDS ================= */
.cards-section{
margin-top:50px;
width:100%;
max-width:1400px; /* ✅ wider screen usage */
}

.cards-title{
color:white;
font-family:'Playfair Display', serif;
font-size:28px;
margin-bottom:25px;
}

/* 3 WIDE CARDS */
.cards{
display:grid;
grid-template-columns:repeat(3, 1fr);
gap:25px;
}

/* RECTANGULAR LANDSCAPE CARDS */
.card{
background:rgba(255,255,255,0.95);
padding:20px 25px;
border-radius:15px;
box-shadow:0 8px 20px rgba(0,0,0,0.15);
transition:0.3s;

/* ✅ MAKE IT RECTANGULAR */
display:flex;
height:180px;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center; /* left aligned = premium */

position:relative;
overflow:hidden;
}

/* SHINE */
.card::before{
content:"";
position:absolute;
top:0;
left:-75%;
width:50%;
height:100%;
background:rgba(255,255,255,0.4);
transform:skewX(-25deg);
transition:0.6s;
}

.card:hover::before{
left:125%;
}

.card:hover{
transform:translateY(-6px);
}

/* TEXT */
.card h3{
font-family:'Playfair Display', serif;
color:#064e3b;
margin:0;
margin-bottom:8px;
margin-top:5px;
font-size:18px;
text-align:center; 
}

.card p{
font-size:14px;
color:#555;
line-height:1.5;
margin:0;
text-align:center;
}

/* ================= GALLERY ================= */

.gallery-section{
margin-top:90px;
width:100%;
max-width:1400px;
margin-left:auto;
margin-right:auto;
padding:0 20px;
box-sizing:border-box;
}

.gallery-title{
font-family:'Playfair Display', serif;
font-size:34px;
font-weight:700;
color:white;
text-align:center;
margin-bottom:35px;
letter-spacing:1px;
}

.gallery{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:25px;
align-items:stretch;
}

.gallery-card{
position:relative;
overflow:hidden;
border-radius:18px;
height:300px;
box-shadow:0 15px 35px rgba(0,0,0,.25);
transition:.35s ease;
cursor:pointer;
display:flex;
align-items:flex-end;
}

.gallery-card:hover{
transform:translateY(-8px);
box-shadow:0 20px 45px rgba(0,0,0,.35);
}

.gallery-card img{
width:100%;
height:100%;
object-fit:cover;
transition:.5s ease;
}

.gallery-card:hover img{
transform:scale(1.08);
}

.gallery-overlay{
position:absolute;
left:0;
bottom:0;
width:100%;
padding:22px;
background:linear-gradient(
to top,
rgba(0,0,0,.82),
rgba(0,0,0,.35),
transparent
);
box-sizing:border-box;
}

.gallery-overlay h3{
margin:0;
font-family:'Playfair Display', serif;
font-size:22px;
font-weight:600;
color:white;
text-align:left;
}

.gallery-overlay p{
margin-top:8px;
font-size:14px;
line-height:1.6;
color:#f3f4f6;
text-align:left;
}

/* ================= STATS ================= */

.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:25px;
margin:90px auto 0;
max-width:1400px;
padding:0 20px;
box-sizing:border-box;
}

.stat{
background:rgba(255,255,255,.95);
padding:35px 20px;
border-radius:18px;
text-align:center;
box-shadow:0 8px 20px rgba(0,0,0,.15);
transition:.35s ease;
}

.stat:hover{
transform:translateY(-8px);
}

.stat h2{
margin:0;
font-size:46px;
font-family:'Playfair Display', serif;
color:#059669;
}

.stat p{
margin-top:12px;
font-size:15px;
color:#555;
line-height:1.5;
}

/* ================= TESTIMONIALS ================= */

.testimonial-section{
margin-top:90px;
width:100%;
max-width:1400px;
margin-left:auto;
margin-right:auto;
padding:0 20px;
box-sizing:border-box;
}

.testimonial-grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:25px;
}

.testimonial{
background:white;
padding:35px 30px;
border-radius:20px;
box-shadow:0 8px 20px rgba(0,0,0,.12);
transition:.35s ease;
text-align:center;
display:flex;
flex-direction:column;
justify-content:space-between;
min-height:220px;
}

.testimonial:hover{
transform:translateY(-8px);
}

.testimonial p{
font-size:15px;
line-height:1.8;
color:#555;
margin:20px 0;
}

.testimonial h4{
margin:0;
font-size:17px;
font-family:'Playfair Display', serif;
color:#064e3b;
}

/* ================= RESPONSIVE ================= */

@media (max-width:1100px){

.gallery{
grid-template-columns:repeat(2,1fr);
}

.stats{
grid-template-columns:repeat(2,1fr);
}

.testimonial-grid{
grid-template-columns:repeat(2,1fr);
}

}

@media (max-width:768px){

.gallery{
grid-template-columns:1fr;
}

.stats{
grid-template-columns:1fr;
}

.testimonial-grid{
grid-template-columns:1fr;
}

.gallery-title{
font-size:28px;
}

.gallery-card{
height:260px;
}

}

/* ================= RESPONSIVE ================= */

/* Tablet = 2 per row */
@media (max-width:900px){
.cards{
grid-template-columns:repeat(2,1fr);
}
}

/* Mobile = 1 per row */
@media (max-width:500px){
.cards{
grid-template-columns:1fr;
}
}

@media (max-width:768px){

h1{font-size:48px;}
h2{font-size:18px;}
p{font-size:15px;}

.center-logo{
width:70px;
height:70px;
}

.content{
transform:translateY(-15px);
}
}

@media (max-width:480px){

h1{font-size:38px;}

.hero{
padding:80px 15px 50px;
}

.content{
transform:translateY(-10px);
}
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
<div></div>
<div class="nav-links">
<a href="index.php">Home</a>
<a href="about.php">About</a>
</div>
</div>

<!-- HERO -->
<div class="hero">

<div class="overlay"></div>

<div class="content">

<img src="logo.jpg" class="center-logo">

<h1>Cut & Coat Nail Salon</h1>
<h2>Style, Care, Perfection</h2>

<p>
Cut and Coat is your all-in-one retreat for hair, nails, and skin.
Experience world-class beauty in a space of total serenity, elegance, and luxury.
</p>

<button onclick="window.location.href='customerlogin.php'">
Book Your Appointment
</button>

<!-- ================= FEATURED SERVICES ================= -->

<div class="gallery-section">

<div class="gallery-title">Featured Services</div>

<div class="gallery">

<div class="gallery-card">
<img src="luxurynail.jpg">
<div class="gallery-overlay">
<h3>Luxury Nail Art</h3>
<p>Elegant custom nail designs.</p>
</div>
</div>

<div class="gallery-card">
<img src="pedicure.jpg">
<div class="gallery-overlay">
<h3>Classic Pedicure</h3>
<p>Relax, refresh, and rejuvenate.</p>
</div>
</div>

<div class="gallery-card">
<img src="manicure.jpg">
<div class="gallery-overlay">
<h3>Gel Manicure</h3>
<p>Long-lasting glossy finish.</p>
</div>
</div>

<div class="gallery-card">
<img src="spa.jpg">
<div class="gallery-overlay">
<h3>Spa Treatment</h3>
<p>Ultimate hand & foot relaxation.</p>
</div>
</div>

</div>

<!-- CARDS -->
<div class="cards-section">

<div class="cards-title">Why Choose Us</div>

<div class="cards">

<div class="card"><h3>Premium Nail Services</h3><p>High-quality nail care using top-grade products. Experience the latest trends and timeless classics tailored to your style.</p></div>
<div class="card"><h3>Relaxing Atmosphere</h3><p>Enjoy a calm and luxurious salon experience. Unwind with soothing music and complimentary refreshments during your visit.</p></div>
<div class="card"><h3>Expert Staff</h3><p>Skilled professionals delivering precise results. Our certified technicians stay updated on the best industry techniques for you.</p></div>
<div class="card"><h3>Clean & Safe</h3><p>Strict hygiene standards for your safety. We use medical-grade sterilization to ensure a worry-free environment.</p></div>
<div class="card"><h3>Affordable Luxury</h3><p>Premium services at reasonable prices.  Indulge in a high-end experience without the high-end price tag.</p></div>
<div class="card"><h3>Easy Booking</h3><p>Quick and convenient appointment system. Schedule your next pampering session in just a few clicks from any device.</p></div>

</div>

<!-- ================= STATS ================= -->

<div class="stats">

<div class="stat">
<h2>500+</h2>
<p>Happy Clients</p>
</div>

<div class="stat">
<h2>8+</h2>
<p>Professional Staff</p>
</div>

<div class="stat">
<h2>30+</h2>
<p>Beauty Services</p>
</div>

<div class="stat">
<h2>5★</h2>
<p>Customer Rating</p>
</div>
</div>
<!-- ================= TESTIMONIALS ================= -->

<div class="testimonial-section">

<div class="gallery-title">
What Our Clients Say
</div>

<div class="testimonial-grid">

<div class="testimonial">

★★★★★

<p>
"The best nail salon I've ever visited. Staff are so friendly and professional!"
</p>

<h4>- Maria</h4>

</div>

<div class="testimonial">

★★★★★

<p>
"My gel manicure lasted almost a month. Highly recommended!"
</p>

<h4>- Andrea</h4>

</div>

<div class="testimonial">

★★★★★

<p>
"The place is beautiful, relaxing, and very clean."
</p>

<h4>- Angela</h4>

</div>

</div>

</div>
</div>

</div>

</div>

</div>

</body>
</html>