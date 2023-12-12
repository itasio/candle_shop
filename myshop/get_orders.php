<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}

//send request to Orders API
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://php-apache-environment:80/api/orders/read.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "user_username":"'.$_SESSION["username"].'"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: text/plain'
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if($httpcode != 200){
    echo "An error occured while retrieving your order history. Try again later.";
}else{
    $result = json_decode($response, true);

    echo "<table>";
    echo "<tr>";
    echo    "<th>Products</th>";
    echo    "<th>Status</th>";
    echo    "<th>Total price</th>";
    echo "</tr>"; 
    foreach ($result as $order) {
        $order_id = $order["_id"]["\$oid"];
        $total_price = $order["TOTAL_PRICE"];
        $status = $order["STATUS"];
        $products = $order["PRODUCTS"];
        foreach($products as $product){
            $name = $product["name"];
            $quantity = $product["amount"];
            $id = $product["PRODUCT_ID"];
            // echo $name;
            // echo $id;
            // echo $quantity;
            echo "<tr>";
                echo "<td>".$name."</td>";
                echo "<td>".$id."</td>";
                echo "<td>".$quantity."</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    //print_r($result);
    
    
    
}
?>