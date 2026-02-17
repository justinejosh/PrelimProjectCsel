<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>Your Shopping Cart</h1>";

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
} else {
    $display_block .= <<<END_OF_TEXT
<table>
<tr>
    <th>Title</th>
    <th>Size</th>
    <th>Color</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total Price</th>
    <th>Action</th>
</tr>
END_OF_TEXT;

    $grand_total = 0;

    while ($cart_info = mysqli_fetch_array($get_cart_res)) {
        $id          = $cart_info['id'];
        $item_title  = stripslashes($cart_info['item_title']);
        $item_price  = $cart_info['item_price'];
        $item_qty    = $cart_info['sel_item_qty'];
        $item_color  = $cart_info['sel_item_color'];
        $item_size   = $cart_info['sel_item_size'];
        $total_price = sprintf("%.02f", $item_price * $item_qty);
        $grand_total += $item_price * $item_qty;

        $display_block .= <<<END_OF_TEXT
<tr>
    <td>$item_title</td>
    <td>$item_size</td>
    <td>$item_color</td>
    <td>\$ $item_price</td>
    <td>$item_qty</td>
    <td>\$ $total_price</td>
    <td><a href="removefromcart.php?id=$id">remove</a></td>
</tr>
END_OF_TEXT;
    }

    $grand_total = sprintf("%.02f", $grand_total);

    $display_block .= <<<END_OF_TEXT
<tr class="grand-total-row">
    <td colspan="5" style="text-align:right;"><strong>Grand Total:</strong></td>
    <td><strong>\$ $grand_total</strong></td>
    <td></td>
</tr>
</table>

<div class="cart-actions">
    <a href="seestore.php" class="btn-back">&larr; Continue Shopping</a>
    <a href="checkout.php" class="btn-checkout">Proceed to Checkout &rarr;</a>
</div>
END_OF_TEXT;
}

mysqli_free_result($get_cart_res);
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Store - Cart</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="wrapper">
    <?php echo $display_block; ?>
</div>
</body>
</html>