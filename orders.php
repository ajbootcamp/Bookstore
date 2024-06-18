<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location: login.php');
   exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
   $order_id = $_GET['id'];
   $status = $_GET['status'];
   if ($status === 'completed') {
      $update_query = "UPDATE orders SET status = 'completed' WHERE id = '$order_id'";
      mysqli_query($conn, $update_query) or die('Query failed');

      // Update the stock quantity of items in the order
      $select_products_query = "SELECT product_id, quantity FROM order_items WHERE order_id = '$order_id'";
      $select_products_result = mysqli_query($conn, $select_products_query) or die('Query failed');
      while ($fetch_products = mysqli_fetch_assoc($select_products_result)) {
         $product_id = $fetch_products['product_id'];
         $quantity = $fetch_products['quantity'];

         // Deduct the quantity from the stock
         $update_stock_query = "UPDATE products SET stock = stock - '$quantity' WHERE id = '$product_id'";
         mysqli_query($conn, $update_stock_query) or die('Query failed');
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Your Orders</h3>
   <p><a href="home.php">Home</a> / Orders</p>
</div>

<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>
   <div class="box-container">
      <?php
      $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('Query failed');
      if (mysqli_num_rows($order_query) > 0) {
         while ($fetch_orders = mysqli_fetch_assoc($order_query)) {
            ?>
            <div class="box">
               <p>Placed on: <span><?php echo $fetch_orders['placed_on']; ?></span></p>
               <p>Name: <span><?php echo $fetch_orders['name']; ?></span></p>
               <p>Number: <span><?php echo $fetch_orders['number']; ?></span></p>
               <p>Email: <span><?php echo $fetch_orders['email']; ?></span></p>
               <p>Address: <span><?php echo $fetch_orders['address']; ?></span></p>
               <p>Payment Method: <span><?php echo $fetch_orders['method']; ?></span></p>
               <p>Your Orders: <span><?php echo $fetch_orders['total_products']; ?></span></p>
               <p>Total Price: <span>P<?php echo $fetch_orders['total_price']; ?></span></p>
               <p>Payment Status: <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') {
                     echo 'red';
                  } else {
                     echo 'green';
                  } ?>;"><?php echo $fetch_orders['payment_status']; ?></span></p>
               <?php if ($fetch_orders['payment_status'] != 'completed') { ?>
                  <a href="orders.php?id=<?php echo $fetch_orders['id']; ?>&status=completed" class="order-status">Mark as Completed</a>
               <?php } ?>
            </div>
            <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
