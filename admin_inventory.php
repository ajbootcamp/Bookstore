<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

if (isset($_GET['id']) && isset($_GET['status'])) {
   $order_id = $_GET['id'];
   $status = $_GET['status'];
   if ($status === 'completed') {
      $update_query = "UPDATE orders SET status = 'completed' WHERE id = '$order_id'";
      mysqli_query($conn, $update_query) or die('Query failed');

      $select_products_query = "SELECT product_id, quantity FROM order_items WHERE order_id = '$order_id'";
      $select_products_result = mysqli_query($conn, $select_products_query) or die('Query failed');
      while ($fetch_products = mysqli_fetch_assoc($select_products_result)) {
         $product_id = $fetch_products['product_id'];
         $quantity = $fetch_products['quantity'];

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
   <title>Inventory Records</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>

<body style="font-family: Arial, sans-serif;">
   <?php include 'admin_header.php'; ?>
   <form method="GET" action="">
      <input type="text" name="search" placeholder="Search by name, author, or ISBN" style="padding: 5px;">
      <input type="submit" value="Search" style="padding: 5px;">
   </form>

   <?php
   include 'config.php';

   if (isset($_GET['search'])) {
      $search = mysqli_real_escape_string($conn, $_GET['search']);
      $query = "SELECT * FROM products WHERE name LIKE '%$search%' OR author LIKE '%$search%' OR isbn LIKE '%$search%'";
   } else {
      $query = "SELECT * FROM products";
   }

   $select_products = mysqli_query($conn, $query) or die('query failed');

   if (mysqli_num_rows($select_products) > 0) {
      echo "<table style='width: 100%; border-collapse: collapse;'>
               <tr>
                  <th style='border: 1px solid black; padding: 8px;'>Name</th>
                  <th style='border: 1px solid black; padding: 8px;'>Author</th>
                  <th style='border: 1px solid black; padding: 8px;'>ISBN</th>
                  <th style='border: 1px solid black; padding: 8px;'>Price</th>
                  <th style='border: 1px solid black; padding: 8px;'>Stock</th>
                  <th style='border: 1px solid black; padding: 8px;'>Actions</th>
               </tr>";
      while ($fetch_products = mysqli_fetch_assoc($select_products)) {
         echo "<tr>
                  <td style='border: 1px solid black; padding: 8px;'>" . $fetch_products['name'] . "</td>
                  <td style='border: 1px solid black; padding: 8px;'>" . $fetch_products['author'] . "</td>
                  <td style='border: 1px solid black; padding: 8px;'>" . $fetch_products['isbn'] . "</td>
                  <td style='border: 1px solid black; padding: 8px;'>" . $fetch_products['price'] . "</td>
                  <td style='border: 1px solid black; padding: 8px;'>" . $fetch_products['stock'] . "</td>
                  <td style='border: 1px solid black; padding: 8px;'>
                     <a href='admin_update.php?id=" . $fetch_products['id'] . "' class='update-btn'><i class='fas fa-edit'></i></a>
                  </td>
               </tr>";
      }
      echo "</table>";
   } else {
      echo '<p>No products found!</p>';
   }
   ?>
<script src="js/admin_script.js"></script>

</body>

</html>
