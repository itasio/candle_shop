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
 $sql ="UPDATE products SET NAME = ?, PRODUCTCODE = ?, PRICE = ?, CATEGORY = ? WHERE ID = ?";
 

#TODO na mporw na kanw edit to quantity!!
$name = $_POST["name"];
$pcode = $_POST["pcode"];
$pr = $_POST["pr"];
$cat = $_POST["cat"];
$pID = $_POST["pID"];
$quantity  = $_POST["quantity"];

 if($stmt = $conn->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("ssdsi",$name, $pcode, $pr, $cat, $pID);      
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        //delete success
        echo "Your product has been updated successfully!"; 
        

    }else{
        echo "Oops! Something went wrong. Please try again later.";

    }
    // Close statement
    $stmt->close();
 }

?>