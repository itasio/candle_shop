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
$user_id = $_POST["uID"];

/**Edit user choice was made
 * We need two calls to keyrock idm 
 * 1. Update a user
 * 2. Assign to  and/or Remove a role from user
 * 
 * We need info about the current role of the user. If it is different from the one posted, only then it will be changed
 * Otherwise no changes about user role are made.
 * That's why we need  a third call to keyrock
 * 3. Get user information and roles
 */

/*****************Update a user**************** */

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://keyrock:3000/v1/users/'.$user_id.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PATCH',
  CURLOPT_POSTFIELDS =>'{
    "user":{
        "username": "'.$username.'",
        "email": "'.$email.'",
        "description": "'.$name.' '.$surname.' '.$role_new.'"
    }
}',
  CURLOPT_HTTPHEADER => array(
    'X-Auth-token: '.$_SESSION["x_subj"].'',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if($httpcode != 200){
    exit("An error occured while updating user info.");
}


/******************************Find the id of the current user's role/****************************** */

 /****************************** Authorized users in application****************************/

//create an assoc array
//auth_user[user_id] = role_id;
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$_SESSION["app_id"]."/users");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
"X-Auth-Token: ".$_SESSION["x_subj"].""
));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if($httpcode != 200){
    exit("An error occured while updating user's role.");
}

$result = json_decode($response);
$num_of_auth_users = count($result->role_user_assignments);
for ($i=0; $i < $num_of_auth_users; $i++) { 
    $auth_user[$result->role_user_assignments[$i]->user_id] = $result->role_user_assignments[$i]->role_id;
}

//maybe  an app has a user that is not assigned a role yet
//e.g. admin might have revoked his role
if(array_key_exists($user_id, $auth_user)){
    $curr_role_id = $auth_user[$user_id];
}else{
    $curr_role_id = "";
}

/****************************** List roles of application****************************/
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://keyrock:3000/v1/applications/'.$_SESSION['app_id'].'/roles',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    "X-Auth-Token: ".$_SESSION['x_subj'].""
),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if($httpcode != 200){
    exit("An error occured while updating user role.");
}
$result = json_decode($response);
$num_of_roles = count($result->roles); 
//create an assoc array
//roles[role_id] = role_name;
for ($i=0; $i < $num_of_roles; $i++) { 
    $roles[$result->roles[$i]->id] = $result->roles[$i]->name;
}


/*********************** Find the id of the new user's role****************/


if(in_array($role_new,$roles)){
	//new role is in the list of roles
    $new_role_id = array_search($role_new, $roles);
}else {
    exit("The role requested is not available");
}


if($curr_role_id == $new_role_id){
    //New role is same as the current role
    exit("Updates accomplished successfully.");
}else{
    //first revoke current role from the user and then assign the new role to user

    /******************Remove a role assignment from a user*********************/


    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://keyrock:3000/v1/applications/'.$_SESSION["app_id"].'/users/'.$user_id.'/roles/'.$curr_role_id.'',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_HTTPHEADER => array(
        "X-Auth-token: ".$_SESSION["x_subj"].""
    ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpcode != 204) {
        exit("An error occured while revoking the current role from the user");
    }
}


/******************** ASSIGN A ROLE TO A USER **********************/

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$_SESSION["app_id"]."/users/".$user_id."/roles/".$new_role_id."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "X-Auth-token: ".$_SESSION["x_subj"].""
));
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($httpcode != 201) {
    exit("An error occured while assinging new role to the user.");
}else{
    echo "User info updated successfully.";
}

?>