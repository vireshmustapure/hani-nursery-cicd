<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

// Fetch published articles
$result = mysqli_query($conn, "SELECT * FROM posts WHERE status='published' ORDER BY is_pinned DESC, published_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Gardening Tips & Articles - Hani Nursery</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.blog-list-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px 60px;
}
.blog-header {
    text-align: center;
    margin-bottom: 50px;
}
.blog-header h2 {
    font-size: 36px;
    margin-bottom: 10px;
}
.blog-header p {
    color: var(--text-muted);
}
.blog-grid-premium {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}
.blog-card-premium {
    background: white;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-smooth);
    border: 1px solid rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
}
.blog-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}
.blog-card-img {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
}
.blog-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.blog-card-premium:hover .blog-card-img img {
    transform: scale(1.05);
}
.blog-card-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.blog-card-cat {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--primary-light);
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}
.blog-card-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 12px;
    line-height: 1.4;
    text-decoration: none;
}
.blog-card-title:hover {
    color: var(--primary-color);
}
.blog-card-desc {
    color: var(--text-muted);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
}
.blog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f0f0f0;
    padding-top: 15px;
    font-size: 12px;
    color: var(--text-muted);
}
.pin-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--secondary-color);
    color: var(--primary-dark);
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    z-index: 10;
    box-shadow: var(--shadow-sm);
}
</style>
</head>
<body>

<div class="blog-list-container">
    <div class="blog-header">
        <h2 class="serif-font">🌿 Gardening Tips & Guides</h2>
        <p>Expert advice, guides, and tips to keep your plants healthy and thriving</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <div class="blog-grid-premium">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="blog-card-premium">
                    <div class="blog-card-img">
                        <?php if ($row['is_pinned']) { ?>
                            <span class="pin-badge">📌 Spotlight</span>
                        <?php } ?>
                        <img src="<?php echo htmlspecialchars($row['image'] ?: 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6'); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    </div>
                    
                    <div class="blog-card-content">
                        <div>
                            <div class="blog-card-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                            <a href="<?= $base ?>blog/<?php echo $row['slug']; ?>/" class="blog-card-title serif-font">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </a>
                            <p class="blog-card-desc">
                                <?php 
                                // Strip HTML tags and summarize intro
                                $clean_intro = strip_tags($row['intro_content']);
                                if (empty($clean_intro)) {
                                    $clean_intro = strip_tags($row['content_html']);
                                }
                                echo htmlspecialchars(substr($clean_intro, 0, 120)) . '...'; 
                                ?>
                            </p>
                        </div>
                        
                        <div class="blog-card-footer">
                            <span>📅 <?php echo date('d M Y', strtotime($row['published_at'])); ?></span>
                            <a href="<?= $base ?>blog/<?php echo $row['slug']; ?>/" style="color: var(--primary-light); font-weight: bold; text-decoration: none;">
                                Read Article ➔
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div style="text-align: center; padding: 60px 0;">
            <h3 style="color: var(--primary-dark);">No articles published yet</h3>
            <p style="color: var(--text-muted);">Check back soon for fresh gardening wisdom! 🌿</p>
        </div>
    <?php } ?>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>