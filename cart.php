<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['update_cart'])) {
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE cart SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   $message[] = 'Cart quantity updated!';
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM cart WHERE id = '$delete_id'") or die('query failed');
   header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');
   header('location:cart.php');
}

// Update stock quantity in the database after successful checkout
if (isset($_SESSION['checkout_success'])) {
   foreach ($_SESSION['checkout_success'] as $product_id => $quantity) {
      mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id") or die('query failed');
   }
   unset($_SESSION['checkout_success']);
}

?>

<!DOCTYPE html>
<!-- Rest of the code -->

<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php include 'header.php'; ?>
   <div class="heading">
      <h3>Shopping Cart</h3>
      <p> <a href="home.php">Home</a> / Cart </p>
   </div>

   <section class="shopping-cart">
      <h1 class="title">Products Added</h1>
      <div class="box-container">
         <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
         if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
               // Get the remaining stock for the product
               $product_name = $fetch_cart['name'];
               $check_stock = mysqli_query($conn, "SELECT stock FROM products WHERE name = '$product_name'");
               $fetch_stock = mysqli_fetch_assoc($check_stock);
               $product_stock = $fetch_stock['stock'];
         ?>
               <div class="box">
                  <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Delete this from cart?');"></a>
                  <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_cart['name']; ?></div>
                  <?php if (!empty($fetch_cart['author'])) : ?>
                     <div class="author">Author: <?php echo $fetch_cart['author']; ?></div>s
                  <?php endif; ?>
                  <?php if (!empty($fetch_cart['stock'])) : ?>
                     <div class="stock">Stocks: <?php echo $product_stock; ?></div>
                  <?php endif; ?>
                  <?php if (!empty($fetch_cart['isbn'])) : ?>
                     <div class="isbn">ISBN: <?php echo $fetch_cart['isbn']; ?></div>
                  <?php endif; ?>
                  <div class="price">Price: P<?php echo $fetch_cart['price']; ?></div>
                  <form action="" method="post">
                     <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                     <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                     <input type="submit" name="update_cart" value="confirm" class="option-btn">
                  </form>
                  <div class="sub-total"> Subtotal: <span>P<?php echo $sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']); ?></span> </div>
               </div>
         <?php
               $grand_total += $sub_total;
            }
         } else {
            echo '<p class="empty">Your cart is empty</p>';
         }
         ?>
      </div>

      <div style="margin-top: 2rem; text-align:center;">
         <form action="" method="GET">
            <button type="submit" name="delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>" onclick="return confirm('Delete all from cart?');">Delete All</button>
         </form>
      </div>

      <div class="cart-total">
         <p>Grand Total: <span>P<?php echo $grand_total; ?></span></p>
         <div class="flex">
            <a href=".php" class="option-btn">Continue Shopping</a>
            <a href="checkout.php" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
         </div>
      </div>
   </section>

   <?php include 'footer.php'; ?>
   <script src="js/script.js"></script>

</body>

</html>
