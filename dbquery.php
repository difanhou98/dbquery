<!-- 
    IAT 352 Assignment 3
    Author: Difan Hou
    Date: 27/10/22
 -->
<!DOCTYPE html>
<?php
    require("db.php");

    $connection = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
    

    // test connection
    if(mysqli_connect_errno()) {
        die(mysqli_connect_error()) ;
    }
    // specify the query and receive results
    $order_number_query = "SELECT orderNumber FROM orders";
    $order_number_result = mysqli_query($connection, $order_number_query);
    // test if query fails
    if (!$order_number_result){
        die("query failed");
    }

    // test the length of returned data, store data in each row in an array
    if (mysqli_num_rows($order_number_result) != 0){
        while ($row = mysqli_fetch_assoc($order_number_result))
        $order_number[] = $row['orderNumber'];
    }

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>DB Query</title>
</head>
<body>
    <h1>Query</h1>  
    <div class="container">
        <!-- parameter selection -->
        <div class="order-param">
            <p class="title">Select Order Parameters</p>
            <!-- set method and action to submit forms to the same page -->
            <form action="" method="POST">
                <label for="order-id">Order Number: </label>
                <select id="order-id" name="order_id">'
                <!-- iterate through $order_number[] to display all order number in the drop-down menu -->
                <?php 
                    foreach($order_number as $orderid){
                        $selected = (strcmp($orderid, $_POST['order_id']) == 0 
                        || strcmp("N/A", $_POST['order_id']) == 0 
                        ? "selected" 
                        : "");
                        echo '<option value=' . $orderid . " " . $selected . '>' . $orderid . '</option>';
                    }
                    // user can select N/A to indicate th system order date will be used for query process instead of order number, however, there is a display bug associated with N/A option. Sometimes the value for order_id will change to N/A even when the user has selected a specific order_id. The generated table will not be affected by this bug.
                    echo '<option value=' . "N/A" . " " . $selected . '>' . "N/A" . '</option>';
                    echo '</select>';
                ?>
                <label for="order-id"> or</label>
                
                <p>Order Date (YYYY-MM-DD)</p>
                <!-- Start Date -->
                <label for="start-date">From: </label>
                <!-- php statement to save the value the user entered after submitting -->
                <input type="text" id="start-date" name="start_date" value="<?php if (isset($_POST['start_date'])) echo $_POST['start_date'];?>">
                <!-- End Date -->
                <label for="end-date">to: </label>
                <input type="text" id="end-date" name="end_date"  value="<?php if (isset($_POST['end_date'])) echo $_POST['end_date'];?>">
                <input type="submit" value="Search" name="submit">
        </div>
        <!-- column display selection -->
        
        <p class="title">Columns to Display </p>
        <!-- Order Number -->
        <div class="display-param">
            <div class="item">
                <input type="checkbox" id="order-number" value="orders.orderNumber" name="display_list[]"  
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.orderNnumber", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="order-number">Order Number</label>
                <!-- search the display_list[] and match user selection to exist order number for display after submitting -->
                
            </div>
                <!-- Order Date -->
            <div class="item">
                <input type="checkbox" id="order-date" value="orders.orderDate" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.orderDate", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="order-date">Order Date</label>
                
            </div>
                <!-- Shipped Date -->
            <div class="item">
                <input type="checkbox" id="shipped_date" value="orders.shippedDate" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.shippedDate", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="shipped-date">Shipped Date</label>
                
            </div>
                <!-- Product Name -->
            <div class="item">
                <input type="checkbox" id="product-name" value="products.productName" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("products.productName", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="product-name">Product Name</label>
                
            </div>
                <!-- Product Description -->
            <div class="item">
                <input type="checkbox" id="product-description" value="products.productDescription" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("products.productDescription", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="product-description">Product Description</label>
                
            </div>
                <!-- Quantity Ordered -->
            <div class="item">
                <input type="checkbox" id="quantity-ordered" value="orderdetails.quantityOrdered" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orderdetails.quantityOrdered", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="quantity-ordered">Quantity Ordered</label>
                
            </div>
                <!-- Price Each -->
            <div class="item">
                <input type="checkbox" id="price-each" value="orderdetails.priceEach" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orderdetails.priceEach", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <label for="price-each">Price Each</label>
                
            </div>
            </form>
        </div>
    </div>
</body>
<!-- table display section -->
</html>
<?php
if (isset($_POST['submit'])){
    if (!empty($_POST['display_list'])){
        //connect the elements in display_list array with ",".
        $selected_column = implode(",", $_POST['display_list']);
        // initialize variable for holding join statement
        $selected_join = "";
        // initialize variable for holding query statement
        $selected_query = "";
        // echo "<p>display list set</p>";

        //check if elements in selected_column contains columns from table other than orders (orderDetail/products) and use proper join statement
        if (str_contains($selected_column, 'orderdetails') && str_contains($selected_column, 'products')){
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber INNER JOIN products ON orderdetails.productCode = products.productCode";
        }
        else if (str_contains($selected_column, 'orderdetails')) {
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber";
        }
        //since table order and products does not have direct relationship connected by foreign keys, orderDetail table needed to be joined to avoid errors.
        else if (str_contains($selected_column, 'products')) {
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber INNER JOIN products ON orderdetails.productCode = products.productCode";
        }
        //for testing purpose
        // if (isset($_POST['order_id']) && !empty($_POST['order_id'])){
        // echo '<p>order id set!</p>';
        // }
        // else {
        //     echo '<p>orderid not set!</p>';
        //     exit;
        // }

        
        
        if (isset($_POST['order_id']) && "N/A" != trim($_POST['order_id'])){
            
            // prepare query statement
            $selected_query = "SELECT ".$selected_column. " FROM ". "orders" . $selected_join. " WHERE orders.orderNumber = " . $_POST['order_id'];
            // echo $_POST['order_id'];
            // echo $selected_query;
            $selected_result = mysqli_query($connection, $selected_query);
            echo "<p class='title'>";
            echo "SQL Query</p>";
            echo "<p class='query-result'>"; 
            echo "$selected_query";
            echo "</p>";
            // if (!$selected_result && $_POST['order-id'] != "N/A"){
            //     die("Database query failed.");
            // }

            if(mysqli_num_rows($selected_result) != 0){
                // trimColumns[] will store all the names of columns with prefix trimmed off. For example, orders.orderNumber will be stored as orderNumber in trimColumns, praparing for table display later.
                $columns = [];
                $trimColumns = [];
                echo    "<table>
                        <tr>";
                // iterate through display_list and trim off excessive string based on the pos of "."
                for ($x = 0; $x < count($_POST['display_list']); $x++){
                    array_push($columns,$_POST['display_list'][$x]);
                    $columnName = substr($_POST['display_list'][$x], strpos($_POST['display_list'][$x], ".")+1);
                    echo "<td>" . $columnName . "</td>";
                }
                echo "</tr>";
                //store value into trimColumns
                foreach ($columns as $columnItem){
                    $pos = strpos($columnItem, ".");
                    array_push($trimColumns,substr($columnItem, $pos+1));
        
                }
                //print out trim columns
                // echo "<pre>";
                // var_dump($trimColumns);
                // echo "</pre>";

                //put values of each row in <td></td> cell, wrapped with <tr></tr>.
                while($row= mysqli_fetch_assoc($selected_result)) {
                    echo "<tr>";
                    for ($x = 0; $x < count($trimColumns); $x++){
                        echo "<td>" . $row[$trimColumns[$x]] . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";             
            }
            else {
                echo "no entry found.";
            }
            
        }
        
        else if (isset($_POST['start_date']) && isset($_POST['end_date'])){
            $selected_query = "SELECT ".$selected_column. " FROM ". "orders" . $selected_join. " WHERE orders.orderDate BETWEEN " ."\"" . $_POST['start_date'] . "\"". " AND " . "\"". $_POST['end_date']. "\"";
            $selected_result = mysqli_query($connection, $selected_query);
            
            echo "<p class='title'>";
            echo "SQL Query</p>";
            echo "<p class='query-result'>"; 
            echo "$selected_query";
            echo "</p>";
            if (!$selected_result){
                die("Database query failed.");
            }

            if(mysqli_num_rows($selected_result) != 0){
                
                $columns = [];
                $trimColumns = [];
                echo    "<table>
                        <tr>";
                for ($x = 0; $x < count($_POST['display_list']); $x++){
                    array_push($columns,$_POST['display_list'][$x]);
                    $columnName = substr($_POST['display_list'][$x], strpos($_POST['display_list'][$x], ".")+1);
                    echo "<td>" . $columnName . "</td>";
                }
                echo "</tr>";
                
                foreach ($columns as $columnItem){
                    $pos = strpos($columnItem, ".");
                    array_push($trimColumns,substr($columnItem, $pos+1));
        
                }
                while($row= mysqli_fetch_assoc($selected_result)) {
                    echo "<tr>";
                    for ($x = 0; $x < count($trimColumns); $x++){
                        echo "<td>" . $row[$trimColumns[$x]] . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";             
            }
            else {
                echo "no entry found.";
            }
        }
        else {
            echo 'Please specify order number or order date to start the query process!';
            exit;
        }
    }
    else {
        echo "<p>display list not set</p>";
        exit;
    }

    //free the result and close mysqli connection
    mysqli_free_result($selected_result);
    mysqli_close($connection);

    
} 
?>