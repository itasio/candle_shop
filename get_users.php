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

 // Include config file for db connectivity
 require_once "config.php";

 //query to run
 $sql =" SELECT ID, NAME, SURNAME, USERNAME, EMAIL, ROLE, CONFIRMED FROM users";

 $result = $conn->query($sql);

    if($result){
            echo "<table>";
            echo "<tr>";
                echo "<th>name</th>";
                echo "<th>surname</th>";
                echo "<th>username</th>";
                echo "<th>email</th>";
                echo "<th>role</th>";
                echo "<th>confirmed</th>";
                echo "<th>edit</th>";
                echo "<th>delete</th>";
            echo "</tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr id= ". $row['ID']. ">";

                    echo "<td data-target=\"NAME\">" . $row['NAME'] . "</td>";
                    echo "<td data-target=\"SURNAME\">" . $row['SURNAME'] . "</td>";
                    echo "<td data-target=\"USERNAME\">" . $row['USERNAME'] . "</td>";
                    echo "<td data-target=\"EMAIL\">" . $row['EMAIL'] . "</td>";
                    echo "<td data-target=\"ROLE\">" . $row['ROLE'] . "</td>";
                    if ($row['CONFIRMED'] == 1) {
                        echo "<td data-target=\"CONFIRMED\"> TRUE </td>";
                    }else {
                        echo "<td data-target=\"CONFIRMED\"> FALSE </td>";
                    }
                    echo "<td>" ."<button data-role= \"edit\" data-id= ". $row['ID']." \"> Edit </button> </td>";
                    echo "<td>" ."<button data-role= \"delete\" data-id= ". $row['ID']." \"> Delete </button> </td>";
                echo "</tr>";
            }
            echo "</table>";
        
        

    }else{
        $errorMessage = "Oops! Something went wrong. Please try again later.";

    }
    // Close connection
    $conn->close();


?>