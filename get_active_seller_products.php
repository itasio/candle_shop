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
 $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, CATEGORY 
 FROM products WHERE SELLERNAME = ? AND DATEOFWITHDRAWAL IS NULL" ;

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
                echo "<th>edit</th>";
                echo "<th>delete</th>";
            echo "</tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr id= ". $row['ID']. ">";

                    echo "<td data-target=\"NAME\">" . $row['NAME'] . "</td>";
                    echo "<td data-target=\"PRODUCTCODE\">" . $row['PRODUCTCODE'] . "</td>";
                    echo "<td data-target=\"PRICE\">" . $row['PRICE'] . "</td>";
                    echo "<td data-target=\"CATEGORY\">" . $row['CATEGORY'] . "</td>";
                    echo "<td>" ."<button data-role= \"edit\" data-id= ". $row['ID']." \"> Edit </button> </td>";
                    echo "<td>" ."<button data-role= \"delete\" data-id= ". $row['ID']." \"> Delete </button> </td>";
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