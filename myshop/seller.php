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

$msg="";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    

    // Include config file for db connectivity
    require_once "config.php";

    if(isset($_POST["name"]) && isset($_POST["prod_code"]) && isset($_POST["prod_price"]) && isset($_POST["prod_category"]))
    {

        $name = $_POST["name"];
        $prod_code = $_POST["prod_code"];
        $prod_price = $_POST["prod_price"];
        $prod_category = $_POST["prod_category"];
        $username = $_SESSION["username"];
        if(!validInput($name, $prod_code, $prod_price, $prod_category)){
            $msg =  'You cannot submit blank fields';
        }
       
       
       else{

            //check if user tries to insert a product that has been deleted in the past
            // if so make date of withdrawal null and update info, otherwise make insertion
            $sql = "SELECT * FROM products where PRODUCTCODE = '$prod_code' and CATEGORY = '$prod_category' and DATEOFWITHDRAWAL IS NOT NULL";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                //update 
                $sql ="UPDATE products SET DATEOFWITHDRAWAL = NULL, NAME = '$name', PRICE = '$prod_price' WHERE PRODUCTCODE = '$prod_code' and CATEGORY = '$prod_category'";
                $result = $conn->query($sql);
                if($result){
                        
                    $msg = 'The deleted product is available again with updated info';
    
                } else{
                    $msg =  "An unexpected error occured. Please try again later.". "Error: " . "<br>" . $conn->error;
                }
            }else{

                $sql = "INSERT INTO products(NAME, PRODUCTCODE, PRICE, SELLERNAME, CATEGORY) 
                VALUES ('$name', '$prod_code', '$prod_price', '$username','$prod_category')";
                

                try {
                    $result = $conn->query($sql);
                    
                    if($result){
                        
                        $msg = 'You have successfully added a new product.';
        
                    } else{
                        $msg =  "An unexpected error occured. Please try again later." . "<br>" . "Important notice: Products with the same code can't belong in the same category.";
                    }
                } catch (\Throwable $th) {
                    $msg =  "An unexpected error occured. You cannot insert the same product twice.";
                }
            
            }
        }
    }



 $conn->close();

} 

