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

$user_id = $_POST["identity"];

/*******************Delete a user ************/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/users/".$user_id."");
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "X-Auth-token: ".$_SESSION["x_subj"].""
));

$response = curl_exec($ch);
curl_close($ch);
if(!$response){
    exit("An error occured while deleting the user");
}else{
    echo "User deleted successfully.";
}
?>