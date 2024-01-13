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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset = "UTF-8">
    <link rel="stylesheet" href="styles_main_site.css">
    <link rel="icon" href="favicon.png" />
    <title> Cart</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>

<body onload="draw_cart()">
    <div class="bg">
        <?php include_once "menu.php";?>
        <div class="contentTable">
            <div id="data_table">
                    <div id= "table1"> </div>
            </div>
        </div>
        

    </div>

    <script>

        var frm = document.getElementById("table1");
        var sc =  document.getElementById("sum_container");

        function draw_cart(){
            frm.innerHTML = "";
            xhttp = new XMLHttpRequest();
            xhttp.open("GET", "get_cart.php", true);
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    frm.innerHTML = this.responseText;

                }
            };
            xhttp.send();
        }
        
        $(document).ready(function(){
 
            //remove from cart choice made
            $(document).on('click', 'button[data-role=remove]', function(){
                var id = $(this).data('id');
                $.ajax({
                    url     : "edit_cart.php",
                    method  : "POST",
                    data    : {"op" : "remove", "prod_id" : id},
                    success : function(response){
                            window.alert(response);
                            draw_cart();
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            })

                //remove from cart choice made
            $(document).on('click', 'button[data-role=clear]', function(){
                $.ajax({
                    url     : "edit_cart.php",
                    method  : "POST",
                    data    : {"op" : "clear"},
                    success : function(response){
                            window.alert(response);
                            draw_cart();
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            })

            $(document).on('click', 'button[data-role=place_order]', function(){
                $.ajax({
                    url     : "edit_cart.php",
                    method  : "POST",
                    data    : {"op" : "order"},
                    success : function(response){
                            window.alert(response);
                            draw_cart();
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            })


            
        })
    </script>
</body>
</html>