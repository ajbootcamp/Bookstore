<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php include 'header.php'; ?>
<div class="heading">
   <h3>about us</h3>
   <p> <a href="home.php">home</a> / about </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>Choose our Bookstore for an exceptional reading experience. With an extensive collection spanning various genres, 
            our curated selection ensures there's something for every reader. 
            Our knowledgeable staff is passionate about literature and ready to provide personalized recommendations. 
            We prioritize customer satisfaction, offering exceptional service whether you're browsing online or 
            visiting our physical store. Enjoy competitive pricing, regular discounts, 
            and promotions that provide great value for your money. 
            Engage with our vibrant community through author events, book clubs, and literary discussions. 
            We guarantee the highest quality books, both new releases and well-preserved used copies. 
            Fuel your love for reading with our Bookstore, your go-to destination for all your literary needs.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>




<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>