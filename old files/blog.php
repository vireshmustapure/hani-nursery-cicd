<?php
include 'config/db.php';
$result = mysqli_query($conn, "SELECT * FROM blogs ORDER BY id DESC");
?>

<div class="blog-grid">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="blog-card">
    <img src="uploads/<?php echo $row['image']; ?>">

    <h3><?php echo $row['title']; ?></h3>

    <a href="blog-single.php?id=<?php echo $row['id']; ?>">
        Read More
    </a>
</div>

<?php } ?>

</div>