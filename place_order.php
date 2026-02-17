<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "testDB");

if (!isset($_POST['order_name']) || empty($_POST['order_name'])) {
    header("Location: checkout.php");
    exit;
}

$safe_order_name     = mysqli_real_escape_string($mysqli, $_POST['order_name']);
$safe_order_address  = mysqli_real_escape_string($mysqli, $_POST['order_address']);
$safe_order_city     = mysqli_real_escape_string($mysqli, $_POST['order_city']);
$safe_order_state    = mysqli_real_escape_string($mysqli, $_POST['order_state']);
$safe_order_zip      = mysqli_real_escape_string($mysqli, $_POST['order_zip']);
$safe_order_tel      = mysqli_real_escape_string($mysqli, $_POST['order_tel']);
$safe_order_email    = mysqli_real_escape_string($mysqli, $_POST['order_email']);
$safe_item_total     = mysqli_real_escape_string($mysqli, $_POST['item_total']);
$safe_shipping_total = mysqli_real_escape_string($mysqli, $_POST['shipping_total']);
$safe_order_total    = mysqli_real_escape_string($mysqli, $_POST['order_total']);

// Demo: generate a fake authorization code
$authorization = strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));

// 1. Insert into store_orders
$insert_order_sql = "INSERT INTO store_orders
    (order_date, order_name, order_address, order_city, order_state,
     order_zip, order_tel, order_email, item_total, shipping_total,
     authorization, status)
    VALUES
    (NOW(), '".$safe_order_name."', '".$safe_order_address."',
     '".$safe_order_city."', '".$safe_order_state."',
     '".$safe_order_zip."', '".$safe_order_tel."',
     '".$safe_order_email."', '".$safe_item_total."',
     '".$safe_shipping_total."', '".$authorization."', 'pending')";
mysqli_query($mysqli, $insert_order_sql)
    or die(mysqli_error($mysqli));

$new_order_id = mysqli_insert_id($mysqli);

// 2. Retrieve cart items
$get_cart_sql = "SELECT st.sel_item_id, st.sel_item_qty,
    st.sel_item_size, st.sel_item_color, si.item_price
    FROM store_shoppertrack AS st
    LEFT JOIN store_items AS si ON si.id = st.sel_item_id
    WHERE st.session_id = '".$_COOKIE['PHPSESSID']."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql)
    or die(mysqli_error($mysqli));

// 3. Insert line items
while ($cart_item = mysqli_fetch_array($get_cart_res)) {
    $safe_item_id    = mysqli_real_escape_string($mysqli, $cart_item['sel_item_id']);
    $safe_item_qty   = mysqli_real_escape_string($mysqli, $cart_item['sel_item_qty']);
    $safe_item_size  = mysqli_real_escape_string($mysqli, $cart_item['sel_item_size']);
    $safe_item_color = mysqli_real_escape_string($mysqli, $cart_item['sel_item_color']);
    $safe_item_price = mysqli_real_escape_string($mysqli, $cart_item['item_price']);

    $insert_item_sql = "INSERT INTO store_orders_items
        (order_id, sel_item_id, sel_item_qty,
         sel_item_size, sel_item_color, sel_item_price)
        VALUES
        ('".$new_order_id."', '".$safe_item_id."',
         '".$safe_item_qty."', '".$safe_item_size."',
         '".$safe_item_color."', '".$safe_item_price."')";
    mysqli_query($mysqli, $insert_item_sql)
        or die(mysqli_error($mysqli));
}
mysqli_free_result($get_cart_res);

// 4. Clear cart
$delete_cart_sql = "DELETE FROM store_shoppertrack
    WHERE session_id = '".$_COOKIE['PHPSESSID']."'";
mysqli_query($mysqli, $delete_cart_sql)
    or die(mysqli_error($mysqli));

// 5. Confirmation display
$order_total = htmlspecialchars($_POST['order_total']);

$display_block = <<<END_OF_TEXT
<div class="confirm-box">
    <div class="confirm-icon">&#10003;</div>
    <h1>Order Confirmed!</h1>
    <p>Thank you, <strong>$safe_order_name</strong>! Your order has been placed successfully.</p>
    <table class="confirm-table">
        <tr>
            <td><strong>Order #:</strong></td>
            <td>$new_order_id</td>
        </tr>
        <tr>
            <td><strong>Authorization Code:</strong></td>
            <td>$authorization</td>
        </tr>
        <tr>
            <td><strong>Order Total:</strong></td>
            <td>\$ $order_total</td>
        </tr>
        <tr>
            <td><strong>Shipping To:</strong></td>
            <td>$safe_order_address, $safe_order_city, $safe_order_state $safe_order_zip</td>
        </tr>
        <tr>
            <td><strong>Confirmation sent to:</strong></td>
            <td>$safe_order_email</td>
        </tr>
    </table>
    <p class="note">Please save your Order # and Authorization Code as your receipt.</p>
    <a href="seestore.php" class="btn-continue">&#127978; Continue Shopping</a>
</div>
END_OF_TEXT;

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Store - Order Confirmed</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="wrapper">
    <?php echo $display_block; ?>
</div>
</body>
</html>