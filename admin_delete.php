<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit();
}

if (isset($_GET['id'])) {
   $product_id = $_GET['id'];

   // Delete the product from the database
   $query = "DELETE FROM products WHERE id = $product_id";
   $delete_product = mysqli_query($conn, $query);

   if ($delete_product) {
      header("Location: admin_inventory.php");
      exit();
   } else {
      echo "Failed to delete the product. Please try again.";
   }
}
?>
