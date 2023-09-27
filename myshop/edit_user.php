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
if(!isset($_POST["uID"]) || !isset($_POST["name"]) || !isset($_POST["surname"]) ||
    !isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["role"])){
        echo "An error occured with post data. Try again later.";
}else{

    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $uID = $_POST["uID"];

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

    /*********************** Update a user ***********************/
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://keyrock:3000/v1/users/'.$uID.'',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PATCH',
    CURLOPT_POSTFIELDS =>'{ 
        "user": {
        "username": "'.$username.'",
        "description": "'.$name .' '.$surname.'",
        "email": "'.$email.'"
        }
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: '.$xtoken.''
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    
    //find the current role of the user

    /****************************** Authorized users in application****************************/
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$_SESSION["app_id"]."/users");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "X-Auth-Token: ".$_SESSION["x_subj"].""
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
    $num_of_auth_users = count($result->role_user_assignments);

    for ($i=0; $i < $num_of_auth_users; $i++) { 
        if($result->role_user_assignments[$i]->user_id == $uID){
            //this is the user we are looking for
            $cur_role_id = $result->role_user_assignments[$i]->role_id;
            break;
        }
    }
    $roleUserID = "6a80b3ad-b458-48f2-a223-fd8ae85c098d";
    $roleProductsellerID = "fd2c47a6-33ed-47c7-a549-892c1049d006";
    $roleAdminID = "bef9ae28-26bf-4380-906a-b5dc1bccb43d";

    if ($cur_role_id == $roleUserID) {
        $cur_role = "USER";
    }elseif($cur_role_id == $roleProductsellerID){
        $cur_role = "PRODUCTSELLER";
    }elseif($cur_role_id == $roleAdminID){
        $cur_role = "ADMIN";
    }
    if($cur_role != $role){
        //role of user must be changed
        //remove the current role from the user and also assign a new role to the user
        //since each user may have only one role

        /******************** REMOVE CURRENT ROLE FROM THE USER **********************/

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$_SESSION["app_id"]."/users/".$uID."/roles/".$cur_role_id."");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "X-Auth-token: ".$_SESSION["x_subj"].""
        ));

        $response = curl_exec($ch);
        curl_close($ch);


        /******************** ASSIGN A ROLE TO A USER **********************/
        
        
        if ($role == "USER") {
            $rolechoiceID =  $roleUserID;
        }elseif ($role == "PRODUCTSELLER") {
            $rolechoiceID = $roleProductsellerID;
        }elseif ($role == "ADMIN") {
            $rolechoiceID = $roleAdminID;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$_SESSION["app_id"]."/users/".$uID."/roles/".$rolechoiceID."");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "X-Auth-token: ".$_SESSION["x_subj"].""
        ));
        $response = curl_exec($ch);

        curl_close($ch);
    }
    echo "User info have been updated successfully!"; 
}
?>