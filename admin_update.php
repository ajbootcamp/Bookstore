<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

if (isset($_GET['id'])) {
   $product_id = $_GET['id'];

   $query = "SELECT * FROM products WHERE id = '$product_id'";
   $result = mysqli_query($conn, $query);
   $product = mysqli_fetch_assoc($result);

   if (!$product) {
      echo "Product not found!";
      exit;
   }
} else {
   echo "Invalid request!";
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $author = mysqli_real_escape_string($conn, $_POST['author']);
   $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $stock = mysqli_real_escape_string($conn, $_POST['stock']);

   $update_query = "UPDATE products SET name = '$name', author = '$author', isbn = '$isbn', price = '$price', stock = '$stock' WHERE id = '$product_id'";
   $update_result = mysqli_query($conn, $update_query);

   if ($update_result) {
      echo "<script>alert('Product updated successfully!');</script>";
      echo "<script>window.location.href = 'admin_inventory.php';</script>";
   } else {
      echo "Failed to update product!";
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Product</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      body {
         font-family: Arial, sans-serif;
      }
      .container {
         max-width: 600px;
         margin: 0 auto;
         padding: 20px;
      }
      .form-group {
         margin-bottom: 10px;
      }
      label {
         display: block;
         margin-bottom: 5px;
      }
      input[type="text"],
      input[type="number"] {
         width: 100%;
         padding: 5px;
         border: 1px solid #ccc;
         border-radius: 4px;
         box-sizing: border-box;
      }
      .btn {
         padding: 10px 20px;
         background-color: #4caf50;
         border: none;
         color: white;
         cursor: pointer;
         border-radius: 4px;
      }
   </style>
</head>

<body>
   <?php include 'admin_header.php'; ?>

   <div class="container">
      <h2>Update Product</h2>

      <form method="POST" action="">
         <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>">
         </div>

         <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" name="author" value="<?php echo $product['author']; ?>">
         </div>

         <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input type="text" name="isbn" value="<?php echo $product['isbn']; ?>">
         </div>

         <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" name="price" value="<?php echo $product['price']; ?>">
         </div>

         <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="text" name="stock" value="<?php echo $product['stock']; ?>">
         </div>

         <input type="submit" value="Update" class="btn">
      </form>
   </div>
</body>

</html>
