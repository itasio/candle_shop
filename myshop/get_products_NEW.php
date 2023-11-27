<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}

$post_fields='';
if(isset($_POST["name"])) $post_fields = '"name":"'.$_POST["name"].'"';


//make request to the api
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://php-apache-environment:80/api/products/read.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "name":"candle 2"
    }',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

// if($post_fields !=''){
//     //query by parameter requested
//     curl_setopt($curl, CURLOPT_POSTFIELDS, '{'.$post_fields.'}');
// }
echo($post_fields);
$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

echo $response;
curl_close($curl);
if($httpcode != 200){
    exit("An error occured while requesting products info.". curl_error($curl));
}

echo $response;
?>