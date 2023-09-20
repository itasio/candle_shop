<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}elseif($_SESSION["role"] !="PRODUCTSELLER"){
    header("location: access_denied.php");
    exit;
}

 // Include config file for db connectivity
 require_once "config.php";

 //query to run
 $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE,DATEOFWITHDRAWAL, CATEGORY 
 FROM products WHERE SELLERNAME = ? AND DATEOFWITHDRAWAL IS NOT NULL" ;

 if($stmt = $conn->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $_SESSION["username"]);      
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Store result
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            //nothing to display
            $errorMessage = "You have not any products currently available in our site. You can a
                                add one from our menu.";
        }else{
            echo "<table>";
            echo "<tr>";
                echo "<th>name</th>";
                echo "<th>product code</th>";
                echo "<th>price</th>";
                echo "<th>category</th>";
                echo "<th>withdrawal date</th>";
            echo "</tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                    echo "<td>" . $row['NAME'] . "</td>";
                    echo "<td>" . $row['PRODUCTCODE'] . "</td>";
                    echo "<td>" . $row['PRICE'] . "</td>";
                    echo "<td>" . $row['CATEGORY'] . "</td>";
                    echo "<td>" . $row['DATEOFWITHDRAWAL'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        

    }else{
        $errorMessage = "Oops! Something went wrong. Please try again later.";

    }
    // Close statement
    $stmt->close();
 }

?>