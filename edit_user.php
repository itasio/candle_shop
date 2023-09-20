<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}elseif($_SESSION["role"] !="ADMIN"){
    header("location: access_denied.php");
    exit;
}

 // Include config file for db connectivity
 require_once "config.php";

 //query to run
 //not deleting, just inserting withdrawal date
 //product will be still on our database
 $sql = "UPDATE users 
         SET NAME = ?, SURNAME = ?, USERNAME = ?, EMAIL = ?, ROLE = ?, CONFIRMED = ? 
         WHERE ID = ?";
 
 
 


$name = $_POST["name"];
$surname = $_POST["surname"];
$username = $_POST["username"];
$email = $_POST["email"];
$role = $_POST["role"];
$confirmed = $_POST["confirmed"];
$uID = $_POST["uID"];

 if($stmt = $conn->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("sssssss",$name, $surname, $username, $email, $role, $confirmed, $uID);      
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        //delete success
        echo "User info have been updated successfully!"; 
        

    }else{
        echo "Oops! Something went wrong. Please try again later.";

    }
    // Close statement
    $stmt->close();
 }

?>