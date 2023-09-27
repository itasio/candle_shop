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
//i want to get from keyrock for each user authorized in our app some info
//specifically ID, NAME, SURNAME, USERNAME, EMAIL, ROLE
//user cannot be disabled through the api so only the action delete can be made
//which is done through the delete_user.php file

//i need List roles -> for all roles in our application: role_id, role_name   => create assoc array roles[role_id] = role_name
//i need Authorized users in an application -> user_id, role_id               => create assoc array auth_user[user_id] = role_id
//i need List users request (X-subj) -> user id, username, email, name, surname 



/****************************** List roles of application****************************/
$curl = curl_init();
//shows every user, that's why every user must be authorized for our application
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
if($response){
  curl_close($curl);
  $result = json_decode($response);
//echo $response;
  $num_of_roles = count($result->roles); 
  //create an assoc array
  //roles[role_id] = role_name;
  for ($i=0; $i < $num_of_roles; $i++) { 
      $roles[$result->roles[$i]->id] = $result->roles[$i]->name;
  }
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
  curl_close($ch);
  if($response){
    $result = json_decode($response);
//echo $response;
    $num_of_auth_users = count($result->role_user_assignments);
    for ($i=0; $i < $num_of_auth_users; $i++) { 
      $auth_user[$result->role_user_assignments[$i]->user_id] = $result->role_user_assignments[$i]->role_id;
    }


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

    
    /****************************** List users****************************/

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://keyrock:3000/v1/users',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        "X-Auth-Token: ".$xtoken.""
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    if($response){
      $result = json_decode($response, true);
//echo $response;
      echo "<table>";
      echo "<tr>";
          echo "<th>name</th>";
          echo "<th>surname</th>";
          echo "<th>username</th>";
          echo "<th>email</th>";
          echo "<th>role</th>";
          echo "<th>edit</th>";
          echo "<th>delete</th>";
      echo "</tr>";

      foreach($result as $key){
        foreach($key as $user){
          $userid = $user['id'];
          $fullname = explode(" ", $user['description']);
          $name = $fullname[0];
          $surname = $fullname[1];
          $username = $user['username'];
          $email = $user['email'];
          $roleid_of_user = $auth_user[$userid];
          $role_of_user = $roles[$roleid_of_user];
          echo "<tr id= ". $userid. ">";

            echo "<td data-target=\"NAME\">" . $name . "</td>";
            echo "<td data-target=\"SURNAME\">" . $surname . "</td>";
            echo "<td data-target=\"USERNAME\">" . $username . "</td>";
            echo "<td data-target=\"EMAIL\">" . $email . "</td>";
            echo "<td data-target=\"ROLE\">" . $role_of_user . "</td>";
            echo "<td>" ."<button data-role= \"edit\" data-id= ". $userid." \"> Edit </button> </td>";
            echo "<td>" ."<button data-role= \"delete\" data-id= ". $userid." \"> Delete </button> </td>";
          echo "</tr>";
        }
      }
      echo "</table>";
    }else{//List users
      echo "An error occured. Try again later.";
  }
  }else{//Authorized users in application
    echo "An error occured. Try again later.";
  }
}else{// List roles of application
  echo "An error occured. Try again later.";
}
?>