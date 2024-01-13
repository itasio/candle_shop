<?php
echo '
<div class = "container">
    <div class = "navbar">
        <button class="dropdownButton"> Menu</button>
        <div class="dropdown-content">';
if ($_SESSION["role"] == "ADMIN"){
    echo '
        <a href="my_orders.php">My orders</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart</a>
        <a href="administration.php">Admin</a>
    ';
}elseif($_SESSION["role"] == "PRODUCTSELLER"){
    echo '<a href="seller.php">My products info</a>';
}elseif($_SESSION["role"] == "USER"){
    echo '
        <a href="my_orders.php">My orders</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart</a>
    ';
}
echo '
        </div>
        <input type="text" placeholder="Search.." class="searchbar">
        <div class="usr">
            '. htmlspecialchars($_SESSION["name"]). " " .htmlspecialchars($_SESSION["surname"]). " (".htmlspecialchars($_SESSION["role"]). ")".'
        </div>
        <a href="logout.php">Sign Out of Your Account</a>
        
    </div>
</div>';
?>
