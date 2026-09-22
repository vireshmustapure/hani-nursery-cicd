<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1LSB0KPW3Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1LSB0KPW3Z');
</script>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

<title>Best Plant Nursery in Kalaburagi | Hani Nursery and Gardening</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

html{
    scroll-behavior:smooth;
}

*{
    -webkit-tap-highlight-color: transparent;
    box-sizing:border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Arial;
    background: #f5fff7;
    overflow-x:hidden;
}

/* SEARCH */
.search-container {
    display: flex;
    justify-content: center;
    margin: 20px;
}

.search-box {
    display: flex;
    width: 650px;
    border-radius: 50px;
    overflow: hidden;
    border: 2px solid #ddd;
    background: #fff;
}

.search-box select {
    padding: 10px;
    border: none;
    background: #f1f1f1;
    outline: none;
}

.search-box input {
    flex: 1;
    padding: 12px;
    border: none;
    outline: none;
}

.search-box button {
    background: #ff9800;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    font-size: 18px;
}

.search-box button:hover {
    background: #f57c00;
}

/* HERO */
.hero {
    position: relative;
    height: 450px;
}

.hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
}

.hero-content h2 {
    font-size: 42px;
    font-weight: bold;
    margin-bottom: 10px;
}

.hero-content h1{
    font-size:40px;
}

/* BLINK BUTTON */
@keyframes blink {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.05); }
    100% { opacity: 1; transform: scale(1); }
}

.blink-btn {
    background: green;
    color: white;
    padding: 13px 25px;
    text-decoration: none;
    border-radius: 25px;
    font-weight: bold;
    display: inline-block;
    animation: blink 1s infinite;
}

/* CATEGORY STYLE */
.categories {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding: 30px;
    flex-wrap: wrap;
    font-family: 'Poppins', sans-serif;
}

.cat {
    background: linear-gradient(145deg, #1b5e20, #2e7d32);
    color: white;
    padding: 12px 20px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    font-size: 15px;
    letter-spacing: 0.3px;
}

.cat:hover {
    transform: translateY(-3px);
    background: linear-gradient(145deg, #2e7d32, #1b5e20);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* OTHER SECTIONS */
.highlight-text {
    font-size: 28px;
    font-weight: bold;
    color: #2e7d32;
    text-align: center;
    padding: 40px 20px;
}

.blog {
    background: #e8f5e9;
    padding: 30px;
}

.blog-grid{
    display:flex;
    gap:20px;
    overflow-x:auto;
    padding-top:20px;
}

.blog-card{
    min-width:280px;
    background:#fff;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    padding:15px;
    transition:0.3s;
}

.blog-card:hover{
    transform:translateY(-5px);
}

.blog-card img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:10px;
}

.blog-card h3{
    font-size:18px;
    margin:10px 0;
    color:#2e7d32;
}

.blog-card a{
    text-decoration:none;
    color:#ff9800;
    font-weight:bold;
}

footer {
    background: #1b5e20;
    color: white;
    text-align: center;
    padding: 15px;
}

/* NURSERY SECTION */

.nursery-section {
    padding: 40px 20px;
    background: #f1f8f4;
}

.nursery-grid {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}

.nursery-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    width: 450px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: flex;
    gap: 15px;
    align-items: center;
}

.nursery-card img {
    width: 180px;
    height: 200px;
    object-fit: cover;
    border-radius: 15px;
}

.nursery-content h3 {
    margin: 0;
    color: #2e7d32;
}

.nursery-content p {
    font-size: 14px;
    color: #555;
}

.visit-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 15px;
    background: #2e7d32;
    color: white;
    border-radius: 20px;
    text-decoration: none;
}

.why-section {
    margin-top: 40px;
    text-align: center;
}