function validInput($name, $prod_code, $prod_price, $prod_category){
    if(empty(trim($name)) || empty(trim($prod_code)) ||empty(trim($prod_price)) || empty(trim($prod_category)))
        return false;
    return true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"s>
    <meta charset = "UTF-8">
    <link rel="stylesheet" href="styles_main_site.css">
    <title> Seller page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>

<body>
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
            <h2>From this page you can access and modify your products</h2>

            <form action=""> 
            <select name="option" onchange="showOptions(this.value)">
            <option value="">Select an action:</option>
            <option value="Show">Show my products info</option>
            <option value="Show_deleted">Show my deleted products info</option>
            <option value="Insert">Insert a new product</option>
            </select>
            </form>
            <br>
            <div class="data_table_form">
                <div class="table_form">  <div> <b><?php echo $msg?> </b></div> </div>
            </div>
                <!-- form for editing the products info -->
            <div class="form-popup" id="edit_form" style="display: none;">
                <form action="edit_product.php" class="form-container">
                    <h1>Edit data</h1>

                    <label for="product_name"><b>Name</b></label>
                    <input id="product_name" type="text" name="product_name" required class = "inputFields"> <br>

                    <label for="product_code"><b>Product code</b></label>
                    <input id="product_code" type="text" name="product_code" required class = "inputFields"> <br>

                    <label for="product_price"><b>Product price</b></label>
                    <input id="product_price" type="number" name="product_price" required class = "inputFields" min="0.01" max="10000" step="0.01"> <br>

                    <label for="product_category"><b>Product category</b></label>
                    <input id="product_category" type="text" name="product_category" required class = "inputFields"> <br>

                    <input id="pID" type ="hidden" class = "inputFields">

                    <button type="submit" class="btn" data-role="submit">Submit</button>
                    <button type="button" class="btn cancel" data-role="close">Cancel</button>
                </form>
            </div>

            
            
        </div>
            

    </div>


    <script>
        var frm = document.getElementsByClassName("table_form")[0];
        var cnt = document.getElementsByClassName("contentTable")[0];
        function showOptions(str) {
            var xhttp;    
            if (str == "") {
                document.getElementsByClassName("table_form")[0].innerHTML = "";
                document.getElementById("edit_form").style.display = "none";
                let ins_form = document.getElementById('insert_form');
                if(ins_form != null){
                    ins_form.remove();
                }
                return;
            }else if(str == "Insert"){
                document.getElementsByClassName("table_form")[0].innerHTML = "";
                document.getElementById("edit_form").style.display = "none";
                //show the Insert form
                // Create a break line element
                var br = document.createElement("br");
                var form = document.createElement("form");
                form.setAttribute("method", "post");
                form.setAttribute("style", "position:relative");
                form.setAttribute("id", "insert_form");
                //data will be sent to the page itself
                form.setAttribute("action", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>");
                
                //label for name
                var label_name = document.createElement("label");
                label_name.setAttribute("for","prod_name");
                label_name.innerHTML = "Product name";
                //append label to the form element
                form.appendChild(label_name);
                form.appendChild(br.cloneNode());

                //input for name
                var inp = document.createElement("input");
                inp.setAttribute("type", "text");
                inp.setAttribute("name", "name");
                inp.setAttribute("id", "prod_name");
                inp.required = true;
                //append name input to the form element
                form.appendChild(inp);
                form.appendChild(br.cloneNode());
                
                //label for product code
                var label_name = document.createElement("label");
                label_name.setAttribute("for","prod_code");
                label_name.innerHTML = "Product code";
                //append label to the form element
                form.appendChild(label_name);
                form.appendChild(br.cloneNode());

                //input for product code
                var inp = document.createElement("input");
                inp.setAttribute("type", "text");
                inp.setAttribute("name", "prod_code");
                inp.setAttribute("id", "prod_code");
                inp.required = true;
                //append product code input to the form element
                form.appendChild(inp);
                form.appendChild(br.cloneNode());

                //label for product price
                var label_name = document.createElement("label");
                label_name.setAttribute("for","prod_price");
                label_name.innerHTML = "Product price";
                //append label to the form element
                form.appendChild(label_name);
                form.appendChild(br.cloneNode());

                //input for product price
                var inp = document.createElement("input");
                inp.setAttribute("type", "number");
                inp.setAttribute("name", "prod_price");
                inp.setAttribute("id", "prod_price");
                inp.setAttribute("min", "0.01");
                inp.setAttribute("max", "10000");
                inp.setAttribute("step", "0.01");
                inp.required = true;
                //append product price input to the form element
                form.appendChild(inp);
                form.appendChild(br.cloneNode());

                //label for product category
                var label_name = document.createElement("label");
                label_name.setAttribute("for","prod_category");
                label_name.innerHTML = "Product category";
                //append label to the form element
                form.appendChild(label_name);
                form.appendChild(br.cloneNode());

                //input for product code
                var inp = document.createElement("input");
                inp.setAttribute("type", "text");
                inp.setAttribute("name", "prod_category");
                inp.setAttribute("id", "prod_category");
                inp.required = true;
                //append product code input to the form element
                form.appendChild(inp);
                form.appendChild(br.cloneNode());

                //submit button
                var sub = document.createElement("button");
                sub.innerHTML = "Add new product"
                sub.setAttribute("type","submit");
                //append button to the form element
                form.appendChild(sub);

                //append form to document
                cnt.append(form);
                return;



            }
            else if(str == "Show"){
                //show only this seller's products with update or delete available which are not withdrawed from the site
                frm.innerHTML = "";
                let ins_form = document.getElementById('insert_form');
                if(ins_form != null){
                    ins_form.remove();
                }
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        frm.innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "get_active_seller_products.php", true);
                xhttp.send();
                return;

            }
            else if(str == "Show_deleted"){
                //show only this seller's withdrawed products with option for making them available again
                var xhttp;
                frm.innerHTML = "";
                let ins_form = document.getElementById('insert_form');
                if(ins_form != null){
                    ins_form.remove();
                }
                document.getElementById("edit_form").style.display = "none";
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        frm.innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "get_deleted_seller_products.php", true);
                xhttp.send();
                return;
            }


        }
        

        $(document).ready(function(){

            //delete choice made
            $(document).on('click', 'button[data-role=delete]', function(){
                var param = $(this).data('id');
                $.ajax({
                    url     : "delete_product.php",
                    method  : "POST",
                    data    : {identity : param},
                    success : function(response){
                              //now reloading the data from the database
                              loadActiveProducts(frm);
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
                document.getElementById("edit_form").style.display = "block";
                //get the values of the table data
                var id = $(this).data('id');
                var name = $('#'+id).children('td[data-target=NAME]').text();
                var pcode = $('#'+id).children('td[data-target=PRODUCTCODE]').text();
                var pr = $('#'+id).children('td[data-target=PRICE]').text();
                var cat = $('#'+id).children('td[data-target=CATEGORY]').text();

                //append values of table into input fields
                $('#product_name').val(name);
                $('#product_code').val(pcode);
                $('#product_price').val(pr);
                $('#product_category').val(cat);
                $('#pID').val(id);

            })

            //submit the changes made in the products info
            $("form").submit(function (event) {

                event.preventDefault();
                //get the data from the input fields
                var name = $('#product_name').val();
                var pcode = $('#product_code').val();
                var pr = $('#product_price').val();
                var cat =  $('#product_category').val();
                var pID =  $('#pID').val();
                document.getElementById("edit_form").style.display = "none";
                $.ajax({
                    url     : "edit_product.php",
                    method  : "POST",
                    data    : {name : name, pcode : pcode, pr : pr, cat : cat, pID : pID},
                    success : function(response){
                              //now reloading the data from the database
                              loadActiveProducts(frm);
                              window.alert(response);
                    },
                    error   : function(xhr){
                        window.alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });

                
            
            });

            //close the popup
            $(document).on('click', 'button[data-role=close]', function(){
                document.getElementById("edit_form").style.display = "none";
            })

        })

        function loadActiveProducts(frm){
            frm.innerHTML = "";
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    frm.innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "get_active_seller_products.php", true);
            xhttp.send();
        }


    </script>

    
            
</body>
</html>
