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

$msg = "Oops! Something went wrong. Please try again later.";

$name = $_POST["name"] ?? exit($msg);
$pcode = $_POST["pcode"] ?? exit($msg);
$pr = $_POST["pr"] ?? exit($msg);
$cat = $_POST["cat"] ?? exit($msg);
$pID = $_POST["pID"] ?? exit($msg);
// $quantity  = $_POST["quantity"] ?? exit($msg);
#TODO na mporw na kanw edit to quantity!!

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://php-apache-enviroment:80/api/products/update.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>'{
    "pID":"'.$pID.'",
    "name":"'.$name.'",
    "pr":'.$pr.',
    "pcode":"'.$pcode.'",
    "cat":"'.$cat.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if($httpcode != 200)
    echo $msg;
else
    echo "Your product has been updated successfully!";
?>