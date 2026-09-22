<?php
include '../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, trim($_GET['slug'])) : '';

if ($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM posts WHERE id=$id");
} elseif (!empty($slug)) {
    $res = mysqli_query($conn, "SELECT * FROM posts WHERE slug='$slug'");
} else {
    header("Location: ../blog/");
    exit();
}

$data = mysqli_fetch_assoc($res);

if (!$data) {
    // If not found in posts, try matching against old blogs table for backward compatibility
    if ($id > 0) {
        $res_old = mysqli_query($conn, "SELECT * FROM blogs WHERE id=$id");
        $data_old = mysqli_fetch_assoc($res_old);
        if ($data_old) {
            // Render basic template for old blogs
            include '../includes/header.php';
            ?>
            <div style="max-width: 800px; margin: 40px auto; padding: 0 20px 60px;">
                <h1 class="serif-font" style="font-size: 36px; margin-bottom: 20px; color: var(--primary-dark);"><?php echo htmlspecialchars($data_old['title']); ?></h1>
                <?php if(!empty($data_old['image'])){ ?>
                    <img src="../uploads/<?php echo $data_old['image']; ?>" style="width: 100%; height: auto; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                <?php } ?>
                <div style="font-size: 16px; line-height: 1.8; color: var(--text-dark);">
                    <?php echo nl2br(htmlspecialchars($data_old['content'])); ?>
                </div>
            </div>
            <?php
            include '../includes/footer.php';
            exit();
        }
    }
    
    header("Location: ../blog/");
    exit();
}

// Redirect to SEO friendly subdirectory URL
header("Location: ../blog/" . $data['slug'] . "/");
exit();
?>