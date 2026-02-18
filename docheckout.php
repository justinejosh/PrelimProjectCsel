<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>My Store - Order Confirmation</h1>";

// Removed the cc_number requirement so it actually processes!
if (!isset($_POST['order_name'])  || empty($_POST['order_name']) ||
    !isset($_POST['order_email']) || empty($_POST['order_email'])) {
    mysqli_close($mysqli);
    header("Location: checkout.php");
    exit;
}

$safe_order_name    = mysqli_real_escape_string($mysqli, $_POST['order_name']);
$safe_order_address = mysqli_real_escape_string($mysqli, $_POST['order_address']);
$safe_order_city    = mysqli_real_escape_string($mysqli, $_POST['order_city']);
$safe_order_state   = mysqli_real_escape_string($mysqli, $_POST['order_state']);
$safe_order_zip     = mysqli_real_escape_string($mysqli, $_POST['order_zip']);
$safe_order_tel     = mysqli_real_escape_string($mysqli, $_POST['order_tel']);
$safe_order_email   = mysqli_real_escape_string($mysqli, $_POST['order_email']);

$get_cart_sql = "SELECT st.sel_item_id, st.sel_item_qty,
st.sel_item_size, st.sel_item_color, si.item_title, si.item_price
FROM store_shoppertrack AS st LEFT JOIN store_items AS si ON
si.id = st.sel_item_id WHERE session_id = '".$_COOKIE['PHPSESSID']."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql)
or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cart_res) < 1) {
    mysqli_close($mysqli);
    header("Location: seestore.php");
    exit;
}

$item_total = 0;
$cart_items = array();

while ($cart_info = mysqli_fetch_array($get_cart_res)) {
    $cart_items[] = $cart_info;
    $item_total += ($cart_info['item_price'] * $cart_info['sel_item_qty']);
}
mysqli_free_result($get_cart_res);

$order_total = $item_total;
$authorization   = "SIMAUTH-".strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
$payment_success = true;

if (!$payment_success) {
    $display_block .= "<p><em>Payment authorization failed.
    Please <a href=\"checkout.php\">try again</a>.</em></p>";
    mysqli_close($mysqli);
} else {
    $safe_item_total     = mysqli_real_escape_string($mysqli, sprintf("%.02f", $item_total));
    $safe_shipping_total = "0.00"; // Fixed NULL shipping!
    $safe_authorization  = mysqli_real_escape_string($mysqli, $authorization);

    $add_order_sql = "INSERT INTO store_orders
        (order_date, order_name, order_address, order_city,
        order_state, order_zip, order_tel, order_email,
        item_total, shipping_total, authorization, status)
        VALUES (now(), '".$safe_order_name."',
        '".$safe_order_address."', '".$safe_order_city."',
        '".$safe_order_state."', '".$safe_order_zip."',
        '".$safe_order_tel."', '".$safe_order_email."',
        '".$safe_item_total."', '".$safe_shipping_total."',
        '".$safe_authorization."', 'processed')";
    $add_order_res = mysqli_query($mysqli, $add_order_sql)
    or die(mysqli_error($mysqli));

    $order_id = mysqli_insert_id($mysqli);

    foreach ($cart_items as $cart_item) {
        $safe_item_id    = mysqli_real_escape_string($mysqli, $cart_item['sel_item_id']);
        $safe_item_qty   = mysqli_real_escape_string($mysqli, $cart_item['sel_item_qty']);
        $safe_item_size  = mysqli_real_escape_string($mysqli, $cart_item['sel_item_size']);
        $safe_item_color = mysqli_real_escape_string($mysqli, $cart_item['sel_item_color']);
        $safe_item_price = mysqli_real_escape_string($mysqli, sprintf("%.02f", $cart_item['item_price']));

        $add_item_sql = "INSERT INTO store_orders_items
            (order_id, sel_item_id, sel_item_qty,
            sel_item_size, sel_item_color, sel_item_price)
            VALUES ('".$order_id."', '".$safe_item_id."',
            '".$safe_item_qty."', '".$safe_item_size."',
            '".$safe_item_color."', '".$safe_item_price."')";
        $add_item_res = mysqli_query($mysqli, $add_item_sql)
        or die(mysqli_error($mysqli));
    }

    $delete_cart_sql = "DELETE FROM store_shoppertrack WHERE session_id = '".$_COOKIE['PHPSESSID']."'";
    $delete_cart_res = mysqli_query($mysqli, $delete_cart_sql)
    or die(mysqli_error($mysqli));

    mysqli_close($mysqli);

    $order_total_fmt = sprintf("%.02f", $order_total);

    $display_block .= <<<END_OF_TEXT
<p style="background: #e9ecef; padding: 15px; border-radius: 5px;">
Thank you, <strong>$safe_order_name</strong>! Your order has been placed successfully.<br/>
<strong>Authorization Code:</strong> $authorization<br/>
A confirmation will be sent to <strong>$safe_order_email</strong>.
</p>
<h2>Order Receipt</h2>
<table>
<tr>
<th>Title</th>
<th>Size</th>
<th>Color</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
</tr>
END_OF_TEXT;

    foreach ($cart_items as $cart_item) {
        $receipt_title = stripslashes($cart_item['item_title']);
        $receipt_size  = $cart_item['sel_item_size'];
        $receipt_color = $cart_item['sel_item_color'];
        $receipt_price = $cart_item['item_price'];
        $receipt_qty   = $cart_item['sel_item_qty'];
        $receipt_line  = sprintf("%.02f", $receipt_price * $receipt_qty);

        $display_block .= "<tr>
<td>$receipt_title</td>
<td>$receipt_size</td>
<td>$receipt_color</td>
<td>\$ $receipt_price</td>
<td>$receipt_qty</td>
<td>\$ $receipt_line</td>
</tr>";
    }

    $display_block .= <<<END_OF_TEXT
<tr>
<td colspan="5" align="right"><strong>Free Shipping:</strong></td>
<td><strong>$ 0.00</strong></td>
</tr>
<tr>
<td colspan="5" align="right"><strong>Order Total:</strong></td>
<td><strong>\$ $order_total_fmt</strong></td>
</tr>
</table>
<br/>
<div align="center">
    <button onclick="window.location.href='seestore.php'">Continue Shopping</button>
</div>
END_OF_TEXT;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Store - Order Confirmation</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php echo $display_block; ?>
    </div>
</body>
</html>