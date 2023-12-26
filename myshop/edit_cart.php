<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
if($_SERVER["REQUEST_METHOD"] == "POST") {  
        if($_POST["op"] == "add"){

            //update SESSION[cart] and then update UI
            $id = $_POST["prod_id"];
            $name = $_POST["name"];
            $code = $_POST["code"];
            $price = $_POST["price"];
            $category = $_POST["category"];
            $sellername = $_POST["sellername"];
            $insertion_date = $_POST["insertion_date"];

            $new_product = array("name"=>$name,"price"=>$price,"category"=>$category,
                                "sellername"=>$sellername,"insertion_date"=>$insertion_date,"quantity"=>1);
            
            //check if this product is already in the cart
            //if so just increase quantity
            //every item in the array will have product id as its key, and the array of the other attributes 
            //as its values
            if(array_key_exists($id,$_SESSION["cart"])){
                //update quantity
                $_SESSION["cart"][$id]["quantity"] +=1;
                echo "You have successfully added ".$name." in your cart.\n";
                echo "In total ".$_SESSION["cart"][$id]["quantity"]." pieces of ".$name." in your cart.";
            }else{
                //add to cart/wishlist
                $insertion_date=date('y-m-j');  //format date for sql query as YYYY-MM-DD
                $_SESSION["cart"][$id]=$new_product;
                echo "You have successfully added ".$name." in your cart.";   

            }
            //update sum of products
            $_SESSION["sum_of_cart"] += $price;
        }elseif ($_POST["op"] == "remove") {         
            //if successful proceed to update UI
            $id = $_POST["prod_id"];
            $name = $_SESSION["cart"][$id]["name"];
            //update sum of products
            $_SESSION["sum_of_cart"] -= $_SESSION["cart"][$id]["price"];
            if($_SESSION["cart"][$id]["quantity"] == 1){
                //delete cart/wishlist
                //remove item from cart if there is only 1 of this item in the cart
                unset($_SESSION["cart"][$id]);
                echo "The product ".$name." has been removed from your cart"; 
            }else{
                //update quantity
                //more than 1 items, so decrease quantity
                $_SESSION["cart"][$id]["quantity"] -=1;
                echo "The quantity of items in your cart has been decreased.";
            }
        }elseif ($_POST["op"] == "clear") {
            $_SESSION["cart"] = array();
            $_SESSION["sum_of_cart"] = 0;
            echo "Your cart is now empty.";

        }elseif ($_POST["op"] == "order") {
            //send request to the Orders Service API to place order
            //reminder: order will have status: PENDING

            $order_info = new stdClass();

            $order_info-> total_price = $_SESSION["sum_of_cart"];
            $order_info->user_username = $_SESSION["username"];
            $order_info->products = array();

            foreach($_SESSION["cart"] as $product => $product_data){
                $info_array = $product_data;
                $pushed_data = ["name"       => $info_array["name"],
                                "amount"     => $info_array["quantity"],
                                "PRODUCT_ID" => $product              
                               ];

                
                $order_info->products[] = $pushed_data; //push to array
            }

            $post_data = json_encode($order_info);  

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://php-apache-environment:80/api/orders/create.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$post_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);


            if($httpcode != 201){
                echo "Oops something went wrong with your order. Please try again.";
            }else{
                echo"Congrats your order was placed successfully.\nStatus: Pending\n\n";
                $result = json_decode($response,true);
                //Also clear the cart
                $_SESSION["cart"] = array();
                $_SESSION["sum_of_cart"] = 0;
                echo "Your cart is now empty. Head to My Orders to review your order.\n";
            }
        }
}


?>
