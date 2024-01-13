<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
?>
 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <link rel="stylesheet" href="styles_main_site.css">
    <link rel="icon" href="favicon.png" />
    <title> Access Denied</title>
</head>

<body>
    <div class="bg">
        <?php include_once "menu.php";?>
        <div class="contentTable">
            <h1>You don't have privileges to access this page</h1>
            <a href="welcome.php">Return to home page</a>
        </div>
    </div>

</body>
</html>

