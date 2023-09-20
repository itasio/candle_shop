<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}elseif($_SESSION["role"] !="PRODUCTSELLER"){
    header("location: access_denied.php");
    exit;
}

 // Include config file for db connectivity
 require_once "config.php";

 //query to run
 //not deleting, just inserting withdrawal date
 //product will be still on our database
 $sql ="UPDATE products SET DATEOFWITHDRAWAL = NOW() WHERE ID = ?";


$id = $_POST["identity"];

 if($stmt = $conn->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $id);      
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        //delete success
        echo "Your product has been deleted successfully!"; 
        

    }else{
        echo "Oops! Something went wrong. Please try again later.";

    }
    // Close statement
    $stmt->close();
 }

?>