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
//check if he deletes himself
$id = $_POST["identity"];
$sql = "SELECT USERNAME FROM users where ID = '$id'";
$result = $conn->query($sql);

if($result->fetch_assoc()['USERNAME'] == $_SESSION["username"]){
    //admin tries to delete himself
        echo "You can not delete yourself! Other admins have the privilege to do it. ";
}else{
    //admin does not delete himself
    $sql ="DELETE FROM users WHERE ID = '$id'";
    $result = $conn->query($sql);
    if($result){
        echo "User has been deleted successfully!";
    }else{
        echo "An unexpected error occured. Please try again later.". "Error: " . "<br>" . $conn->error;
    }

}
 $conn->close();

?>