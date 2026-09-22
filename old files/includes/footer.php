<style>
.footer-btn{
    display:block;
    background:#2e7d32;
    color:#fff;
    padding:10px 15px;
    margin:6px 0;
    border-radius:25px;
    text-decoration:none;
    text-align:center;
    font-size:14px;
    transition:0.3s;
}
.footer-btn:hover{
    background:#43a047;
    transform:translateY(-2px);
}
```html
/* MOBILE FOOTER */
@media(max-width:768px){

    footer{
        padding:30px 15px !important;
    }

    footer h2{
        font-size:18px !important;
        white-space:normal !important;
        text-align:center;
    }

    footer h3{
        text-align:center;
    }

    footer p{
        text-align:center;
        font-size:14px;
    }

    .footer-btn{
        font-size:13px;
        padding:10px;
    }

    footer div[style*="grid"]{
        gap:20px !important;
    }
}

</style>
```


<footer style="background:#1f3d2b; color:#fff; padding:40px 20px; font-family:Poppins;">

<div style="max-width:100%; margin:auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:30px;">

    <!-- Company -->
    <div>
        <h2 style="white-space:nowrap; font-size:20px;">
            Hani Nursery & Gardening 🌱
        </h2>
        <p>Growing Green Life in Kalaburagi</p>
        <p>📍 Kalaburagi, Karnataka</p>
        <p>📞 +91 7676626666</p>
        <p>📧 info@shrenidhi.online</p>
    </div>

    <!-- Quick Links -->
    <div>
        <h3>Quick Links</h3>
        <a href="/" class="footer-btn">Home</a>
        <a href="/pages/shop.php" class="footer-btn">Shop</a>
        <a href="/pages/about.php" class="footer-btn">About</a>
        <a href="/pages/contact.php" class="footer-btn">Contact</a>
    </div>

    <!-- Categories -->
    <div>
        <h3>Categories</h3>
        <a href="/pages/shop.php?category=Indoor Plants" class="footer-btn">Indoor Plants</a>
        <a href="/pages/shop.php?category=Outdoor Plants" class="footer-btn">Outdoor Plants</a>
        <a href="/pages/shop.php?category=Flowering Plants" class="footer-btn">Flower Plants</a>
        <a href="/pages/shop.php" class="footer-btn">Pots & Tools</a>
    </div>

    <!-- Support -->
    <div>
        <h3>Support</h3>
        <a href="#" class="footer-btn">Shipping Policy</a>
        <a href="#" class="footer-btn">Return Policy</a>
        <a href="#" class="footer-btn">Privacy Policy</a>
    </div>

</div>

<div style="text-align:center; margin-top:30px; border-top:1px solid #555; padding-top:15px;">
    © 2026 Hani Nursery and Gardening. All Rights Reserved.
</div>

</footer>

</body>
</html>