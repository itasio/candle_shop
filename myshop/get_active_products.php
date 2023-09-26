<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}

 // Include config file for db connectivity
 require_once "config.php";

 //query to run
 $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, CATEGORY, SELLERNAME FROM products WHERE DATEOFWITHDRAWAL IS NULL";

 $result = $conn->query($sql);

    if($result){
        if($result->num_rows == 0){
            //nothing to display
            $errorMessage = "There aren't any products currently available in our site.";
        }else{
            echo "<table>";
            echo "<tr>";
                echo "<th>name</th>";
                echo "<th>product code</th>";
                echo "<th>price</th>";
                echo "<th>category</th>";
                echo "<th>sellername</th>";
                echo "<th>action</th>";
            echo "</tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr id= ". $row['ID']. ">";

                    echo "<td data-target=\"NAME\">" . $row['NAME'] . "</td>";
                    echo "<td data-target=\"PRODUCTCODE\">" . $row['PRODUCTCODE'] . "</td>";
                    echo "<td data-target=\"PRICE\">" . $row['PRICE'] . "</td>";
                    echo "<td data-target=\"CATEGORY\">" . $row['CATEGORY'] . "</td>";
                    echo "<td data-target=\"sellername\">" . $row['SELLERNAME'] . "</td>";
                    echo "<td>" ."<button data-role= \"add\" data-id= ". $row['ID']." \"> Add to cart </button> </td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        

    }else{
        $errorMessage = "Oops! Something went wrong. Please try again later.";

    }
    // Close connection
    $conn->close();


?>