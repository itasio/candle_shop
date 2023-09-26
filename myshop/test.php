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
    
            if(isset($_POST["name"]) && isset($_POST["prod_code"]) && isset($_POST["prod_price"]) && isset($_POST["prod_category"])){
    
                $name = $_POST["name"];
                $prod_code = $_POST["prod_code"];
                $prod_price = $_POST["prod_price"];
                $prod_category = $_POST["prod_category"];
                $username = $_SESSION["username"];
                if(!validInput($name, $prod_code, $prod_price, $prod_category)){
                    echo 'You cannot submit blank fields';
                }
               
               
               else{
                    $sql = "INSERT INTO products(NAME, PRODUCTCODE, PRICE, SELLERNAME, CATEGORY) 
                    VALUES ('$name', '$prod_code', '$prod_price', '$username','$prod_category')";
                    
        
                    $result = $conn->query($sql);
                        
                    if($result){
                        
                        echo 'You have successfully added a new product.';
        
                    } else{
                        echo "An unexpected error occured. Please try again later.";
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
            }
    
    
    
         $conn->close();
    
        } 
    
        function validInput($name, $prod_code, $prod_price, $prod_category){
            if(empty(trim($name)) || empty(trim($prod_code)) ||empty(trim($prod_price)) || empty(trim($prod_category)))
                return false;
            return true;
        }
    ?>

    