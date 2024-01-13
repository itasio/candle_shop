<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedinToMyShop"]) || $_SESSION["loggedinToMyShop"] !== true){
    header("location: index.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <link rel="icon" href="favicon.png" />
    <link rel="stylesheet" href="styles_main_site.css">
    
    <title> My orders</title>
</head>

<body onload="draw_orders()">
    <div class="bg">
        <?php include_once "menu.php";?>
        <div class="contentTable">
            <h2>Order history</h1>
            <hr>
            <div id="data_table">
                <div id= "table1"> </div>
            </div>
        </div>

    </div>
    <script>
        var frm = document.getElementById("table1");

        function draw_orders(){
            frm.innerHTML = "";
            xhttp = new XMLHttpRequest();
            xhttp.open("GET", "get_orders.php", true);
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    frm.innerHTML = this.responseText;

                }
            };
            xhttp.send();
        }
    </script>
</body>
</html>

