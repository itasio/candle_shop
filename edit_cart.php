<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
   // if(isset($_POST["name"]) && isset($_POST["code"]) && isset($_POST["price"]) && isset($_POST["category"])
    // && isset($_POST["sellername"]) && isset($_POST["insertion_date"]) && isset($_POST["op"]))
    //{   
        if($_POST["op"] == "add"){
            $name = $_POST["name"];
            $code = $_POST["code"];
            $price = $_POST["price"];
            $category = $_POST["category"];
            $sellername = $_POST["sellername"];
            $insertion_date = $_POST["insertion_date"];
            $new_product = array("name"=>$name,"price"=>$price,"category"=>$category,
                                "sellername"=>$sellername,"insertion_date"=>$insertion_date,"quantity"=>1);
            
            //check if this product is already in the cart
            //if so just increase quantity
            //every item in the array will have product code as its key, and the array of the other attributes 
            //as its values
            if(array_key_exists($code,$_SESSION["cart"])){
                $_SESSION["cart"][$code]["quantity"] +=1;
                echo "You have ".$_SESSION["cart"][$code]["quantity"]." pieces from the product ".$name." in your cart";
            }else{
                //array_push($_SESSION["cart"], $code, $new_product);
                $_SESSION["cart"][$code]=$new_product;
                echo "The product ".$name." has been added in your cart"; 
            }
            //update sum of products
            $_SESSION["sum_of_cart"] += $price;
        }elseif ($_POST["op"] == "remove") {
            $code = $_POST["code"];
            $name = $_SESSION["cart"][$code]["name"];
            //update sum of products
            $_SESSION["sum_of_cart"] -= $_SESSION["cart"][$code]["price"];
            if($_SESSION["cart"][$code]["quantity"] == 1){
                //remove item from cart if there is only 1 of this item in the cart
                unset($_SESSION["cart"][$code]);
                echo "The product ".$name." has been removed from your cart"; 
            }else{
                //more than 1 items, so decrease quantity
                $_SESSION["cart"][$code]["quantity"] -=1;
                echo "The quantity of items in your cart has been decreased.";
            }
        }
    //}    
}
?>
