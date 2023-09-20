<?php
//This script returns a dropdown list of the productsellers username 
//or of the product categories
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}

 // Include config file for db connectivity
 require_once "config.php";

define("category", "CATEGORY");
define("productseller", "PRODUCTSELLER");
$param = $_GET['q'];

if($param == productseller){
    //query to run
    $sql = "SELECT ID, USERNAME FROM users WHERE ROLE = 'PRODUCTSELLER'";

    $result = $conn->query($sql);
    if($result){
        echo "<label for=\"sellers\">Choose a product seller:</label>";
        echo "<select name=\"sellers\" id=\"sellers\">";
        while ($row = $result->fetch_assoc()) {
            echo "<option value=".$row['USERNAME'].">". $row['USERNAME'] ."</option>";
        }
        echo "</select>";
        echo "<br>";
    }else{
        echo "Oops! Something went wrong. Please try again later.";
    }
}elseif($param == category){
    //query to run
    $sql = "SELECT DISTINCT CATEGORY FROM products";

    $result = $conn->query($sql);
    if($result){
        echo "<label for=\"categories\">Choose a product category:</label>";
        echo "<select name=\"categories\" id=\"categories\">";
        while ($row = $result->fetch_assoc()) {
            echo "<option value=".$row[category].">". $row[category] ."</option>";
        }
        echo "</select>";
        echo "<br>";
    }else{
        echo "Oops! Something went wrong. Please try again later.";
    }
}else{
    echo "Oops! Something went wrong. Please try again later.";
}
    // Close connection
    $conn->close();


?>