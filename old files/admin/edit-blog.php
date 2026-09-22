<?php
session_start();
include '../config/db.php';

$id = $_GET['id'];

$result = mysqli_query($conn,"SELECT * FROM blogs WHERE id=$id");
$row = mysqli_fetch_assoc($result);

$msg = "";

if(isset($_POST['update'])){

    $title = $_POST['title'];
    $content = $_POST['content'];

    if($_FILES['image']['name']){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../uploads/".$image);

        mysqli_query($conn,"UPDATE blogs SET 
        title='$title',
        content='$content',
        image='$image'
        WHERE id=$id");
    }else{
        mysqli_query($conn,"UPDATE blogs SET 
        title='$title',
        content='$content'
        WHERE id=$id");
    }

    $msg = "Blog Updated Successfully ✅";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Blog</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#eef7ee;
}

/* CONTAINER */
.container{
    max-width:700px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    color:#2e7d32;
    margin-bottom:20px;
}

/* INPUT */
input, textarea{
    width:100%;
    padding:12px;
    margin-top:8px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

textarea{
    height:140px;
}

/* IMAGE PREVIEW */
.preview{
    margin-top:10px;
}

.preview img{
    width:120px;
    border-radius:10px;
    box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

/* BUTTON */
button{
    margin-top:20px;
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

/* MESSAGE */
.msg{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}

/* BACK BUTTON */
.back{
    display:inline-block;
    margin-bottom:15px;
    text-decoration:none;
    color:#2e7d32;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container">

<a href="manage-blog.php" class="back">← Back to Manage Blogs</a>

<h2>✏️ Edit Blog</h2>

<!-- SUCCESS MESSAGE -->
<?php if($msg!=""){ ?>
    <div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<label>Title</label>
<input type="text" name="title" value="<?php echo $row['title']; ?>" required>

<label>Content</label>
<textarea name="content" required><?php echo $row['content']; ?></textarea>

<label>Current Image</label>
<div class="preview">
    <img src="../uploads/<?php echo $row['image']; ?>">
</div>

<label>Change Image</label>
<input type="file" name="image">

<button name="update">Update Blog</button>

</form>

</div>

</body>
</html>