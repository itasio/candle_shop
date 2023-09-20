<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
?>
 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <link rel="stylesheet" href="styles_main_site.css">
    <title> Welcome page</title>
</head>

<body>
    <div class="bg">
        <div class = "container">
            <div class = "navbar">
                <button class="dropdownButton"> Menu</button>
                <div class="dropdown-content">
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart</a>
                    <a href="seller.php">Seller (only for sellers)</a>
                    <a href="administration.php">Admin (only for admins)</a>
                </div>
                <input type="text" placeholder="Search.." class="searchbar">
                <div class="usr">
                    <?php echo htmlspecialchars($_SESSION["name"]). " " .htmlspecialchars($_SESSION["surname"]). " (".htmlspecialchars($_SESSION["role"]). ")" ; ?>
                </div>
                <a href="logout.php">Sign Out of Your Account</a>
                
            </div>


        </div>

        <div class="contentTable">
            <h1>Welcome to my candle store</h1>
            <h1>You can browse it through the menu</h1>
        </div>
    </div>

</body>
</html>

