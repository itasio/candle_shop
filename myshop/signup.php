<?php

$msg = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Include config file for db connectivity
    require_once "config.php";

    if(isset($_POST["name"]) && isset($_POST["surname"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["email"])){

        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $role = $_POST["role"];

        $dup = checkForDuplicates($conn, $username, $email);
       if( $dup != ""){
            $msg= 'This ' . $dup . ' is already used. Please choose another.';
       }elseif(!validInput($name, $surname, $email, $username, $password)){
        $msg= 'You cannot submit blank fields';
        }
       
       
       else{

            $param_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (NAME, SURNAME, USERNAME, PASSWORD, EMAIL, ROLE)
            VALUES ('$name', '$surname', '$username', '$param_password', '$email', '$role')";
        

            $result = $conn->query($sql);
            
            if($result){
                $msg ='<style>#form1 { display:none;}</style>';
                $msg ='<style>#linkIndex { display:block;}</style>';
                $msg= 'You have successfully sent your registration form.
                You will be able to enter our website as soon as the admin accepts your data.';

            } else{
                $msg= "An unexpected error occured. Please try again later.";
                $msg= "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }



 $conn->close();

} 

function validInput($name, $surname, $email, $username, $password){
    if(empty(trim($name)) || empty(trim($surname)) ||empty(trim($email)) || empty(trim($username)) || empty(trim($password)))
        return false;
    return true;
}

function checkForDuplicates($conn, $username, $email){
    //check if this username is already used

    $sql = "Select * from users where USERNAME='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //username is used already
        return "username";
    }
    $sql = "Select * from users where EMAIL='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //email is used already
        return "email";
    }
    return "";
}   
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset = "UTF-8">
    <title> Login page</title>
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
            <a href="index.php" id="linkIndex">Return to login page</a>
            </div>
        </div>
    </div>

    <!-- <div class="info"> -->

 <!-- </div> -->
</body>
</html>



