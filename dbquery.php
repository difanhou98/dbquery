<!DOCTYPE html>
<?php
    require("db.php");

    $connection = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);

    // test connection
    if(mysqli_connect_errno()) {
        die(mysqli_connect_error()) ;
    }

    $order_number_query = "SELECT orderNumber FROM orders";
    $order_number_result = mysqli_query($connection, $order_number_query);
    if (!$order_number_result){
        die("query failed");
    }

    
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
            <form action="/dbquery.php" method="POST">
                <label for="order-id">Order Number: </label>
                
                <?php 
                    echo '<select id="order-id" name="order_id">';
                    foreach($order_number as $orderid){
                        echo '<option value='.$orderid.'>'.$orderid.'</option>';
                    }
                ?>
                <select name="" id=""></select>
                <label for="order-id"> or</label>
                <p>Order Date (YYYY-MM-DD)</p>
                <!-- Start Date -->
                <label for="start-date">From: </label>
                <input type="date" id="start-date" name="start_date">
                <!-- End Date -->
                <label for="end-date">to: </label>
                <input type="date" id="end-date" name="end_date">
                <input type="submit" value="Search" name="submit">
            </form>
        </div>
        <!-- column sidplay section -->
        <div class="column-display">
        <form action="/dbquery.php" method="POST">
            <p>Columns to Display </p>
            <!-- Order Number -->
            <label for="order-number">Order Number</label>
            <input type="checkbox" id="order-number" value="orderNnumber" name="display_list[]">
            <!-- Order Date -->
            <label for="order-number">Order Date</label>
            <input type="checkbox" id="order-number" value="orderDate" name="display_list[]">
            <!-- Shipped Date -->
            <label for="order-number">Shipped Date</label>
            <input type="checkbox" id="order-number" value="shippedDate" name="display_list[]">
            <!-- Product Name -->
            <label for="order-number">Product Name</label>
            <input type="checkbox" id="order-number" value="productName" name="display_list[]">
            <!-- Product Description -->
            <label for="order-number">Product Description</label>
            <input type="checkbox" id="order-number" value="productDescription" name="display_list[]">
            <!-- Quantity Ordered -->
            <label for="order-number">Quantity Ordered</label>
            <input type="checkbox" id="order-number" value="quantityOrdered" name="display_list[]">
            <!-- Price Each -->
            <label for="order-number">Price Each</label>
            <input type="checkbox" id="order-number" value="priceEach" name="display_list[]">
        </form>
        </div>
    </div>
</body>
</html>