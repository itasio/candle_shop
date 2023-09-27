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
require_once "headers_to_array.php";
//query to run
//check if he deletes himself

if(!isset($_POST["identity"])){
    echo "An error occured with post data. Try again later.";
}elseif ($_POST["identity"] == $_SESSION["id"]) {   //admin tries to delete himself ->forbidden
    echo "You can not delete yourself! Other admins have the privilege to do it.";
}else{

    //i need admin's credentials to list and/or modify users
    /***************** ACQUIRE X-Subj-Token *****************/

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/auth/tokens");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_POST, TRUE);

    curl_setopt($ch, CURLOPT_POSTFIELDS, "{
        \"name\": \"admin@test.com\",
        \"password\": \"1234\"
    }");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json"
    ));

    $response = curl_exec($ch);  
    
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    curl_close($ch);

    $hc = new HeaderConverter();
    $data = $hc->headersToArray($header);
    $xtoken = $data['X-Subject-Token'];

/*************************Delete a user ***********************/
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/users/".$_POST["identity"]."");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "X-Auth-Token: ".$xtoken.""
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    echo "User has been deleted successfully!";
}
?>