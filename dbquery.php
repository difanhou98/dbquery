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
    <title>DB Query</title>
</head>
<body>
    <h1>Query</h1>  
    <div class="container">
        <!-- parameter selection -->
        <div class="order-param">
            <p>Select Order Parameters</p>
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
                    echo '<option value=' . "N/A" . " " . $selected . '>' . "N/A" . '</option>';
                    // echo '<option value="" selected="selected"></option>';
                    echo '</select>';
                ?>
                <label for="order-id"> or</label>
                
                <p>Order Date (YYYY-MM-DD)</p>
                <!-- Start Date -->
                <label for="start-date">From: </label>
                <input type="date" id="start-date" name="start_date">
                <!-- End Date -->
                <label for="end-date">to: </label>
                <input type="date" id="end-date" name="end_date">
                <input type="submit" value="Search" name="submit">

                <p>Columns to Display </p>
                <!-- Order Number -->
                <label for="order-number">Order Number</label>
                <input type="checkbox" id="order-number" value="orders.orderNumber" name="display_list[]"  
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.orderNnumber", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Order Date -->
                <label for="order-date">Order Date</label>
                <input type="checkbox" id="order-date" value="orders.orderDate" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.orderDate", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Shipped Date -->
                <label for="shipped-date">Shipped Date</label>
                <input type="checkbox" id="shipped_date" value="orders.shippedDate" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orders.shippedDate", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Product Name -->
                <label for="product-name">Product Name</label>
                <input type="checkbox" id="product-name" value="products.productName" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("products.productName", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Product Description -->
                <label for="product-description">Product Description</label>
                <input type="checkbox" id="product-description" value="products.productDescription" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("products.productDescription", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Quantity Ordered -->
                <label for="quantity-ordered">Quantity Ordered</label>
                <input type="checkbox" id="quantity-ordered" value="orderdetails.quantityOrdered" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orderdetails.quantityOrdered", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
                <!-- Price Each -->
                <label for="price-each">Price Each</label>
                <input type="checkbox" id="price-each" value="orderdetails.priceEach" name="display_list[]"
                <?php 
                    if (!empty($_POST['display_list']) && in_array("orderdetails.priceEach", $_POST['display_list']))
                    echo 'checked="checked"';
                ?>>
            </form>
        </div>
        <!-- column sidplay section -->
    </div>
</body>
</html>
<?php
if (isset($_POST['submit'])){
    if (!empty($_POST['display_list'])){
        $selected_column = implode(",", $_POST['display_list']);
        $selected_join = "";
        $selected_query = "";
        echo "<p>display list set</p>";
        if (str_contains($selected_column, 'orderdetails') && str_contains($selected_column, 'products')){
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber INNER JOIN products ON orderdetails.productCode = products.productCode";
        }
        else if (str_contains($selected_column, 'orderdetails')) {
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber";
        }
        else if (str_contains($selected_column, 'products')) {
            $selected_join = " INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber INNER JOIN products ON orderdetails.productCode = products.productCode";
        }
        if (isset($_POST['order_id']) && !empty($_POST['order_id'])){
        
        echo '<p>orderid set!</p>';
        }
        else {
            echo '<p>orderid not set!</p>';
        }
       
        
        
        if (isset($_POST['order_id'])){
            $selected_query = "SELECT ".$selected_column. " FROM ". "orders" . $selected_join. " WHERE orders.orderNumber = " . $_POST['order_id'];
            echo $_POST['order_id'];
            echo $selected_query;
           
            $selected_result = mysqli_query($connection, $selected_query);
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
                    echo "<td>" . $_POST['display_list'][$x] . "</td>";
                }
                echo "</tr><tr>";
                
                foreach ($columns as $columnItem){
                    $pos = strpos($columnItem, ".");
                    array_push($trimColumns,substr($columnItem, $pos+1));
        
                }
                //print out trim columns
                echo "<pre>";
                var_dump($trimColumns);
                echo "</pre>";
                while($row= mysqli_fetch_assoc($selected_result)) {
                    for ($x = 0; $x < count($trimColumns); $x++){
                        echo "<td>" . $row['orderNumber'] . "</td>";
                    }
                }
                echo "  </tr>
                        </table>";             
            }
            else {
                echo "no entry found.";
            }
            // start here, trim the column
        }
        else if (isset($_POST['start_date']) && isset($_POST['end_date'])){

        }
        else {
            echo 'Please specify order number or order date to start the query process!';
            exit;
        }
    }
    else {
        echo "<p>display list not set</p>";
    }
    
} 
?>