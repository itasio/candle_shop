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

$username = $_SESSION["username"];

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
    "sellers":"'.$username.'",
    "status":"withdrawed"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if($httpcode != 200){
    exit("An error occured while requesting products info.". curl_error($curl));
}

$result = json_decode($response, true);

if(count($result) == 0){
    echo  "You have not any products currently available in our site. You can a
    add one from our menu.";
}else{
    echo "<table>";
    echo "<tr>";
        echo "<th>name</th>";
        echo "<th>product code</th>";
        echo "<th>price</th>";
        echo "<th>category</th>";
        echo "<th>withdrawal date</th>";
    echo "</tr>";

    foreach($result as $key){
        $name = $key["NAME"];
        $productcode = $key["PRODUCTCODE"];
        $price = $key["PRICE"];
        $category = $key["CATEGORY"];
        $dateofwithdrawal = $key["DATEOFWITHDRAWAL"];
        $quantity = $key["QUANTITY"];
        $sellername = $key["SELLERNAME"];
        $id = $key["_id"]["\$oid"];
        
        echo "<tr>";
            echo "<td>" . $name. "</td>";
            echo "<td>" . $productcode . "</td>";
            echo "<td>" . $price . "</td>";
            echo "<td>" . $category . "</td>";
            echo "<td>" . $dateofwithdrawal . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>