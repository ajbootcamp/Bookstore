<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

if (isset($_POST['add_product'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $author = mysqli_real_escape_string($conn, $_POST['author']);
   $price = $_POST['price'];
   $image = $_FILES['books']['name'];
   $image_size = $_FILES['books']['size'];
   $image_tmp_name = $_FILES['books']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $isbn = $_POST['isbn'];
   $stock = $_POST['stock'];

   $select_product_name = mysqli_query($conn, "SELECT name FROM products WHERE name = '$name'") or die('query failed');

   if (mysqli_num_rows($select_product_name) > 0) {
      $message[] = 'Product name already added';
   } else {
      $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, author, price, image, isbn, stock) VALUES('$name', '$author', '$price', '$image', '$isbn', '$stock')") or die('query failed');

      if ($add_product_query) {
         if ($image_size > 2000000) {
            $message[] = 'Image size is too large';
         } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Product added successfully!';
         }
      } else {
         $message[] = 'Product could not be added!';
      }
   }
}

if (isset($_POST['update_product'])) {
   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_price = $_POST['update_price'];
   $update_stock = $_POST['update_stock'];

   mysqli_query($conn, "UPDATE products SET name = '$update_name', price = '$update_price', stock = '$update_stock' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['image']['name'];
   $update_image_tmp_name = $_FILES['image']['tmp_name'];
   $update_image_size = $_FILES['image']['size'];
   $update_folder = 'uploaded_img/' . $update_image;
   $update_old_image = $_POST['update_old_image'];

   if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
         $message[] = 'Image size is too large!';
      } else {
         mysqli_query($conn, "UPDATE products SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/' . $update_old_image);
      }
   }

   header('location:admin_products.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

   <?php include 'admin_header.php'; ?>

   <section class="add-products">
      <h1 class="title">Books</h1>
      <form action="" method="post" enctype="multipart/form-data">
         <h3>Add Books</h3>
         <input type="text" name="name" class="box" placeholder="Enter book title" required>
         <input type="text" name="author" class="box" placeholder="Enter author's name" required>
         <input type="number" min="0" name="price" class="box" placeholder="Enter product price" required>
         <input type="text" name="isbn" class="box" placeholder="Enter ISBN code" required>
         <input type="number" min="0" name="stock" class="box" placeholder="Enter stock quantity" required>
         <input type="file" name="books" accept=".pdf,.docx,.txt,.jpg,.jpeg,.png" class="box" required>
         <input type="submit" value="Add Product" name="add_product" class="btn">
      </form>
   </section>

   <section class="show-products">
      <div class="box-container">
         <?php
         $select_products = mysqli_query($conn, "SELECT * FROM products") or die('query failed');

         if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_products = mysqli_fetch_assoc($select_products)) {
               ?>
               <div class="box">
                  <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <div class="author">Author: <?php echo $fetch_products['author']; ?></div>
                  <div class="isbn">ISBN: <?php echo $fetch_products['isbn']; ?></div>
                  <div class="price">Price: P<?php echo $fetch_products['price']; ?></div>
                  <div class="stock">Stock: <?php echo $fetch_products['stock']; ?></div>
                  <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
               </div>
            <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
         ?>

      </div>
   </section>

   <section class="edit-product-form">
      <?php
      if (isset($_GET['update'])) {
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$update_id'") or die('query failed');
         if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
               ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                  <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter product name">
                  <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Enter product price">
                  <input type="number" name="update_stock" value="<?php echo $fetch_update['stock']; ?>" min="0" class="box" required placeholder="Enter stock quantity">
                  <input type="file" name="image" accept=".pdf,.docx,.txt,.jpg,.jpeg,.png" class="box">
                  <input type="submit" value="Update" name="update_product" class="btn">
                  <input type="reset" value="Cancel" id="close-update" class="option-btn">
               </form>
      <?php
            }
         }
      } else {
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
      ?>
   </section>

   <script src="js/admin_script.js"></script>
</body>

</html>
