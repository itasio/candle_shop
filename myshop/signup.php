<?php

$msg = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "headers_to_array.php";
    // if(isset($_POST["name"]) && isset($_POST["surname"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["email"])){
    if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["email"])){
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $role = $_POST["role"];
        if(!validInput($name, $surname, $email, $username, $password)){
            $msg= 'You cannot submit blank fields';
        }else{
            // "error": {
            //     "message": "Email already used",
            //     "code": 409,
            //     "title": "Conflict"
            // }

            // "user": {
            //     "id": "be9b8b35-6939-4524-8b1a-e27ff0a0036d",
            //     "image": "default",
            //     "gravatar": false,
            //     "enabled": true,
            //     "admin": false,
            //     "starters_tour_ended": false,
            //     "eidas_id": null,
            //     "username": "seller2",
            //     "email": "seller2@test.com",
            //     "salt": "f1598d14bc4b834c",
            //     "date_password": "2022-12-15T22:12:26.484Z"
            // }

            /***************** ACQUIRE X-Auth-Token *****************/

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

            /********************** CREATE USER *********************/

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/users");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_POST, TRUE);

            curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"user\": {
                \"username\": \"".$username."\",
                \"email\": \"".$email."\",
                \"password\": \"".$password."\",
                \"description\": \"".$name." ".$surname."\"
            }
            }");

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-Auth-token: ".$xtoken.""
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            /******************** GET THE CREATED USER'S ID **********************/

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
            //echo $response;
            $result = json_decode($response, true);

            foreach($result as $key){
                foreach($key as $doc){

                    if($doc['username'] == $username && $doc['email'] == $email){
                        $userid = $doc['id'];
                    }

                }
            }

            /******************** ASSIGN A ROLE TO A USER **********************/
            $appID = "4075a6c5-551b-459e-bb17-d6c0acd905c4";
            $roleUserID = "6a80b3ad-b458-48f2-a223-fd8ae85c098d";
            $roleProductsellerID = "fd2c47a6-33ed-47c7-a549-892c1049d006";
            $roleAdminID = "bef9ae28-26bf-4380-906a-b5dc1bccb43d";
            
            if ($role == "USER") {
                $rolechoiceID =  $roleUserID;
            }elseif ($role == "PRODUCTSELLER") {
                $rolechoiceID = $roleProductsellerID;
            }elseif ($role == "ADMIN") {
                $rolechoiceID = $roleAdminID;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://keyrock:3000/v1/applications/".$appID."/users/".$userid."/roles/".$rolechoiceID."");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-Auth-token: ".$xtoken.""
            ));
            $response = curl_exec($ch);

            curl_close($ch);
            if($response)
                $msg= 'Congrats! You have been signed up! Return to login page to sign in.';
            else
                $msg = 'An error occured. Try again later.';
            }
    }

} 

function validInput($name, $surname, $email, $username, $password){
    if(empty(trim($name)) || empty(trim($surname)) ||empty(trim($email)) || empty(trim($username)) || empty(trim($password)))
        return false;
    return true;
}  
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset = "UTF-8">
    <title> Signup page</title>
    <link rel="stylesheet" href="styles_index_signup.css">
</head>

<body>
    <div class="bg">

        <div class="content"> 
            <div class="info">
                <h1>Welcome to my store</h1>
                <h2>Please fill your personal info to complete sign up process</h2>
                <?php echo $msg;?>
            </div>

            <div class="form">            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post" id = "form1">

                <label for="name"> <b>Name</b> </label> <br>
                <input type="text"  name="name"id="name" required> <br>
            
                <label for="surname"> <b>Surname</b> </label> <br>
                <input type="text"  name="surname"id="surname" required> <br>


                <label for="email"> <b>Email</b> </label> <br>
                <input type="text"  name="email"id="email" required> <br>
            
                <label for="username"> <b>Username</b> </label> <br>
                <input type="text" name="username" id="username" required> <br>
            
                <label for="password"> <b>Password</b> </label> <br>
                <input type="password" name="password" id="password" required> <br>

                <label for="user"> <b>User</b> </label>
                <input type="radio" name="role" id="user" value="USER" required> <br>
    
                <label for="pSeller"> <b>Product seller</b> </label>
                <input type="radio" name="role" id="pSeller" value="PRODUCTSELLER" required> <br>
                
                <label for="admin"> <b>Administrator</b> </label>
                <input type="radio" name="role" id="admin" value="ADMIN" required> <br>

                <button type="submit">Sign Up</button>
                
            
            </form>
            <a href="index.php" id="linkIndex"> <b>Return to login page</b></a>
            </div>
        </div>
    </div>
</body>
</html>



