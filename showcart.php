<?php
session_start();
$mysqli = mysqli_connect("localhost","root","","testDB");

$sql = "SELECT st.id, si.item_title, si.item_price,
st.sel_item_qty, st.sel_item_size, st.sel_item_color
FROM store_shoppertrack st
JOIN store_items si ON si.id = st.sel_item_id
WHERE session_id='{$_COOKIE['PHPSESSID']}'";

$res = mysqli_query($mysqli,$sql);

echo "<h1>Your Cart</h1>";

if (mysqli_num_rows($res) < 1) {
    echo "Cart is empty.";
} else {
    echo "<table border='1'>";
    while ($row = mysqli_fetch_assoc($res)) {
        $total = $row['item_price'] * $row['sel_item_qty'];
        echo "<tr>
        <td>{$row['item_title']}</td>
        <td>{$row['sel_item_size']}</td>
        <td>{$row['sel_item_color']}</td>
        <td>{$row['sel_item_qty']}</td>
        <td>\$$total</td>
        <td><a href='removefromcart.php?id={$row['id']}'>Remove</a></td>
        </tr>";
    }
    echo "</table>";
    echo "
    <p>
    <button onclick=\"history.back()\">⬅ Back to Items</button>
    </p>
    ";
    echo "
    <p>
      <a href='checkout.php'>
        <button type='button'>Proceed to Checkout</button>
      </a>
    </p>
    ";
}
mysqli_close($mysqli);
?>