.why-box {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.why-item {
    background: white;
    padding: 15px;
    border-radius: 10px;
    width: 150px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

/* SLIDER */

.slider-box{
    width:260px;
    height:260px;
    overflow:hidden;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);
    background:#fff;
    position:relative;
    flex-shrink:0;
}

.slider{
    display:flex;
    width:500%;
    height:100%;
    animation:slide 25s infinite;
}

.slide{
    width:20%;
    height:100%;
    flex-shrink:0;
}

.slide img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

@keyframes slide{

    0%,18%{
        transform:translateX(0%);
    }

    20%,38%{
        transform:translateX(-20%);
    }

    40%,58%{
        transform:translateX(-40%);
    }

    60%,78%{
        transform:translateX(-60%);
    }

    80%,98%{
        transform:translateX(-80%);
    }

    100%{
        transform:translateX(0%);
    }

}

/* MOBILE */

@media(max-width:768px){

    body{
        zoom:1.05;
    }

    .hero{
        height:520px;
    }

    .hero-content{
        width:92%;
    }

    .hero-content h2{
        font-size:34px;
        line-height:1.4;
    }

    .hero-content h1{
        font-size:28px;
        line-height:1.5;
    }

    .search-container{
        margin:15px 10px;
    }

    .search-box{
        width:100%;
        border-radius:22px;
    }

    .search-box input{
        font-size:17px;
        padding:16px;
    }

    .search-box button{
        font-size:20px;
    }

    .cat{
        font-size:16px;
        padding:14px 20px;
    }

    .highlight-text{
        font-size:30px;
        line-height:1.5;
    }

    .blog-card{
        min-width:320px;
    }

    .blog-card h3{
        font-size:20px;
        line-height:1.5;
    }

    .nursery-card{
        width:100%;
        flex-direction:column;
        text-align:center;
    }

    .nursery-card img{
        width:100%;
        height:260px;
    }
    
    .slider-box{
    width:100%;
    height:260px;
}
    .nursery-content h3{
        font-size:28px;
    }

    .nursery-content p{
        font-size:17px;
        line-height:1.7;
    }

    .why-item{
        width:42%;
        font-size:17px;
    }

    .visit-buttons a,
    .blink-btn,
    .visit-btn{
        font-size:17px;
        padding:15px 24px;
    }

   
    footer p{
        font-size:17px;
    }

}

</style>

</head>

<body>
 <!-- WEEKEND OFFER BANNER START -->
<style>
.offer-strip{
    width:100%;
    background:linear-gradient(90deg,#1b5e20,#2e7d32,#1b5e20);
    color:#fff;
    overflow:hidden;
    white-space:nowrap;
    padding:10px 0;
    font-weight:700;
    font-size:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
    position:relative;
    z-index:999;
}
.offer-strip-1{
    width:100%;
    background:#ffffff;
    color:#fff;
    overflow:hidden;
    white-space:nowrap;
    padding:10px 0;
    font-weight:700;
    font-size:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
    position:relative;
    z-index:999;
}
.offer-move{
    display:inline-block;
    padding-left:100%;
    animation:offerMove 18s linear infinite;
}
.offer-move span{
    color:#ffeb3b;
}
.offer-move-1{
    display:inline-block;
    padding-left:100%;    color:green;

    animation:offerMove 18s linear infinite;
}
.offer-move-1 span{
    color:green;
}
@keyframes offerMove{
    0%{transform:translateX(0);}
    100%{transform:translateX(-100%);}
}

/* Mobile */
@media(max-width:768px){
    .offer-strip{
        font-size:14px;
        padding:8px 0;
    }
}
</style>

<?php
$day = date('N'); // 1 = Monday, 7 = Sunday

if($day == 6 || $day == 7){
    // WEEKEND OFFER
?>
<div class="offer-strip">
    <div class="offer-move">
        🎉 WEEKEND OFFER 🎉 Show your Google rating/review at shop counter and get <span>10% OFF</span> 🌿 Every Saturday & Sunday 🎉
    </div>
</div>

<?php
} else {
    // WEEKDAY OFFER
?>
<div class="offer-strip-1" style="bg-color:#000000">
    <div class="offer-move-1">
        🌿 WEEKDAY SPECIAL 🌿 Buy any plants & get exciting offers on fruit plants 🍋🌱 Best deals available Monday to Friday 💚
    </div>
    
</div>
<?php
}
?>
<!-- WEEKEND OFFER BANNER END -->   



<?php include __DIR__ . "/includes/header.php"; ?>

<div class="search-container">
<form method="GET" action="pages/shop.php" class="search-box">

<select name="category">
<option value="">All</option>
<option value="Indoor Plants">Indoor</option>
<option value="Outdoor Plants">Outdoor</option>
<option value="Farmer Plants">Farmer</option>
<option value="Flowering Plants">Flower</option>
</select>

<input type="text" name="search" placeholder="Search plants..." />

<button type="submit">🔍</button>

</form>
</div>

<section class="hero">
<img id="hero-img" src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6">

<div class="hero-content">
<h2>Bring Nature Home 🌱</h2>
<a href="pages/shop.php" class="blink-btn">Shop Now</a>


<h1>Best Plant Nursery in Kalaburagi</h1>
</div>
</section>

<script>
const images = [
"https://images.unsplash.com/photo-1501004318641-b39e6451bec6",
"https://images.unsplash.com/photo-1490750967868-88aa4486c946",
"https://images.unsplash.com/photo-1604762524889-3e2fcc145683",
"https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e",
"https://images.unsplash.com/photo-1466692476868-aef1dfb1e735"
];

let i = 0;
setInterval(() => {
i = (i + 1) % images.length;
document.getElementById("hero-img").src = images[i];
}, 3000);
</script>

<section class="categories">
<a href="pages/shop.php" class="cat">🌱 All Plants</a>
<a href="pages/shop.php?category=Indoor Plants" class="cat">🌿 Indoor Plants</a>
<a href="pages/shop.php?category=Outdoor Plants" class="cat">🌳 Outdoor Plants</a>
<a href="pages/shop.php?category=Farmer Plants" class="cat">🚜 Farmer Plants</a>
<a href="pages/shop.php?category=Flowering Plants" class="cat">🌸 Flowering Plants</a>
</section>

<section class="highlight-text">
Happiness is creating a garden in your own space 🌿
</section>

<section class="blog">

<h2 style="text-align:center;">🌿 Gardening Tips</h2>

<div class="blog-grid">

<?php
include 'config/db.php';

$result = mysqli_query($conn,"SELECT * FROM blogs ORDER BY id DESC LIMIT 5");

while($row = mysqli_fetch_assoc($result)){
?>

<div class="blog-card">
    <img src="uploads/<?php echo $row['image']; ?>">
    <h3><?php echo $row['title']; ?></h3>
    
    <a href="blog-single.php?id=<?php echo $row['id']; ?>">
        Read More
    </a>
</div>

<?php } ?>

</div>

</section>
<!-- NEW SECTION -->
<section class="nursery-section">

<div class="nursery-grid">

<div class="nursery-card">
<div class="nursery-content">
<h3>🌼 Visit Our Nursery</h3>
<p>Experience nature like never before! Walk through our beautiful nursery filled with fresh plants and greenery.</p>
<a href="https://share.google/7YePEfVIKtmIGDyxn" target="_blank" class="visit-btn">
    📍 Visit Now
</a>
</div>
<img src="uploads/nursury_front.jpg" alt="Nursery Image">
</div>

<div class="nursery-card">
    <div class="nursery-content">
        <h3>💼 Wholesale Prices</h3>
        <p>Get best deals on bulk plants. Visit our nursery for special wholesale discounts.</p>
        <p style="color:orange;font-weight:bold;">★ Available only at our nursery</p>
    </div>
    <!-- SLIDER REPLACES SINGLE IMAGE -->
    <div class="slider-box">
    <div class="slider">

        <div class="slide"><img src="uploads/IMG-20260409-WA0041.webp"></div>
        <div class="slide"><img src="uploads/IMG-20260409-WA0021.webp"></div>
        <div class="slide"><img src="uploads/IMG-20260409-WA0013.webp"></div>
        <div class="slide"><img src="uploads/IMG-20260409-WA0036.webp"></div>
        <div class="slide"><img src="uploads/IMG-20260426-WA00001.webp"></div>

    </div>
</div>
</div>

<div class="why-section">
<h2>🌿 Why Visit Our Nursery?</h2>

<div class="why-box">
<div class="why-item">🌱 Fresh Plants</div>
<div class="why-item">💰 Affordable</div>
<div class="why-item">👨‍🌾 Expert Help</div>
<div class="why-item">🌸 Huge Variety</div>
<div class="why-item">🚜 Farm Direct</div>
</div>
</div>

<div class="location-section">

<!-- ================= CONTACT + MAP SECTION ================= -->
<section class="visit-section">

<div class="visit-heading">
    <h2>🌿 VISIT HANI NURSERY TODAY 🌿</h2>
    <p>Fresh Plants • Wholesale Price • Trusted Nursery</p>
</div>

<div class="visit-wrapper">

    <!-- LEFT SIDE -->
    <div class="visit-info">

        <h3>📍 Contact Details</h3>

        <p><strong>🌿 Hani Nursery & Gardening</strong></p>
        <p>P&T Cross, Old Jewargi Rd</p>
        <p>Near Zudio, Opp Sai Bazar</p>
        <p>Kalaburagi - 585102</p>

        <p>📞 +91 7676626666</p>
        <p>🕒 Open Daily: 8AM - 8PM</p>
        <p>⭐⭐⭐⭐⭐ Trusted by People</p>

        <div class="visit-buttons">

            <a href="https://maps.app.goo.gl/zZooo2P3QFCKZiSi7" target="_blank" class="dir-btn">
                📍 Get Directions
            </a>

            <a href="https://wa.me/917676626666?text=Hello%20I%20want%20to%20visit%20your%20nursery" target="_blank" class="wa-btn">
                💬 WhatsApp Now
            </a>

        </div>

        <div class="mini-review">
    <div class="mini-stars">
        <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
        <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
        <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
        <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
        <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
    </div>

    <p>Love Our Nursery?</p>

    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank" class="mini-btn">
        ⭐ Rate Us
    </a>
</div>

<style>
.mini-review{
    grid-column:1 / -1;
    text-align:center;
    margin-top:15px;
}

.mini-stars a{
    font-size:26px;
    text-decoration:none;
    margin:0 2px;
}

.mini-review p{
    margin:8px 0;
    color:#1b5e20;
    font-size:15px;
    font-weight:600;
}

.mini-btn{
    display:inline-block;
    background:#FFD700;
    color:#222;
    padding:8px 18px;
    border-radius:20px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
}

.mini-btn:hover{
    background:#f5c400;
}
</style>
        

    </div>

    <!-- RIGHT SIDE -->
    <div class="visit-map">

        <iframe
            src="https://www.google.com/maps?q=Hani+Nursery+Kalaburagi&output=embed"
            loading="lazy"
            allowfullscreen>
        </iframe>

    </div>

</div>

</section>

<style>
.visit-section{
    padding:60px 20px;
    background:linear-gradient(135deg,#eef8f0,#dff3e3);
}

.visit-heading{
    text-align:center;
    margin-bottom:40px;
}

.visit-heading h2{
    font-size:34px;
    color:#1b5e20;
    margin-bottom:10px;
}

.visit-heading p{
    color:#555;
    font-size:18px;
}

.visit-wrapper{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
}

.visit-info,
.visit-map{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.12);
}

.visit-info h3{
    color:#1b5e20;
    margin-bottom:20px;
}

.visit-info p{
    margin:10px 0;
    font-size:16px;
    color:#333;
}

.visit-buttons{
    margin-top:25px;
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.visit-buttons a{
    padding:14px 22px;
    border-radius:30px;
    text-decoration:none;
    color:white;
    font-weight:bold;
    transition:0.3s;
}

.dir-btn{
    background:#2e7d32;
}

.wa-btn{
    background:#25D366;
}

.visit-buttons a:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,0.18);
}

.visit-map iframe{
    width:100%;
    height:100%;
    min-height:420px;
    border:0;
    border-radius:18px;
}

/* MOBILE */
@media(max-width:900px){
    .visit-wrapper{
        grid-template-columns:1fr;
    }

    .visit-heading h2{
        font-size:26px;
    }

    .visit-map iframe{
        min-height:320px;
    }
}
</style>

</div>

</div>

</section>

<footer>
<p>© 2026 Hani Nursery and Gardening</p>
</footer>

</body>
</html>