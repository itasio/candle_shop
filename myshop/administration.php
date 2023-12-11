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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <link rel="stylesheet" href="styles_main_site.css">
    <link rel="icon" href="favicon.png" />
    <title> Administration page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>

<body onload="getUsers()">
    <div class="bg">
        <div class = "container">
            <div class = "navbar">
                <button class="dropdownButton"> Menu</button>
                <div class="dropdown-content">
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart</a>
                    <a href="seller.php">Seller (only for sellers)</a>
                    <a href="administration.php">Admin (only for admins)</a>
                </div>
                <input type="text" placeholder="Search.." class="searchbar">
                <div class="usr">
                    <?php echo htmlspecialchars($_SESSION["name"]). " " .htmlspecialchars($_SESSION["surname"]). " (".htmlspecialchars($_SESSION["role"]). ")" ; ?>
                </div>
                <a href="logout.php">Sign Out of Your Account</a>
                
            </div>


        </div>

        <div class="contentTable">
            <!-- <h2>From this page you can access and modify users info</h2> -->
            <div class="data_table_form">
                <div class= "table_form">  </div>
            </div>
            <!-- form for editing the users info -->
            <div class="form-popup" id="editForm">
                <form action="" class="form-container" id="edit_form">
                    <h1>Edit data</h1>

                    <label for="name"><b>Name</b></label>
                    <input id="name" type="text" name="name" required class = "inputFields"> <br>

                    <label for="surname"><b>Surname</b></label>
                    <input id="surname" type="text" name="surname" required class = "inputFields"> <br>

                    <label for="user_name"><b>Username</b></label>
                    <input id="user_name" type="text" name="user_name" required class = "inputFields"> <br>

                    <label for="email"><b>Email</b></label>
                    <input id="email" type="text" name="email" required class = "inputFields"> <br>
                    
                    <label for="user"> <b>User</b> </label>
                    <input type="radio" data-info="role" name="role" id="user" value="USER" required> <br>
        
                    <label for="pSeller"> <b>Product seller</b> </label>
                    <input type="radio" data-info="role" name="role" id="pSeller" value="PRODUCTSELLER" required> <br>
                    
                    <!-- <label for="admin"> <b>Administrator</b> </label>
                    <input type="radio" data-info="role" name="role" id="admin" value="ADMIN" required> <br> -->

                    <!-- <label for="confirmed"> <b>Confirmed</b> </label>
                    <input type="checkbox" name="confirmed" id="confirmed"> <br> -->


                    <input id="uID" type ="hidden" class = "inputFields">

                    <button type="submit" class="btn" data-role="submit">Submit</button>
                    <button type="button" class="btn cancel" data-role="close">Cancel</button>
                </form>
            </div>

            
        </div>
            

    </div>
    <script>
        var frm = document.getElementsByClassName("table_form")[0];
        
        function getUsers(){
            frm.innerHTML="";
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    frm.innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "get_users.php", true);
            xhttp.send();
        }

        $(document).ready(function(){

        //delete choice made
        $(document).on('click', 'button[data-role=delete]', function(){
            var param = $(this).data('id');
            $.ajax({
                url     : "delete_user.php",
                method  : "POST",
                data    : {identity : param},
                success : function(response){
                        //now reloading the data from the database
                        getUsers();
                        window.alert(response);
                },
                error   : function(xhr){
                    window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
            

        })

        //edit choice made
        //open popup
        $(document).on('click', 'button[data-role=edit]', function(){
            document.getElementById("editForm").style.display = "block";
            //get the values of the table data
            var id = $(this).data('id');
            var name = $('#'+id).children('td[data-target=NAME]').text();
            var surname = $('#'+id).children('td[data-target=SURNAME]').text();
            var username = $('#'+id).children('td[data-target=USERNAME]').text();
            var email = $('#'+id).children('td[data-target=EMAIL]').text();
            //append values of table into input fields
            $('#name').val(name);
            $('#surname').val(surname);
            $('#user_name').val(username);
            $('#email').val(email);
            $('#uID').val(id);

        })

        //submit the changes made in the users info
        $("form").submit(function (event) {

            event.preventDefault();
            //get the data from the input fields
            var name = $('#name').val();
            var surname = $('#surname').val();
            var username = $('#user_name').val();
            var email =  $('#email').val();
            var role =  $("input[name='role']:checked").val();
            // if ($('#confirmed').is(":checked")){
            //     var confirmed = "1";
            // }else{
            //     var confirmed = "0";
            // }            
            var uID =  $('#uID').val();
            document.getElementById("editForm").style.display = "none";
            $.ajax({
                url     : "edit_user.php",
                method  : "POST",
                data    : {name : name, surname : surname, username : username,
                    email : email, role : role, uID : uID},
                success : function(response){
                        //now reloading the data from the database
                        getUsers();
                        window.alert(response);
                },
                error   : function(xhr){
                    window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });


        });

        //close the popup
        $(document).on('click', 'button[data-role=close]', function(){
            document.getElementById("editForm").style.display = "none";
        })

        })
    </script>
    </body>


</html>