<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
$products = $_SESSION["cart"];
if(count($products) == 0)

    echo "<p> <b>You haven't any products in your cart to display </b> </p>";
else{

    echo "<h2>These are your cart info</h2>";
    echo "<table>";
    echo "<tr>";
    echo    "<th>Code</th>";
    echo    "<th>Name</th>";
    echo    "<th>Price</th>";
    echo    "<th>Category</th>";
    echo   "<th>Sellername</th>";
    echo    "<th>Cart insertion date</th>";
    echo    "<th>Quantity</th>";
    echo    "<th>Action</th>";
    echo "</tr>"; 
            $keys = array_keys($products);
            for($i = 0; $i < count($products); $i++) {
                echo "<tr>";
                echo "<td>".$keys[$i]."</td>";
                foreach($products[$keys[$i]] as $key => $value) {
                    echo "<td>".$value."</td>";

                }
                echo "<td>" ."<button data-role= \"remove\" data-id=".$keys[$i]." \"> Remove from cart </button> </td>";
                echo "</tr>";
            }
    echo "</table>";
    echo "<hr>";
    echo "<h3 style=\"color: black;\">Sum</h3 >";
    echo "<div id = \"sum_container\">";
    echo "<b style=\"color: black;\">".$_SESSION["sum_of_cart"]."</b>";
    echo "</div>";

}
?>