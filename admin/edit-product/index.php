<?php
include __DIR__ . '/../../config.php';
require_once __DIR__ . '/../auth_check.php';

$id = intval($_GET['id']);
$get = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$row = mysqli_fetch_assoc($get);

if(!$row) {
    die("Product not found");
}

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $mrp = floatval($_POST['mrp']);
    $discount_percent = intval($_POST['discount_percent']);
    $stock = intval($_POST['stock']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Process tags
    $tags_arr = isset($_POST['tags_preset']) ? $_POST['tags_preset'] : [];
    if (!empty($_POST['tags_custom'])) {
        $custom_tags = explode(',', $_POST['tags_custom']);
        foreach ($custom_tags as $ct) {
            $tags_arr[] = trim($ct);
        }
    }
    $tags = implode(', ', array_map('trim', array_filter($tags_arr)));
    $tags_esc = mysqli_real_escape_string($conn, $tags);

    $image = $row['image'];
    if($_FILES['image']['name'] != ""){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../../uploads/".$image);
    }

    mysqli_query($conn, "UPDATE products SET 
                         name='$name', 
                         description='$description', 
                         price='$price', 
                         mrp='$mrp', 
                         discount_percent='$discount_percent', 
                         tags='$tags_esc', 
                         stock='$stock', 
                         category='$category', 
                         image='$image' 
                         WHERE id='$id'");

    header("Location: ../products/");
    exit();
}

// Parse existing tags for checkboxes
$existing_tags = array_map('trim', explode(',', $row['tags'] ?? ''));
$presets = ['fragrance', 'ambient', 'creeper', 'no sun', 'no water', 'colorful', 'air purifying'];
$customs = array_diff($existing_tags, $presets);
$customs_str = implode(', ', $customs);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #eef7ee;
            padding: 40px 20px;
        }

        .box {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        textarea {
            height: 100px;
            resize: none;
        }

        /* Tag Grid Styles */
        .tags-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }

        .tag-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: normal;
            cursor: pointer;
            background: #f4faf4;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .tag-checkbox-label input {
            width: auto;
        }

        /* Pricing row */
        .price-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        img {
            max-width: 150px;
            display: block;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1b5e20;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 18px;
            text-decoration: none;
            color: #2e7d32;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
<div class="box">
    <h2>🌿 Edit Plant Product</h2>

    <form method="post" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>

        <label>Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>

        <div class="price-row">
            <div>
                <label for="mrp">MRP (₹)</label>
                <input type="number" step="0.01" id="mrp" name="mrp" value="<?php echo htmlspecialchars($row['mrp'] ?? $row['price']); ?>" required>
            </div>
            <div>
                <label for="price">Offered Price (₹)</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
            </div>
            <div>
                <label for="discount_percent">Discount (%)</label>
                <input type="number" id="discount_percent" name="discount_percent" value="<?php echo htmlspecialchars($row['discount_percent'] ?? 0); ?>" required>
            </div>
        </div>

        <label>Stock Quantity</label>
        <input type="number" name="stock" value="<?php echo htmlspecialchars($row['stock']); ?>" required>

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Indoor Plants" <?php echo $row['category'] === 'Indoor Plants' ? 'selected' : ''; ?>>🌿 Indoor Plants</option>
            <option value="Outdoor Plants" <?php echo $row['category'] === 'Outdoor Plants' ? 'selected' : ''; ?>>🌳 Outdoor Plants</option>
            <option value="Flowering Plants" <?php echo $row['category'] === 'Flowering Plants' ? 'selected' : ''; ?>>🌸 Flowering Plants</option>
            <option value="Farmer Plants" <?php echo $row['category'] === 'Farmer Plants' ? 'selected' : ''; ?>>🚜 Farmer Plants</option>
        </select>

        <!-- Product Tags -->
        <label>Product Tags (Select presets and/or add custom)</label>
        <div class="tags-container">
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="fragrance" <?php echo in_array('fragrance', $existing_tags) ? 'checked' : ''; ?>> fragrance</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="ambient" <?php echo in_array('ambient', $existing_tags) ? 'checked' : ''; ?>> ambient</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="creeper" <?php echo in_array('creeper', $existing_tags) ? 'checked' : ''; ?>> creeper</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="no sun" <?php echo in_array('no sun', $existing_tags) ? 'checked' : ''; ?>> no sun</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="no water" <?php echo in_array('no water', $existing_tags) ? 'checked' : ''; ?>> no water</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="colorful" <?php echo in_array('colorful', $existing_tags) ? 'checked' : ''; ?>> colorful</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="air purifying" <?php echo in_array('air purifying', $existing_tags) ? 'checked' : ''; ?>> air purifying</label>
        </div>
        <input type="text" name="tags_custom" value="<?php echo htmlspecialchars($customs_str); ?>" placeholder="Or enter custom tags (comma separated, e.g. rare, hanging)">

        <label>Current Image</label>
        <img src="../../uploads/<?php echo $row['image']; ?>" alt="Current Product Image">

        <label style="margin-top: 15px;">Change Image</label>
        <input type="file" name="image">

        <button type="submit" name="update">Update Product</button>
    </form>

    <a href="../products/" class="back">← Back to Products</a>
</div>
</div>

<script>
    const mrpInput = document.getElementById('mrp');
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discount_percent');

    function calculateFromDiscount() {
        const mrp = parseFloat(mrpInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        if (mrp > 0) {
            priceInput.value = (mrp * (1 - discount / 100)).toFixed(2);
        }
    }

    function calculateFromPrice() {
        const mrp = parseFloat(mrpInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        if (mrp > 0 && price > 0) {
            discountInput.value = Math.round(((mrp - price) / mrp) * 100);
        }
    }

    discountInput.addEventListener('input', calculateFromDiscount);
    priceInput.addEventListener('input', calculateFromPrice);
    mrpInput.addEventListener('input', () => {
        if (discountInput.value) {
            calculateFromDiscount();
        } else if (priceInput.value) {
            calculateFromPrice();
        }
    });
</script>

</body>
</html>