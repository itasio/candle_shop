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
    <link rel="stylesheet" href="styles_main_site.css">
    <link rel="icon" href="favicon.png" />
    <title> Products page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>

<body onload="getProducts()">
    <div class="bg">
        <?php include_once "menu.php";?>
        <div class="contentTable">
            <div class="data_table_form">
                    <div class= "table_form"> </div>
                    <!-- adding the search fields -->

            </div>
            <div id="searchContainer">
                <select name="option" onchange="showSearchfields(this.value)" style="margin-bottom: 5px;">
                    <option value="ALL">Show all:</option>
                    <option value="NAME">Search by Product name</option>
                    <option value="SELLERNAME">Search by Productseller (Username)</option>
                    <option value="CATEGORY">Search by Category</option>
                    <option value="PRICE">Search by Price</option>
                    <option value="DATEOFWITHDRAWAL">Search by Date of withdrawal</option>
                </select>
                <form id = "SearchFields" method="POST">
                
                </form>
            </div>
        </div>
        

    </div>

    <script>

        var frm = document.getElementsByClassName("table_form")[0];
        function showSearchfields(str) {
            var field = document.getElementById("SearchFields");
            var br = document.createElement("br");
            if (str == "ALL") {
                field.innerHTML = "";
                getProducts();
            }else if(str == "NAME"){
                field.innerHTML = "";
                var inp = document.createElement("input");
                inp.setAttribute("type", "text");
                inp.setAttribute("name", "name");
                inp.setAttribute("id", "prod_name");
                inp.setAttribute("placeholder", "Type the name of the product");
                //append name input
                field.appendChild(inp);
                field.appendChild(br.cloneNode());

                //create button 
                buttonCreate(field, "NAME");
                field.appendChild(br.cloneNode());
            }else if(str == "SELLERNAME"){
                field.innerHTML = "";
                //create drodown with sellers to choose from
                getSellersList(field);
            }else if(str == "CATEGORY"){
                //field.setAttribute("action","get_products.php?q="+str);
                field.innerHTML = "";
                //create drodown with categories to choose from
                getCategoriesList(field);
            }else if(str == "PRICE"){
                //label for min price
                field.innerHTML = "";
                var label_name = document.createElement("label");
                label_name.setAttribute("for","min_price");
                label_name.innerHTML = "Minimum price";
                //append label to field
                field.appendChild(label_name);

                //input for min price
                var inp = document.createElement("input");
                inp.setAttribute("type", "number");
                inp.setAttribute("name", "min_price");
                inp.setAttribute("id", "min_price");
                inp.setAttribute("min", "0");
                inp.setAttribute("max", 10000);
                inp.setAttribute("step", "0.01");
                inp.required = true;
                field.appendChild(inp);
                field.appendChild(br.cloneNode());

                //label for max price
                var label_name = document.createElement("label");
                label_name.setAttribute("for","max_price");
                label_name.innerHTML = "Maximum price";
                //append label to field
                field.appendChild(label_name);

                //input for max price
                var inp = document.createElement("input");
                inp.setAttribute("type", "number");
                inp.setAttribute("name", "max_price");
                inp.setAttribute("id", "max_price");
                inp.setAttribute("min", 0);
                inp.setAttribute("max", "10000");
                inp.setAttribute("step", "0.01");
                inp.required = true;
                field.appendChild(inp);
                field.appendChild(br.cloneNode());

                //create button 
                buttonCreate(field, "PRICE");
                field.appendChild(br.cloneNode());
            }else if(str == "DATEOFWITHDRAWAL"){
                //select date after which the products have been withdrawed
                field.innerHTML = "";
                //label for date
                var label_name = document.createElement("label");
                label_name.setAttribute("for","date");
                label_name.innerHTML = "Search prosucts that have withdrawed later from the selected date or they are available";
                //append label to field
                field.appendChild(label_name);
                field.appendChild(br.cloneNode());


                //input for date
                var inp = document.createElement("input");
                inp.setAttribute("type", "date");
                inp.setAttribute("id", "date");
                inp.setAttribute("name", "date");
                inp.setAttribute("min", "1970-01-01");
                inp.setAttribute("max", "2050-01-01");
                inp.required = true;
                field.appendChild(inp);
                field.appendChild(br.cloneNode());

                //create button 
                buttonCreate(field, "DATEOFWITHDRAWAL");
                field.appendChild(br.cloneNode());

            }
        }

        //creates a button and appends it to the field element
        function buttonCreate(field, target) {
            var but = document.createElement("button");
            but.setAttribute("name", "submit_button");
            but.setAttribute("id", "submit_button");
            but.setAttribute("data-role", "submit");
            but.setAttribute("data-target", target);
            but.innerHTML = "Search now"
            //append button
            field.appendChild(but);

        }


        //return a dropdown list of product categories to choose from in order to search based
        //on category
        function getCategoriesList(field){
            var param = "category";
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    field.innerHTML = this.responseText;
                    buttonCreate(field, "CATEGORY");
                }
            };
            xhttp.open("GET", "get_list.php?q="+param, true);
            xhttp.send();
        }
        //return a dropdown list of sellers to choose from in order to search based
        //on productseller
        function getSellersList(field) {
            var param = "productseller";
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    field.innerHTML = this.responseText;
                    buttonCreate(field, "SELLERNAME");
                }
            };
            xhttp.open("GET", "get_list.php?q="+param, true);
            xhttp.send();
        }

        function getProducts() {
            console.log(frm);
            frm.innerHTML="";
            xhttp = new XMLHttpRequest();
            xhttp.open("POST", "get_products.php", true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    frm.innerHTML = this.responseText;
                }
            };
            xhttp.send();
        }

        $(document).ready(function(){
 
            //add choice made
            $(document).on('click', 'button[data-role=add]', function(){
                var id = $(this).data('id');
                var name = $('#'+id).children('td[data-target=NAME]').text();
                var code = $('#'+id).children('td[data-target=PRODUCTCODE]').text();
                var category = $('#'+id).children('td[data-target=CATEGORY]').text();
                var sellername = $('#'+id).children('td[data-target=SELLERNAME]').text();
                var price = $('#'+id).children('td[data-target=PRICE]').text();
                var dt = new Date();
                var dt_str = dt.toDateString();
                var product = { "prod_id" : id,
                                "name": name,
                                "code": code,
                                "category": category,
                                "sellername": sellername,
                                "price": price,
                                "insertion_date": dt_str,
                                "op" : "add"
                            }
                //send product to be added to the cart
                $.ajax({
                    url     : "edit_cart.php",
                    method  : "POST",
                    data    : product,
                    success : function(response){
                            window.alert(response);
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            })

            
            $("form").submit(function (event) {

                event.preventDefault();
                var addr = "get_products.php";
                //get the data from the input fields
                if($('#submit_button').data("target") == "NAME"){
                    var name = $('#prod_name').val();                
                    var data = {name : name, criterion : "NAME"};
                }else if($('#submit_button').data("target") == "SELLERNAME"){
                    var sellername = $('#sellers').val();                
                    var data = {sellers : sellername, criterion : "SELLERNAME"};
                }
                else if($('#submit_button').data("target") == "CATEGORY"){
                    var category = $('#categories').val();                
                    var data = {categories : category, criterion : "CATEGORY"};
                }else if($('#submit_button').data("target") == "PRICE"){
                    var minPrice = $('#min_price').val();
                    var maxPrice = $('#max_price').val();           
                    var data = {minimum : minPrice, maximum : maxPrice, criterion : "PRICE"};
                }else if($('#submit_button').data("target") == "DATEOFWITHDRAWAL"){
                    var date = $('#date').val();             
                    console.log(date);   
                    var data = {date : date, criterion : "DATEOFWITHDRAWAL"};
                }

                $.ajax({
                    url     : addr,
                    method  : "POST",
                    data    : data,
                    success : function(response){
                              var field = document.getElementById("SearchFields");
                              frm.innerHTML = response;
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            });
        })

    </script>
</body>
</html>