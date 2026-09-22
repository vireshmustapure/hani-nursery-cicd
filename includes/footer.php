<!-- ================= PREMIUM FOOTER ================= -->
<style>
.footer-premium {
    background: #0d3c13; /* Premium Forest Dark Green */
    color: rgba(255, 255, 255, 0.8);
    padding: 70px 20px 30px;
    font-family: 'Outfit', sans-serif;
    border-top: 4px solid var(--secondary-color);
}

.footer-container {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 2fr;
    gap: 40px;
}

.footer-col h3 {
    color: white;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 10px;
}

.footer-col h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 35px;
    height: 3px;
    background-color: var(--secondary-color);
    border-radius: var(--radius-full);
}

.footer-col p {
    font-size: 14px;
    line-height: 1.8;
    margin-bottom: 12px;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition-smooth);
    display: inline-block;
}

.footer-links a:hover {
    color: white;
    transform: translateX(5px);
}

/* Newsletter styling */
.newsletter-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 15px;
}

.newsletter-input-box {
    display: flex;
    border-radius: var(--radius-sm);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.newsletter-input-box input {
    flex: 1;
    padding: 12px 15px;
    border: none;
    outline: none;
    font-size: 14px;
    color: var(--text-dark);
}

.newsletter-input-box button {
    background-color: var(--secondary-color);
    color: var(--primary-dark);
    border: none;
    padding: 0 20px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition-smooth);
    font-size: 14px;
}

.newsletter-input-box button:hover {
    background-color: #b58915;
}

.newsletter-status {
    font-size: 13px;
    font-weight: 600;
    display: none;
    margin-top: 5px;
}

.footer-bottom {
    max-width: 1200px;
    margin: 40px auto 0;
    padding-top: 25px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.5);
}

.footer-bottom-links a {
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    margin-left: 20px;
    transition: var(--transition-smooth);
}

.footer-bottom-links a:hover {
    color: white;
}

/* Mobile responsive styles */
@media(max-width: 900px) {
    .footer-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
}

@media(max-width: 600px) {
    .footer-premium {
        padding: 50px 15px 30px;
    }
    
    .footer-container {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
    }
    
    .footer-col h3::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .newsletter-input-box {
        flex-direction: column;
        border-radius: var(--radius-sm);
        gap: 10px;
        box-shadow: none;
        background: transparent;
    }
    
    .newsletter-input-box input {
        border-radius: var(--radius-sm);
        width: 100%;
        padding: 12px;
    }
    
    .newsletter-input-box button {
        border-radius: var(--radius-sm);
        width: 100%;
        padding: 12px;
    }
    
    .footer-bottom {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .footer-bottom-links {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-left: 0;
    }
    
    .footer-bottom-links a {
        margin: 0 10px;
    }
}
</style>

<footer class="footer-premium">
    <div class="footer-container">
        
        <!-- Column 1: Brand Info -->
        <div class="footer-col">
            <h3>Hani Nursery & Gardening 🌱</h3>
            <p>Bringing premium nature and fresh green life straight to your home. We specialize in dynamic home gardening solutions, farm-fresh fruit plants, and wholesale orders in Kalaburagi.</p>
            <p style="margin-top: 15px;"><strong>📍 Address:</strong> P&T Cross, Old Jewargi Rd, Kalaburagi, Karnataka - 585102</p>
            <p><strong>📞 Phone:</strong> +91 7676626666</p>
        </div>

        <!-- Column 2: Quick Links -->
        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="<?= $base ?>index.php">🏠 Home</a></li>
                <li><a href="<?= $base ?>pages/shop/">🪴 Shop Plants</a></li>
                <li><a href="<?= $base ?>blog/">📝 Gardening Blog</a></li>
                <li><a href="<?= $base ?>contact/">📍 Visit Nursery</a></li>
            </ul>
        </div>

        <!-- Column 3: Categories -->
        <div class="footer-col">
            <h3>Categories</h3>
            <ul class="footer-links">
                <li><a href="<?= $base ?>pages/shop/?category=Indoor Plants">🌿 Indoor Plants</a></li>
                <li><a href="<?= $base ?>pages/shop/?category=Outdoor Plants">🌳 Outdoor Plants</a></li>
                <li><a href="<?= $base ?>pages/shop/?category=Flowering Plants">🌸 Flower Plants</a></li>
                <li><a href="<?= $base ?>pages/shop/?category=Farmer Plants">🚜 Farmer Plants</a></li>
            </ul>
        </div>

        <!-- Column 4: Newsletter -->
        <div class="footer-col">
            <h3>Fresh Tips & Offers</h3>
            <p>Subscribe to our newsletter to receive the latest gardening tips, exclusive offers, and fresh plant releases.</p>
            
            <form class="newsletter-form" onsubmit="subscribeNewsletter(event, this)">
                <div class="newsletter-input-box">
                    <input type="email" placeholder="Your Email Address" required>
                    <button type="submit">Subscribe</button>
                </div>
                <div class="newsletter-status" id="newsletter-status"></div>
            </form>
        </div>

    </div>

    <!-- Bottom Line -->
    <div class="footer-bottom">
        <div>
            © <?= date('Y') ?> Hani Nursery & Gardening. All rights reserved.
        </div>
        <div class="footer-bottom-links">
            <a href="#">Shipping Policy</a>
            <a href="#">Return Policy</a>
            <a href="#">Privacy Policy</a>
        </div>
    </div>
</footer>

<!-- Dynamic Newsletter Submission JavaScript -->
<script>
function subscribeNewsletter(e, form) {
    e.preventDefault();
    
    let emailInput = form.querySelector('input[type="email"]');
    let statusDiv = form.querySelector('#newsletter-status');
    let emailVal = emailInput.value.trim();
    
    if(!emailVal) return;
    
    statusDiv.style.display = "block";
    statusDiv.style.color = "white";
    statusDiv.innerText = "Subscribing...";
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "<?= $base ?>pages/newsletter_subscribe/index.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        try {
            let res = JSON.parse(this.responseText);
            if(res.success) {
                statusDiv.style.color = "#a5d6a7"; /* light green text */
                statusDiv.innerText = res.message;
                emailInput.value = "";
            } else {
                statusDiv.style.color = "#ffcccb"; /* light red text */
                statusDiv.innerText = res.message;
            }
        } catch(err) {
            statusDiv.style.color = "#ffcccb";
            statusDiv.innerText = "An error occurred. Please try again.";
        }
    };
    
    xhr.send("email=" + encodeURIComponent(emailVal));
}
</script>

