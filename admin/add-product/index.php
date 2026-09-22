<?php
include __DIR__ . '/../../config.php';
require_once __DIR__ . '/../auth_check.php';

$msg = "";

if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $mrp = floatval($_POST['mrp']);
    $discount_percent = intval($_POST['discount_percent']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = intval($_POST['stock']);
    
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

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmp, "../../uploads/".$image);

    mysqli_query($conn, "INSERT INTO products(name, description, price, mrp, discount_percent, tags, category, image, stock)
                         VALUES('$name', '$description', '$price', '$mrp', '$discount_percent', '$tags_esc', '$category', '$image', '$stock')");
    $msg = "Product Added Successfully! ✅";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Admin</title>
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

        .msg {
            background: #e8f5e9;
            color: green;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
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
    <h2>🌿 Add New Plant Product</h2>

    <?php if($msg != ""){ ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <div class="price-row">
            <div>
                <label for="mrp">MRP (₹)</label>
                <input type="number" step="0.01" id="mrp" name="mrp" placeholder="e.g. 399" required>
            </div>
            <div>
                <label for="price">Offered Price (₹)</label>
                <input type="number" step="0.01" id="price" name="price" placeholder="e.g. 299" required>
            </div>
            <div>
                <label for="discount_percent">Discount (%)</label>
                <input type="number" id="discount_percent" name="discount_percent" placeholder="e.g. 25" required>
            </div>
        </div>

        <label>Stock Quantity</label>
        <input type="number" name="stock" required>

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Indoor Plants">🌿 Indoor Plants</option>
            <option value="Outdoor Plants">🌳 Outdoor Plants</option>
            <option value="Flowering Plants">🌸 Flowering Plants</option>
            <option value="Farmer Plants">🚜 Farmer Plants</option>
        </select>

        <!-- Product Tags -->
        <label>Product Tags (Select presets and/or add custom)</label>
        <div class="tags-container">
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="fragrance"> fragrance</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="ambient"> ambient</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="creeper"> creeper</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="no sun"> no sun</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="no water"> no water</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="colorful"> colorful</label>
            <label class="tag-checkbox-label"><input type="checkbox" name="tags_preset[]" value="air purifying"> air purifying</label>
        </div>
        <input type="text" name="tags_custom" placeholder="Or enter custom tags (comma separated, e.g. rare, hanging)">

        <label>Upload Image</label>
        <input type="file" name="image" required>

        <button type="submit" name="save">Add Product</button>
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