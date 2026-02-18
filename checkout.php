<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>My Store - Checkout</h1>";

$get_cart_sql = "SELECT st.id, si.item_title, si.item_price,
st.sel_item_qty, st.sel_item_size, st.sel_item_color FROM
store_shoppertrack AS st LEFT JOIN store_items AS si ON
si.id = st.sel_item_id WHERE session_id = '".$_COOKIE['PHPSESSID']."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql)
or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cart_res) < 1) {
    mysqli_close($mysqli);
    header("Location: seestore.php");
    exit;
} else {
    $item_total = 0;
    $display_block .= <<<END_OF_TEXT
<h2>Order Summary</h2>
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

    while ($cart_info = mysqli_fetch_array($get_cart_res)) {
        $item_title = stripslashes($cart_info['item_title']);
        $item_price = $cart_info['item_price'];
        $item_qty   = $cart_info['sel_item_qty'];
        $item_color = $cart_info['sel_item_color'];
        $item_size  = $cart_info['sel_item_size'];
        $line_total = $item_price * $item_qty;
        $item_total += $line_total;
        $line_total_fmt = sprintf("%.02f", $line_total);

        $display_block .= "<tr>
<td>$item_title</td>
<td>$item_size</td>
<td>$item_color</td>
<td>\$ $item_price</td>
<td>$item_qty</td>
<td>\$ $line_total_fmt</td>
</tr>";
    }
    $item_total_fmt = sprintf("%.02f", $item_total);

    $display_block .= <<<END_OF_TEXT
<tr>
<td colspan="5" align="right"><strong>Order Total:</strong></td>
<td><strong>\$ $item_total_fmt</strong></td>
</tr>
</table>
END_OF_TEXT;
}

mysqli_free_result($get_cart_res);
mysqli_close($mysqli);

$display_block .= <<<END_OF_TEXT
<br/>
<h2>Billing Information</h2>
<form method="post" action="docheckout.php">
<table>
<tr>
<td width="30%"><label for="order_name">Full Name:</label></td>
<td><input type="text" id="order_name" name="order_name" required /></td>
</tr>
<tr>
<td><label for="order_address">Address:</label></td>
<td><input type="text" id="order_address" name="order_address" required /></td>
</tr>
<tr>
<td><label for="order_city">City:</label></td>
<td><input type="text" id="order_city" name="order_city" required /></td>
</tr>
<tr>
<td><label for="order_state">State:</label></td>
<td><input type="text" id="order_state" name="order_state" maxlength="2" required /></td>
</tr>
<tr>
<td><label for="order_zip">ZIP Code:</label></td>
<td><input type="text" id="order_zip" name="order_zip" required /></td>
</tr>
<tr>
<td><label for="order_tel">Phone:</label></td>
<td><input type="text" id="order_tel" name="order_tel" required /></td>
</tr>
<tr>
<td><label for="order_email">Email:</label></td>
<td><input type="text" id="order_email" name="order_email" required /></td>
</tr>
<tr>
<td colspan="2" align="right">
<button type="button" class="btn-back" onclick="window.location.href='showcart.php'">&laquo; Back to Cart</button>
<button type="submit" name="submit" value="submit">Place Order</button>
</td>
</tr>
</table>
</form>
END_OF_TEXT;
?>
<!DOCTYPE html>
<html>
<head>
<title>My Store - Checkout</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php echo $display_block; ?>
    </div>
</body>
</html>