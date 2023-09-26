<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
define("all", "all");
define("NAME", "NAME");
define("SELLERNAME", "SELLERNAME");
define("CATEGORY", "CATEGORY");
define("PRICE", "PRICE");
define("DATEOFWITHDRAWAL", "DATEOFWITHDRAWAL");
$param = $_POST['criterion'];
// Include config file for db connectivity
 require_once "config.php";
if($param == all){
    //search all products
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
}elseif($param == NAME){
    //search based on product name
    $name = $_POST["name"];
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products
    WHERE NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
}elseif($param == SELLERNAME){
    $sellername = $_POST['sellers'];
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products
    WHERE SELLERNAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sellername);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    //search based on seller name
}elseif($param == CATEGORY){
    $category = $_POST['categories'];
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products 
    WHERE CATEGORY = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    //search based on category
}elseif($param == PRICE){
    $min = $_POST['minimum'];
    $max = $_POST['maximum'];
    if($min > $max){
        echo "Minimum price can't be greater than maximum price";
        exit;
    }
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products
    WHERE PRICE >= ? AND PRICE <= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $min, $max);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    //search based on price
}elseif($param == DATEOFWITHDRAWAL){
    //search based on dateofwithdrawal
    //show products that have withdrawed later from the selected date or they are available
    $date = $_POST['date'];
    $sql =" SELECT ID, NAME, PRODUCTCODE, PRICE, DATEOFWITHDRAWAL, CATEGORY, SELLERNAME 
    FROM products
    WHERE DATEOFWITHDRAWAL IS NULL OR DATEOFWITHDRAWAL > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
}else{
    echo "Oops! Something went wrong. Please try again later.";
}


    if($result){
        if($result->num_rows == 0){
            //nothing to display
            echo  "There aren't any products currently available in our site.";
        }else{
            echo "<table>";
            echo "<tr>";
                echo "<th>name</th>";
                echo "<th>product code</th>";
                echo "<th>price</th>";
                echo "<th>category</th>";
                echo "<th>sellername</th>";
                echo "<th>status</th>";
                //echo "<th></th>";
                echo "<th>action</th>";
            echo "</tr>";

            
            while ($row = $result->fetch_assoc()) {
                echo "<tr id= ". $row['ID']. ">";

                    echo "<td data-target=\"NAME\">" . $row['NAME'] . "</td>";
                    echo "<td data-target=\"PRODUCTCODE\">" . $row['PRODUCTCODE'] . "</td>";
                    echo "<td data-target=\"PRICE\">" . $row['PRICE'] . "</td>";
                    echo "<td data-target=\"CATEGORY\">" . $row['CATEGORY'] . "</td>";
                    echo "<td data-target=\"SELLERNAME\">" . $row['SELLERNAME'] . "</td>";
                    if($row['DATEOFWITHDRAWAL'] === null){
                        echo "<td data-target=\"DATEOFWITHDRAWAL\"> available </td>";
                        echo "<td>" ."<button data-role= \"add\" data-id= ". $row['ID']." \"> Add to cart </button> </td>";
                    }else{
                        echo "<td data-target=\"DATEOFWITHDRAWAL\"> unavailable </td>";
                    }
                echo "</tr>";
            }
            echo "</table>";
        }
    }else{
        echo "Oops! Something went wrong. Please try again later.";

    }
    // Close connection
    $stmt->close();


?>