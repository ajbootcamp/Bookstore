<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query failed');

   if (mysqli_num_rows($check_cart_numbers) > 0) {
      $message[] = 'Product already added to cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('Query failed');
      $message[] = 'Product added to cart!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>
   <div class="heading">
      <h3>Search Page</h3>
      <p><a href="home.php">Home</a> / Search</p>
   </div>

   <section class="search-form">
      <form action="" method="post">
         <input type="text" name="search" placeholder="Search products..." class="box">
         <input type="submit" name="submit" value="Search" class="btn">
      </form>
   </section>

   <section class="products" style="padding-top: 0;">

      <div class="box-container">
         <?php
         if (isset($_POST['submit'])) {
            $search_item = $_POST['search'];
            $select_products = mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%$search_item%' OR author LIKE '%$search_item%' OR isbn LIKE '%$search_item%'") or die('Query failed');
            if (mysqli_num_rows($select_products) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_products)) {
         ?>
                  <form action="" method="post" class="box">
                     <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
                     <div class="name"><?php echo $fetch_product['name']; ?></div>
                     <?php if (isset($fetch_product['author']) && !empty($fetch_product['author'])) : ?>
                        <div class="author">Author: <?php echo $fetch_product['author']; ?></div>
                     <?php endif; ?>
                     <?php if (isset($fetch_product['isbn']) && !empty($fetch_product['isbn'])) : ?>
                        <div class="isbn">ISBN: <?php echo $fetch_product['isbn']; ?></div>
                     <?php endif; ?>
                     <div class="price">P<?php echo $fetch_product['price']; ?></div>
                     <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                     <input type="number" name="product_quantity" value="1" min="1" class="quantity">
                     <input type="submit" class="btn" value="Add to Cart" name="add_to_cart">
                  </form>
         <?php
               }
            } else {
               echo '<p class="empty">No results found!</p>';
            }
         } else {
            echo '<p class="empty">Search for something!</p>';
         }
         ?>
      </div>

   </section>


   <?php include 'footer.php'; ?>
   <script src="js/script.js"></script>

</body>

</html>