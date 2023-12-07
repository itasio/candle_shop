<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}

$post_fields='';
if(isset($_POST["name"]))
  $post_fields = '"name":"'.$_POST["name"].'"';
elseif(isset($_POST["sellers"]))
  $post_fields = '"sellers":"'.$_POST["sellers"].'"';
elseif(isset($_POST["minimum"]) && isset($_POST["maximum"])){
  if($_POST["minimum"] > $_POST["maximum"]){
    echo "Minimum price can't be greater than maximum price";
    exit;
  }
  $post_fields = '"minimum":"'.$_POST["minimum"].'","maximum":"'.$_POST["maximum"].'"';
}
elseif(isset($_POST["date"])) {
  $post_fields = '"date":"'.$_POST["date"].'"';
}elseif(isset($_POST["categories"])){
  $post_fields = '"categories":"'.$_POST["categories"].'"';
}

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
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

if($post_fields !=''){
    //query by parameter requested
    curl_setopt($curl, CURLOPT_POSTFIELDS, '{'.$post_fields.'}');
}

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);
if($httpcode != 200){
    exit("An error occured while requesting products info.". curl_error($curl));
}

$result = json_decode($response, true);

if(count($result) == 0){
  echo  "There aren't any products currently available in our site matching your criteria.";
}else{
  echo "<table>";
    echo "<tr>";
        echo "<th>name</th>";
        echo "<th>product code</th>";
        echo "<th>price</th>";
        echo "<th>category</th>";
        echo "<th>sellername</th>";
        echo "<th>quantity</th>";
        echo "<th>status</th>";
        //echo "<th></th>";
        echo "<th>action</th>";
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

      echo "<tr id= ". $id. ">";
            
        echo "<td data-target=\"NAME\">" . $name . "</td>";
        echo "<td data-target=\"PRODUCTCODE\">" . $productcode . "</td>";
        echo "<td data-target=\"PRICE\">" . $price . "</td>";
        echo "<td data-target=\"CATEGORY\">" . $category . "</td>";
        echo "<td data-target=\"SELLERNAME\">" . $sellername . "</td>";
        echo "<td data-target=\"QUANTITY\">" . $quantity . "</td>";
        if($dateofwithdrawal == ''){
          echo "<td data-target=\"DATEOFWITHDRAWAL\"> available </td>";
          echo "<td>" ."<button data-role= \"add\" data-id= ". $id." \"> Add to cart </button> </td>";
        }else{
          echo "<td data-target=\"DATEOFWITHDRAWAL\"> unavailable </td>";
        }
      echo "</tr>";
    }
  echo "</table>";
}


?>