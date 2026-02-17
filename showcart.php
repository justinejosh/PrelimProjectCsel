<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "
<header>
    <h1>Your Cart</h1>
    <a href='seestore.php' class='btn-cart'>Continue Shopping 🛍️</a>
</header>";

$display_block .= "<div style='max-width: 800px; margin: 0 auto;'>";

$get_cart_sql = "SELECT st.id, si.item_title, si.item_price, st.sel_item_qty, st.sel_item_size, st.sel_item_color FROM store_shoppertrack AS st LEFT JOIN store_items AS si ON si.id = st.sel_item_id WHERE session_id = '".session_id()."'";
$get_cart_res = mysqli_query($mysqli, $get_cart_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cart_res) < 1) {
    $display_block .= "<p style='text-align:center'>You have no items in your cart. <br><br><a href='seestore.php' class='btn'>Start Shopping</a></p>";
} else {
    $display_block .= "
    <table>
    <tr>
    <th>Title</th>
    <th>Size</th>
    <th>Color</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total Price</th>
    <th>Action</th>
    </tr>";

    $grand_total = 0;

    while ($cart_info = mysqli_fetch_array($get_cart_res)) {
        $id = $cart_info['id'];
        $item_title = stripslashes($cart_info['item_title']);
        $item_price = $cart_info['item_price'];
        $item_qty = $cart_info['sel_item_qty'];
        $item_color = $cart_info['sel_item_color'];
        $item_size = $cart_info['sel_item_size'];
        $total_price = sprintf("%.02f", $item_price * $item_qty);
        $grand_total += $total_price;

        $display_block .= "
        <tr>
        <td>$item_title</td>
        <td>$item_size</td>
        <td>$item_color</td>
        <td>\$ $item_price</td>
        <td>$item_qty</td>
        <td>\$ $total_price</td>
        <td><a href='removefromcart.php?id=$id' style='color:red;'>remove</a></td>
        </tr>";
    }
    
    $display_block .= "<tr>
        <td colspan='5' style='text-align:right; font-weight:bold;'>Grand Total:</td>
        <td style='font-weight:bold;'>\$ ".sprintf("%.02f", $grand_total)."</td>
        <td></td>
    </tr>";
    
    $display_block .= "</table>";
    
    $display_block .= "
    <div style='text-align: center; margin-top: 20px;'>
        <a href='checkout.php' class='btn'>Proceed to Checkout</a>
    </div>";
}

$display_block .= "</div>"; // Close Container
mysqli_free_result($get_cart_res);
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Cart</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php echo $display_block; ?>
</body>
</html>