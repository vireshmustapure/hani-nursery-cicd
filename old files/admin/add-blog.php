<?php
session_start();
include '../config/db.php';

$msg = "";

// FORM SUBMIT
if(isset($_POST['submit'])){

    $title = $_POST['title'];
    $content = $_POST['content'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if(!is_dir("../uploads")){
        mkdir("../uploads", 0777, true);
    }

    move_uploaded_file($tmp, "../uploads/".$image);

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $image);

    if($stmt->execute()){
        $msg = "Blog added successfully ✅";
    }else{
        $msg = "Error adding blog ❌";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Blog</title>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/x7qze6yw0kml6mmoenjfklpyfkseg2ptth1hecpz5enmhz9c/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<style>
body{
    font-family: Arial;
    background:#eef7ee;
    padding:30px;
}

.container{
    max-width:800px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    color:#2e7d32;
}

input, textarea{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    margin-top:15px;
    width:100%;
    padding:12px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    background:#1b5e20;
}

.msg{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}
</style>

</head>

<body>

<div class="container">

<h2>🌿 Add New Blog</h2>

<?php if($msg!=""){ ?>
    <div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<label>Title</label>
<input type="text" name="title" required>

<label>Content</label>
<textarea name="content" id="editor"></textarea>

<label>Image</label>
<input type="file" name="image" required>

<button type="submit" name="submit">Add Blog</button>

</form>

</div>

<!-- TinyMCE INIT -->
<script>
tinymce.init({
    selector: '#editor',
    height: 400,
    menubar: true,
    plugins: [
        'advlist autolink lists link image charmap preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
});
</script>

</body>
</html>