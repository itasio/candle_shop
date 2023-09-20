<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedinToMyShop"]) && $_SESSION["loggedinToMyShop"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
// Define variables and initialize with empty values
$errorMessage = '';
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];
    // Check if username or password is empty
    if(!validInput($username, $password)){
        $errorMessage =  'You cannot submit blank fields';
    }else{

    //credentials valid
        $username = trim($username);
        $password = trim($password);
        // Prepare a select statement
        $sql = "SELECT NAME, SURNAME, PASSWORD, CONFIRMED, ROLE FROM users WHERE USERNAME = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $username);      
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $result = $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($name, $surname, $hashed_password, $confirmed, $role);
                    if($stmt->fetch()){
                        if(!$confirmed){
                            $errorMessage = "You have not been approved yet from the adminisrator. Try again later.";
                        }
                        elseif(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedinToMyShop"] = true;
                            $_SESSION["username"] = $username;
                            $_SESSION["name"] = $name;
                            $_SESSION["surname"] = $surname;
                            $_SESSION["role"] = $role;
                            $_SESSION["cart"]=array();
                            $_SESSION["sum_of_cart"]=0;

                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $errorMessage = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $errorMessage = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    
    }
    // Close connection
    $conn->close();    
}

function validInput($username, $password){
    if(empty(trim($username)) || empty(trim($password)))
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
                <label for="username"> <b>Username</b>  </label> <br>
                <input type="text" id="username" name="username" value="" required><br>
                
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