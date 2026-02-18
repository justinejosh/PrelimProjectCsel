<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1 style='text-align: center; border-bottom: none; padding-bottom: 0;'>Your Shopping Cart</h1>
<hr style='border: 0; border-top: 1px solid #eee; margin-top: 15px; margin-bottom: 25px;'>";

$get_cart_sql = "SELECT st.id, si.item_title, si.item_price,
st.sel_item_qty, st.sel_item_size, st.sel_item_color FROM
store_shoppertrack AS st LEFT JOIN store_items AS si ON
si.id = st.sel_item_id WHERE session_id = '".$_COOKIE['PHPSESSID']."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql)
or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cart_res) < 1) {
    // UPDATED: Centered empty cart message with a proper button!
    $display_block .= "
    <div style='text-align: center; padding: 40px 0;'>
        <p style='font-size: 18px; color: #555; margin-bottom: 20px;'>You have no items in your cart.</p>
        <button onclick=\"window.location.href='seestore.php'\" style=\"background-color: #f4f4f4; color: black; border: 1px solid #888; padding: 8px 20px; border-radius: 3px; font-size: 16px; cursor: pointer;\">&laquo; Continue Shopping</button>
    </div>";
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

    while ($cart_info = mysqli_fetch_array($get_cart_res)) {
        $id = $cart_info['id'];
        $item_title = stripslashes($cart_info['item_title']);
        $item_price = $cart_info['item_price'];
        $item_qty = $cart_info['sel_item_qty'];
        $item_color = $cart_info['sel_item_color'];
        $item_size = $cart_info['sel_item_size'];
        $total_price = sprintf("%.02f", $item_price * $item_qty);

        $display_block .= "<tr>
<td>$item_title</td>
<td>$item_size</td>
<td>$item_color</td>
<td>\$ $item_price</td>
<td>$item_qty</td>
<td>\$ $total_price</td>
<td align='center'><a href=\"removefromcart.php?id=$id\" style=\"color: #dc3545; font-weight: bold;\">remove</a></td>
</tr>";
    }
    $display_block .= "</table>";

    // UPDATED: Styled the buttons at the bottom of a full cart to match the new aesthetic
    $display_block .= "
    <br/>
    <div style='display: flex; justify-content: space-between; align-items: center; margin-top: 20px;'>
        <button onclick=\"window.location.href='seestore.php'\" style=\"background-color: #f4f4f4; color: black; border: 1px solid #888; padding: 8px 16px; border-radius: 3px; font-size: 15px; cursor: pointer;\">&laquo; Continue Shopping</button>
        
        <button onclick=\"window.location.href='checkout.php'\" style=\"background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer;\">Proceed to Checkout &raquo;</button>
    </div>";
}
mysqli_free_result($get_cart_res);
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Store - Cart</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php echo $display_block; ?>
    </div>
</body>
</html>