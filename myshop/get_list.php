<?php
//This script returns a dropdown list of the productsellers username 
//or of the product categories
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
$param = $_GET['q'];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://php-apache-environment:80/api/products/distinct.php?q='.$param.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);
if($httpcode != 200){
    exit("An error occured while requesting products info.". curl_error($curl));
}
$result = json_decode($response, true);

if($param == "productseller"){
    echo "<label for=\"sellers\">Choose a product seller:</label>";
    echo "<select name=\"sellers\" id=\"sellers\">";
    foreach ($result as $item) {
        echo "<option value=".$item.">". $item ."</option>";
    }
    echo "</select>";
    echo "<br>";
}elseif ($param == "category") {
    echo "<label for=\"categories\">Choose a product category:</label>";
    echo "<select name=\"categories\" id=\"categories\">";
    foreach ($result as $item) {
        echo "<option value=".$item.">". $item ."</option>";
    }
    echo "</select>";
    echo "<br>";
}
?>