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

   $check_product_stock = mysqli_query($conn, "SELECT stock FROM products WHERE name = '$product_name'");
   $fetch_stock = mysqli_fetch_assoc($check_product_stock);
   $product_stock = $fetch_stock['stock'];

   if ($product_stock > 0 && $product_stock >= $product_quantity) {
      $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($check_cart_numbers) > 0) {
         $message[] = 'already added to cart!';
      } else {
         mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');

         $new_stock = $product_stock - $product_quantity;
         mysqli_query($conn, "UPDATE products SET stock = '$new_stock' WHERE name = '$product_name'");

         $message[] = 'product added to cart!';
      }
   } else {
      $message[] = 'product not available in stock!';
   }
}

function getUpdatedStockQuantity($conn, $product_name) {
   $check_product_stock = mysqli_query($conn, "SELECT stock FROM products WHERE name = '$product_name'");
   $fetch_stock = mysqli_fetch_assoc($check_product_stock);
   return $fetch_stock['stock'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .sold-out {
         color: red;
         text-transform: uppercase;
         font-size: 24px;
         cursor: pointer;
      }
   </style>
</head>
<body>
   <?php include 'header.php'; ?>

   <section class="home">
      <div class="content">
         <h3>"Unleash your imagination, one page at a time."</h3>
         <p>Explore, escape, and enlighten with books.</p>
         <a href="about.php" class="white-btn">Discover More</a>
      </div>
   </section>

   <section class="products">
      <h1 class="title">Latest Products</h1>
      <div class="box-container">
         <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM products LIMIT 6") or die('query failed');
         if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_products = mysqli_fetch_assoc($select_products)) {
               $product_id = $fetch_products['id'];
               $product_stock = $fetch_products['stock'];
               ?>
               <form action="" method="post" class="box">
                  <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <div class="price">P<?php echo $fetch_products['price']; ?></div>
                  <div class="author">Author: <?php echo $fetch_products['author']; ?></div>
                  <div class="isbn">ISBN: <?php echo $fetch_products['isbn']; ?></div>
                  <div class="stock">
                     <?php if ($product_stock > 0) : ?>
                        <span class="available">Available: <?php echo $product_stock; ?></span>
                     <?php else : ?>
                        <span class="sold-out" onclick="showSoldOutAlert()">SOLD OUT</span>
                     <?php endif; ?>
                  </div>
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <?php if ($product_stock > 0) : ?>
                     <input type="number" name="product_quantity" value="0" min="1" max="<?php echo $product_stock; ?>" style="display: block; margin: 0.5rem 0;">
                     <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
                  <?php else : ?>
                     <input type="button" value="Add to Cart" class="btn" disabled>
                  <?php endif; ?>
               </form>
               <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
         ?>
      </div>
     
   </section>

   <section class="about">
      <div class="flex">
         <div class="image">
            <img src="images/about-img.jpg" alt="">
         </div>
         <div class="content">
            <h3>About Us</h3>
            <p>Choose our Bookstore for an exceptional reading experience. With an extensive collection spanning various genres, our curated selection ensures there's something for every reader. Our knowledgeable staff is passionate about literature and ready to provide personalized recommendations. We prioritize customer satisfaction, offering exceptional service whether you're browsing online or visiting our physical store. Enjoy competitive pricing, regular discounts, and promotions that provide great value for your money. Engage with our vibrant community through author events, book clubs, and literary discussions. We guarantee the highest quality books, both new releases and well-preserved used copies. Fuel your love for reading with our Bookstore, your go-to destination for all your literary needs.</p>
            <a href="about.php" class="btn">Read More</a>
         </div>
      </div>
   </section>

   <section class="home-contact">
      <div class="content">
         <h3>Have Any Questions?</h3>
         <p>Engage with our vibrant community through author events, book clubs, and literary discussions. We guarantee the highest quality books, both new releases and well-preserved used copies. Fuel your love for reading with our Bookstore, your go-to destination for all your literary needs.</p>
         <a href="contact.php" class="white-btn">Contact Us</a>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <script>
      function showSoldOutAlert() {
         alert("Sorry, this item is not available at this time.");
      }
   </script>
</body>
</html>