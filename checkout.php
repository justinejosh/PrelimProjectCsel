<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>Checkout</h1>";

// Check if cart is empty
$check_cart_sql = "SELECT * FROM store_shoppertrack WHERE session_id = '".session_id()."'";
$check_cart_res = mysqli_query($mysqli, $check_cart_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($check_cart_res) < 1) {
    header("Location: seestore.php");
    exit;
}

if (isset($_POST['submit'])) {
    // 1. Validate and Clean Data
    $safe_name = mysqli_real_escape_string($mysqli, $_POST['order_name']);
    $safe_email = mysqli_real_escape_string($mysqli, $_POST['order_email']);
    $safe_address = mysqli_real_escape_string($mysqli, $_POST['order_address']);
    $safe_city = mysqli_real_escape_string($mysqli, $_POST['order_city']);
    $safe_zip = mysqli_real_escape_string($mysqli, $_POST['order_zip']);
    $safe_tel = mysqli_real_escape_string($mysqli, $_POST['order_tel']);
    
    // 2. Insert into store_orders
    $insert_order = "INSERT INTO store_orders (order_date, order_name, order_email, order_address, order_city, order_zip, order_tel, status) 
                     VALUES (now(), '$safe_name', '$safe_email', '$safe_address', '$safe_city', '$safe_zip', '$safe_tel', 'pending')";
    mysqli_query($mysqli, $insert_order) or die(mysqli_error($mysqli));
    
    // Get the new Order ID
    $order_id = mysqli_insert_id($mysqli);
    
    // 3. Move items from shoppertrack to orders_items
    while ($cart_item = mysqli_fetch_array($check_cart_res)) {
        $sel_item_id = $cart_item['sel_item_id'];
        $sel_qty = $cart_item['sel_item_qty'];
        $sel_size = $cart_item['sel_item_size'];
        $sel_color = $cart_item['sel_item_color'];
        
        // Get price again just to be safe
        $get_price = mysqli_query($mysqli, "SELECT item_price FROM store_items WHERE id = '$sel_item_id'");
        $price_row = mysqli_fetch_array($get_price);
        $item_price = $price_row['item_price'];
        
        $insert_item = "INSERT INTO store_orders_items (order_id, sel_item_id, sel_item_qty, sel_item_size, sel_item_color, sel_item_price) 
                        VALUES ('$order_id', '$sel_item_id', '$sel_qty', '$sel_size', '$sel_color', '$item_price')";
        mysqli_query($mysqli, $insert_item) or die(mysqli_error($mysqli));
    }
    
    // 4. Clear the Cart
    $clear_cart = "DELETE FROM store_shoppertrack WHERE session_id = '".session_id()."'";
    mysqli_query($mysqli, $clear_cart) or die(mysqli_error($mysqli));
    
    // 5. Success Message
    $display_block .= "<div style='text-align:center'>
        <h3>Order Placed Successfully!</h3>
        <p>Thank you, <strong>$safe_name</strong>. Your order ID is #<strong>$order_id</strong>.</p>
        <a href='seestore.php' class='btn'>Back to Store</a>
    </div>";
    
} else {
    // SHOW CHECKOUT FORM
    $display_block .= "
    <form method='post' action='checkout.php'>
        <label>Full Name:</label>
        <input type='text' name='order_name' required />
        
        <label>Email Address:</label>
        <input type='email' name='order_email' required />
        
        <label>Address:</label>
        <textarea name='order_address' rows='3' required></textarea>
        
        <label>City:</label>
        <input type='text' name='order_city' required />
        
        <label>Zip Code:</label>
        <input type='text' name='order_zip' required />
        
        <label>Telephone:</label>
        <input type='text' name='order_tel' required />
        
        <button type='submit' name='submit' class='btn'>Place Order</button>
        <a href='showcart.php' class='btn btn-back'>Back to Cart</a>
    </form>";
}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php echo $display_block; ?>
</body>
</html>