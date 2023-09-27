<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
if($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Include config file for db connectivity
    require_once "config.php";
    //we don't have user id, so we must retrieve it

    $sql = "SELECT ID
            FROM users
            WHERE USERNAME = ?";
    
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s",$_SESSION["username"]);      
        // Attempt to execute the prepared statement
        if($stmt->execute()){
           $stmt->bind_result($uid);
           $stmt->fetch();
        }else{
            echo "Oops! Something went wrong. Please try again later.";
            exit();
        }
        // Close statement
        $stmt->close();
     }
        if($_POST["op"] == "add"){

            //send sql query for cart update
            //if successful proceed to update UI
            $product_id = $_POST["prod_id"];
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
                $sql = "UPDATE carts
                        SET QUANTITY = QUANTITY + 1
                        WHERE PRODUCT_ID = ? and USER_ID = ?";
                if($stmt = $conn->prepare($sql)){
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("ii",$product_id, $uid);      
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        //update quantity
                        $_SESSION["cart"][$code]["quantity"] +=1;
                        echo "You have ".$_SESSION["cart"][$code]["quantity"]." pieces from the product ".$name." in your cart";
                    }else{
                        echo "Oops! Something went wrong. Please try again later.";
                        exit();
                    }
                    // Close statement
                    $stmt->close();
                 }
                
                
            }else{
                //add to cart/wishlist
                $insertion_date=date('y-m-j');  //format date for sql query as YYYY-MM-DD
                $sql = "INSERT into carts (PRODUCT_ID, USER_ID, DATEOFINSERTION) VALUES (?,?,?)";
                if($stmt = $conn->prepare($sql)){
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("iis",$product_id, $uid, $insertion_date);      
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        $_SESSION["cart"][$code]=$new_product;
                        echo "The product ".$name." has been added in your cart"; 
                    }else{
                        echo $insertion_date;
                        echo "Oops! Something went wrong. Please try again later.";
                        exit();
                    }
                    // Close statement
                    $stmt->close();
                 }
            }
            //update sum of products
            $_SESSION["sum_of_cart"] += $price;
        }elseif ($_POST["op"] == "remove") {
            //send sql query for cart update
            //we don't have product id available as POST variable, so we must retrieve it
            
            //if successful proceed to update UI
            $code = $_POST["code"];
            $name = $_SESSION["cart"][$code]["name"];
            //update sum of products
            $_SESSION["sum_of_cart"] -= $_SESSION["cart"][$code]["price"];
            if($_SESSION["cart"][$code]["quantity"] == 1){
                //delete cart/wishlist
                //remove item from cart if there is only 1 of this item in the cart
                unset($_SESSION["cart"][$code]);
                echo "The product ".$name." has been removed from your cart"; 
            }else{
                //update quantity
                //more than 1 items, so decrease quantity
                $_SESSION["cart"][$code]["quantity"] -=1;
                echo "The quantity of items in your cart has been decreased.";
            }
        }
}
?>
