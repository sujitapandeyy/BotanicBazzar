<?php
require('connection.php');
session_start();
if(isset($_SESSION['username'])){
    $email = $_SESSION['useremail'];
}
// else{
//     header("Location: login.php?error=Login first");
// }?>
 <?php




if(isset($_POST['add_to_cart'])){

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];
    $product_availableQuantity = $_POST['product_availableQuantity'];

    // $check_cart_numbers = mysqli_query($con, "SELECT * FROM `cart` WHERE name = '$product_name'") or die('query failed');
    $check_cart_numbers = mysqli_query($con, "SELECT * FROM `cart` WHERE name = '$product_name' AND email = '$email'") or die('query failed');

    if(mysqli_num_rows($check_cart_numbers) > 0){
        // "error=Already added to cart";
        header("Location: product.php?error=Already added to cart");
        
      } elseif ($product_quantity > $product_availableQuantity) {
         // Requested quantity exceeds the available quantity, display error message
         header("Location: product.php?error=Requested quantity exceeds available quantity in stock!!");
         exit();
     }else{
        
        
        mysqli_query($con, "INSERT INTO `cart`(email,pid, name, price, quantity, image) VALUES('$email','$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
        header("Location: product.php?error=product added to cart");
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>product</title>
   <script src="https://kit.fontawesome.com/72f30a4d56.js" crossorigin="anonymous"></script>
    <link rel="icon" href="favIcon.png" type="image/png">
    <link rel="stylesheet" href="css/stal2.css?v=10">
    <style>
    .error-container {
         /* text-align: center; */
         /* margin: 10px 0; */
         }

      .error-container.success {
         background-color: lightgreen;
          }

      .error-container.failed {
         background-color: lightcoral;
         }

         /* catagories ko nav design */
         .box-container {
         /* display:block; */
         /* background:orange; */
         
         }

         .box-container h4 {
         margin: 0;
         padding: 5px;
         text-align: center;

         }

         .box-container h4 a {
         text-decoration: none;
         text-align: center;
         font-weight: bold;
            font-size: 20px;

         }

         .box-container h4 a:hover {
            /* border-bottom: 2px solid  #088178; */
            color: #088178;
         }

         .heading h3{
            color:#088178;
            text-align: center;
            font-weight: bold;
            font-size: 40px;
         }
         </style>
    
</head>
<body>
   <?php @include 'header.php'; ?>
   <section id="product-banner">
        <div class="product-banner-text" background="img/FunFact.jpg" >
            <h1>#Discover the green path to wellness and vibrant living.</h1>
            <h2>get <span id="red">20%</span> off on first purchase</h2>
            
        </div>
    </section>
   <section class="heading">
      <h3>CATEGORIES</h3>
   </section>

   <section class="products">
      <!-- Display the list of plant types -->
      <div class="box-container">
         <?php
      
         // Retrieve distinct plant types from the database
         
         $select_plant_types = mysqli_query($con, "SELECT DISTINCT type FROM `products`") or die('query failed');
         if (mysqli_num_rows($select_plant_types) > 0) {
            while ($fetch_plant_types = mysqli_fetch_assoc($select_plant_types)) {
               $plant_type = $fetch_plant_types['type'];
               ?>
               <!-- Display each plant type as a heading with a link -->
               <h4><a href="#<?php echo $plant_type; ?>"><?php echo $plant_type; ?></a></h4>
               <?php
            }
         } else {
            echo '<p class="empty">No plant types found!</p>';
         }
         ?>

      </div><?php
      if(isset($_GET['error'])): ?>
           <?php
           $errorMessage = $_GET['error'];
           $errorClass = ($errorMessage === 'product added to cart'||$errorMessage === 'Product added to cart') ? 'success' : 'failed';
           ?>
           <div class="error-container <?php echo $errorClass; ?>">
              <p class="formerror"><?php echo $errorMessage; ?></p>
           </div>
           <?php endif; ?>
      <!-- Display the plants of each plant type -->
      
      <?php
         $select_plant_types = mysqli_query($con, "SELECT DISTINCT type FROM `products`") or die('query failed');
         if (mysqli_num_rows($select_plant_types) > 0) {
            while ($fetch_plant_types = mysqli_fetch_assoc($select_plant_types)) {
               $plant_type = $fetch_plant_types['type'];
               ?>
               <section id="<?php echo $plant_type; ?>">
                  <h3><?php echo $plant_type; ?></h3>
           
                  <div class="box-container">
                     <?php
                     // Retrieve plants of the selected type from the database
                     $select_plants = mysqli_query($con, "SELECT * FROM `products` WHERE type = '$plant_type'") or die('query failed');
                     if (mysqli_num_rows($select_plants) > 0) {
                        while ($fetch_plants = mysqli_fetch_assoc($select_plants)) {
                           ?>
                           <!-- Display each plant -->
                           <!-- <form action="" method="POST" class="box"> -->
                           <form action="" method="POST" class="box" onclick="window.location.href='view.php?pid=<?php echo $fetch_plants['id']; ?>'">

                              <img src="uploaded_img/<?php echo $fetch_plants['image']; ?>" alt="" class="image">
                              <div class="price">₹<?php echo $fetch_plants['price']; ?>/-</div>
                              <div class="name"><?php echo $fetch_plants['name']; ?></div>
                              <!-- <a href="view.php?pid=<?php echo $fetch_plants['id']; ?>" class="view">view</a> -->
                              <input type="hidden" name="product_id" value="<?php echo $fetch_plants['id']; ?>">
                              <input type="hidden" name="product_name" value="<?php echo $fetch_plants['name']; ?>">
                              <input type="hidden" name="product_availableQuantity" value="<?php echo $fetch_plants['p_quantity']; ?>">
                              <input type="hidden" name="product_type" value="<?php echo $fetch_plants['type']; ?>">
                              <input type="hidden" name="product_price" value="<?php echo $fetch_plants['price']; ?>">
                              <input type="hidden" name="product_image" value="<?php echo $fetch_plants['image']; ?>">
                              <?php
                              if (isset($_SESSION['loggedin']) == true) {
                                 echo '<input type="number" name="product_quantity" value="1" min="1" class="qty"><br/>';
                                 echo '<input type="submit" value="Add to Cart" name="add_to_cart" class="btn">';
                              }
                              ?>
                           </form>
                           <?php
                        }
                     } else {
                        echo '<p class="empty">No plants found for the selected type!</p>';
                     }
                     ?>
                  </div>
               </section>
               <?php
            }
         }
      ?>
   </section>

   <?php @include 'footer.php'; ?>
</body>
</html>
