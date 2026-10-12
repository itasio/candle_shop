<?php
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedinToMyShop"]) && $_SESSION["loggedinToMyShop"] === true){
    header("location: welcome.php");
    exit;
}
require_once "headers_to_array.php";
// Define variables and initialize with empty values
$errorMessage = '';
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];
    // Check if username or password is empty
    if(!validInput($email, $password)){
        $errorMessage =  'You cannot submit blank fields';
    }else{
        /************************** ACQUIRE X-AUTH-TOKEN WITH ADMIN DATA **************************/

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/auth/tokens");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
          \"name\": \"".$email."\",
          \"password\": \"".$password."\"
        }");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json"
        ));
        $response = curl_exec($ch);  
        //$res = json_decode($response);
        

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);
        
       // var_dump($data['X-Subject-Token']);
        // if(property_exists($response,"error"))
        //   echo "err";
        //   else
        //   echo "nn";
        // if($res === null){
        //   $errorMessage = 'Invalid username or passworddddddd.';
        // }else{
        $hc = new HeaderConverter();
        $data = $hc->headersToArray($header);

        //var_dump($data);
        if(array_key_exists('X-Subject-Token', $data)){
        $xtoken = $data['X-Subject-Token'];

        /********************************** AUTHENTICATE USER AND GENERATE TOKEN ****************************/

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://keyrock:3000/oauth2/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'grant_type=password&username='.$email.'&password='.$password.'',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',            
            'Authorization: Basic YTZmZDBlMzctZjY3My00YTE2LWI0YjMtMjEzZDZkNzM0YjkxOjJlNzg4YjZmLTQzM2QtNGU1NC04ZGQyLTAwMmY4Y2ZiZmRkYQ=='
          
          ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        $result1 = json_decode($response);

        if(!property_exists($result1,"error")){

          /************************Get User info and roles********************************/
          $x_oath = $result1->access_token;
          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://keyrock:3000/user?access_token='.$x_oath.'',
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
          //var_dump($result);
          $userid = $result->id;
          $username = $result->username;
          $role = $result->roles[0]->name;
          
          // var_dump($result->id);
          // var_dump($result->username);
          // var_dump($result->roles[0]->name);
          
          /************************ACQUIRE NAME AND SURNAME FROM DESCRIPTION (READ INFO ABOUT A USER)********************************/

          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://keyrock:3000/v1/users/'.$userid.'',
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
          $result = json_decode($response);

          $fullname = $result->user->description;
          $fullname_exp = explode(" ", $fullname);
          $name = $fullname_exp[0];
          $surname = $fullname_exp[1];
          


          // /************************ACQUIRE USERNAME********************************/

          // $curl = curl_init();

          // curl_setopt_array($curl, array(
          //   CURLOPT_URL => 'http://keyrock:3000/v1/users',
          //   CURLOPT_RETURNTRANSFER => true,
          //   CURLOPT_ENCODING => '',
          //   CURLOPT_MAXREDIRS => 10,
          //   CURLOPT_TIMEOUT => 0,
          //   CURLOPT_FOLLOWLOCATION => true,
          //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          //   CURLOPT_CUSTOMREQUEST => 'GET',
          //   CURLOPT_HTTPHEADER => array(
          //     "X-Auth-Token: ".$xtoken.""
          //   ),
          // ));

          // $response = curl_exec($curl);

          // curl_close($curl);
          // //echo $response;
          // $result = json_decode($response, true);

          // foreach($result as $key){
          //   foreach($key as $doc){

          //     if($doc['email'] == $email){
          //       $userid = $doc['id'];
          //       $username = $doc['username'];
          //       $fullname = explode(" ", $doc['description']);
          //       //$fullname = $doc['description'];
          //       $name = $fullname[0];
          //       $surname = $fullname[1];
          //     }

          //   }
          // }

          // /***************************ACQUIRE ROLE ***************************/

          // $appID = "6d4379ad-725a-40b9-b91f-f2d5ddf48ace";
          // $roleUserID = "59c1ff0a-6292-4dc1-966d-2d6f3fb7e6b7";
          // $roleProductsellerID = "adb64b81-d753-4a38-a712-f72c07e4ff31";
          // $roleAdminID = "22035ca4-3aa1-41b0-bc3d-d1baa9087bda";

          // $ch = curl_init();

          // curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$appID."/users/".$userid."/roles");
          // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
          // curl_setopt($ch, CURLOPT_HEADER, FALSE);
          // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          //   "X-Auth-token: ".$xtoken.""
          // ));
          // $response = curl_exec($ch);
          // curl_close($ch);
          // $result = json_decode($response, true);
          
          // foreach($result as $key){
          //   foreach($key as $doc){
          //     $role = $doc['role_id'];
          //   }
          // }

          // if ($role == $roleUserID) {
          //   $role = "User";
          // }
          // elseif ($role == $roleProductsellerID) {
          //   $role = "Productseller";
          // }
          // elseif ($role == $roleAdminID) {
          //   $role = "Admin";
          // }

          session_start();
          $_SESSION["app_id"] = 'a6fd0e37-f673-4a16-b4b3-213d6d734b91';
          $_SESSION["token"] = $x_oath;
          $_SESSION["x_subj"] = $xtoken;
          $_SESSION["id"] = $userid;
          $_SESSION["username"] = $username;
          $_SESSION["email"] = $email;
          $_SESSION["role"] = $role;

          $_SESSION["loggedinToMyShop"] = true;
          $_SESSION["name"] = $name;
          $_SESSION["surname"] = $surname;
          $_SESSION["cart"]=array();
          $_SESSION["sum_of_cart"]=0;

          // Redirect user to welcome page
          header("location: welcome.php");
        }
        else {
          $errorMessage = 'Invalid username or password.'; // echo the response message you want for error 
        }

      }else {
        $errorMessage = 'Invalid username or password.';
      }

    }

} 
function validInput($email, $password){
    if(empty(trim($email)) || empty(trim($password)))
        return false;
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <title> Login page</title>
    <link rel="stylesheet" href="styles_index_signup.css">
    <link rel="icon" href="favicon.png" />
</head>

<body>
    <div class="bg">

        <div class="content"> 
            <h1 >Welcome to my store</h1>
            <?php 
                if(!empty($errorMessage)){
                    echo $errorMessage;
                }        
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="email"> <b>Email</b>  </label> <br>
                <input type="text" id="email" name="email" value="" required><br>
                
                <label for="password"> <b>Password</b>  </label> <br>
                <input type="password" id="password" name="password" value="" required><br>
                <button type="submit"> Login </button>
            </form>

            <p>
            <a href="signup.php"><b> Sign up to gain access to our store </b></a>
            </p>
        </div>
    </div>


</body>
</html>