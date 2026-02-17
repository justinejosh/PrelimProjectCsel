<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>Checkout</h1>";

$get_cart_sql = "SELECT st.id, si.item_title, si.item_price,
    st.sel_item_qty, st.sel_item_size, st.sel_item_color FROM
    store_shoppertrack AS st LEFT JOIN store_items AS si ON
    si.id = st.sel_item_id WHERE session_id =
    '".$_COOKIE['PHPSESSID']."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql)
    or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cart_res) < 1) {
    $display_block .= "<p>You have no items in your cart.
        Please <a href=\"seestore.php\">continue to shop</a>!</p>";

    mysqli_free_result($get_cart_res);
    mysqli_close($mysqli);
} else {
    $display_block .= <<<END_OF_TEXT
<p><a class="btn-back" href="showcart.php">&larr; Back to Cart</a></p>
<h2>Order Summary</h2>
<table>
<tr>
    <th>Item</th>
    <th>Size</th>
    <th>Color</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Subtotal</th>
</tr>
END_OF_TEXT;

    $item_total = 0;

    while ($cart_info = mysqli_fetch_array($get_cart_res)) {
        $item_title  = stripslashes($cart_info['item_title']);
        $item_price  = $cart_info['item_price'];
        $item_qty    = $cart_info['sel_item_qty'];
        $item_color  = $cart_info['sel_item_color'];
        $item_size   = $cart_info['sel_item_size'];
        $subtotal    = sprintf("%.02f", $item_price * $item_qty);
        $item_total += $item_price * $item_qty;

        $display_block .= <<<END_OF_TEXT
<tr>
    <td>$item_title</td>
    <td>$item_size</td>
    <td>$item_color</td>
    <td>$item_qty</td>
    <td>\$ $item_price</td>
    <td>\$ $subtotal</td>
</tr>
END_OF_TEXT;
    }

    $shipping_total = 0.00;
    $order_total    = sprintf("%.02f", $item_total + $shipping_total);
    $item_total     = sprintf("%.02f", $item_total);

    $display_block .= <<<END_OF_TEXT
<tr class="subtotal-row">
    <td colspan="5" style="text-align:right;"><strong>Item Total:</strong></td>
    <td><strong>\$ $item_total</strong></td>
</tr>
<tr class="subtotal-row">
    <td colspan="5" style="text-align:right;"><strong>Shipping (flat rate):</strong></td>
    <td><strong>\$ $shipping_total</strong></td>
</tr>
<tr class="total-row">
    <td colspan="5" style="text-align:right;"><strong>Order Total:</strong></td>
    <td><strong>\$ $order_total</strong></td>
</tr>
</table>
END_OF_TEXT;

    mysqli_free_result($get_cart_res);
    mysqli_close($mysqli);

    $display_block .= <<<END_OF_TEXT
<h2>Your Information</h2>
<p class="note">Fields marked <span class="req">*</span> are required.</p>
<form method="post" action="place_order.php">
<input type="hidden" name="item_total"     value="$item_total" />
<input type="hidden" name="shipping_total" value="$shipping_total" />
<input type="hidden" name="order_total"    value="$order_total" />

<fieldset>
    <legend>Contact &amp; Shipping Details</legend>
    <div class="form-row">
        <label for="order_name">Full Name <span class="req">*</span></label>
        <input type="text" id="order_name" name="order_name" required maxlength="100" />
    </div>
    <div class="form-row">
        <label for="order_address">Address <span class="req">*</span></label>
        <input type="text" id="order_address" name="order_address" required maxlength="255" />
    </div>
    <div class="form-row">
        <label for="order_city">City <span class="req">*</span></label>
        <input type="text" id="order_city" name="order_city" required maxlength="50" />
    </div>
    <div class="form-row">
        <label for="order_state">State <span class="req">*</span></label>
        <input type="text" id="order_state" name="order_state" required maxlength="2" placeholder="e.g. CA" />
    </div>
    <div class="form-row">
        <label for="order_zip">ZIP Code <span class="req">*</span></label>
        <input type="text" id="order_zip" name="order_zip" required maxlength="10" />
    </div>
    <div class="form-row">
        <label for="order_tel">Phone Number <span class="req">*</span></label>
        <input type="tel" id="order_tel" name="order_tel" required maxlength="25" />
    </div>
    <div class="form-row">
        <label for="order_email">Email Address <span class="req">*</span></label>
        <input type="email" id="order_email" name="order_email" required maxlength="100" />
    </div>
</fieldset>


<div class="form-actions">
    <a href="showcart.php" class="btn-back">&larr; Back to Cart</a>
    <button type="submit" class="btn-place-order">&#10003; Place Order</button>
</div>
</form>
END_OF_TEXT;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Store - Checkout</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="wrapper">
    <?php echo $display_block; ?>
</div>
</body>
</html>