<!-- Newsletter Popup Modal -->
<div id="newsletter-modal" style="
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.5s ease;
">
    <div style="
        background: linear-gradient(135deg, #0d3c13, #1b5e20);
        color: white;
        padding: 40px;
        border-radius: 20px;
        width: 90%;
        max-width: 450px;
        position: relative;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        text-align: center;
        border: 2px solid rgba(255,255,255,0.1);
        font-family: 'Outfit', sans-serif;
    ">
        <span onclick="closeNewsletterModal()" style="
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: rgba(255,255,255,0.7);
            transition: color 0.3s;
        " onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">&times;</span>
        
        <div style="font-size: 50px; margin-bottom: 15px;">🪴</div>
        <h2 style="margin-top: 0; margin-bottom: 10px; font-size: 24px; font-weight: 800;">Get Exclusive Offers!</h2>
        <p style="font-size: 14px; line-height: 1.6; margin-bottom: 25px; color: rgba(255,255,255,0.9);">
            Subscribe to our newsletter to receive the latest gardening tips, exclusive offers, and fresh plant releases. <b>Subscribe now and receive special discounts & offers!</b> 🚚🌿
        </p>
        
        <form onsubmit="subscribeNewsletterPopup(event, this)" style="display: flex; flex-direction: column; gap: 12px;">
            <input type="email" placeholder="Your Email Address" required style="
                width: 100%;
                padding: 12px 15px;
                border: none;
                border-radius: 8px;
                outline: none;
                font-size: 14px;
                color: #333;
            ">
            <button type="submit" style="
                width: 100%;
                background-color: #fb641b;
                color: white;
                border: none;
                padding: 12px;
                border-radius: 8px;
                font-weight: bold;
                font-size: 15px;
                cursor: pointer;
                transition: background-color 0.3s;
            " onmouseover="this.style.backgroundColor='#e05310'" onmouseout="this.style.backgroundColor='#fb641b'">
                Subscribe & Get Offers
            </button>
            <div id="newsletter-popup-status" style="font-size: 13px; font-weight: 600; margin-top: 5px;"></div>
        </form>
    </div>
</div>

<script>
function showNewsletterPopup() {
    if (localStorage.getItem("newsletter_popup_closed") || localStorage.getItem("newsletter_subscribed")) {
        return;
    }
    const modal = document.getElementById("newsletter-modal");
    modal.style.display = "flex";
    setTimeout(() => {
        modal.style.opacity = "1";
    }, 50);
}

function closeNewsletterModal() {
    const modal = document.getElementById("newsletter-modal");
    modal.style.opacity = "0";
    setTimeout(() => {
        modal.style.display = "none";
    }, 500);
    localStorage.setItem("newsletter_popup_closed", "true");
}

function subscribeNewsletterPopup(e, form) {
    e.preventDefault();
    let emailInput = form.querySelector('input[type="email"]');
    let statusDiv = form.querySelector('#newsletter-popup-status');
    let emailVal = emailInput.value.trim();
    if(!emailVal) return;
    
    statusDiv.style.display = "block";
    statusDiv.style.color = "white";
    statusDiv.innerText = "Subscribing...";
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "<?= $base ?>pages/newsletter_subscribe/index.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        try {
            let res = JSON.parse(this.responseText);
            if(res.success) {
                statusDiv.style.color = "#a5d6a7";
                statusDiv.innerText = res.message + " Check your email for special offers!";
                localStorage.setItem("newsletter_subscribed", "true");
                emailInput.value = "";
                setTimeout(closeNewsletterModal, 3000);
            } else {
                statusDiv.style.color = "#ffcccb";
                statusDiv.innerText = res.message;
            }
        } catch(err) {
            statusDiv.style.color = "#ffcccb";
            statusDiv.innerText = "An error occurred. Please try again.";
        }
    };
    xhr.send("email=" + encodeURIComponent(emailVal));
}

// Trigger popup after 30 seconds
setTimeout(showNewsletterPopup, 30000);
</script>