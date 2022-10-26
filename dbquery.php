<!DOCTYPE html>
<?php
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
        <div class="order-param">
            <p>Select Order Parameters</p>
            <form action="/dbquery.php" method="POST">
                <label for="order-id">Order Number: </label>
                <input type="text" id="order-id" name="order_id" placeholder="Order ID">
                <label for="order-id"> or</label>
            </form>
        </div>

        <div class="column-display">

        </div>
    </div>
</body>
</html>