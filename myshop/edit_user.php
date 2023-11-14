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

 $name = $_POST["name"];
 $surname = $_POST["surname"];
 $username = $_POST["username"];
 $email = $_POST["email"];
 $role_new = $_POST["role"];


/**Edit user choice was made
 * We need two calls to keyrock idm 
 * 1. Update a user
 * 2. Assign to or Remove a role from user
 * 
 * We need info about the current role of the user. If it is different from the one posted, only then it will be changed
 * Otherwise no changes about user role are made.
 * That's why we need  a third call to keyrock
 * 3. Get user information and roles
 */


 /**********Get user information and roles ****************/

 $curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://keyrock:3000/user?access_token='.$_SESSION["token"].'',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
$result = json_decode($response);

$userid = $result->id;
$username = $result->username;
$curr_role = $result->roles[0]->name;

?>