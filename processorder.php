<?php
session_start();
$mysqli = mysqli_connect("localhost","root","","testDB");

// 1️⃣ Calculate total
$total_sql = "
SELECT si.item_price, st.sel_item_qty
FROM store_shoppertrack st
JOIN store_items si ON si.id = st.sel_item_id
WHERE session_id='{$_COOKIE['PHPSESSID']}'
";
$total_res = mysqli_query($mysqli, $total_sql);

$item_total = 0;
while ($row = mysqli_fetch_assoc($total_res)) {
    $item_total += $row['item_price'] * $row['sel_item_qty'];
}

// 2️⃣ Insert order
$order_sql = "
INSERT INTO store_orders
(order_date, order_name, order_address, order_city, order_state,
 order_zip, order_tel, order_email, item_total, shipping_total,
 authorization, status)
VALUES (
NOW(),
'{$_POST['order_name']}',
'{$_POST['order_address']}',
'{$_POST['order_city']}',
'{$_POST['order_state']}',
'{$_POST['order_zip']}',
'{$_POST['order_tel']}',
'{$_POST['order_email']}',
$item_total,
0.00,
'TEST-AUTH',
'processed'
)
";
mysqli_query($mysqli, $order_sql);

$order_id = mysqli_insert_id($mysqli);

$cart_sql = "
SELECT * FROM store_shoppertrack
WHERE session_id='{$_COOKIE['PHPSESSID']}'
";
$cart_res = mysqli_query($mysqli, $cart_sql);

while ($item = mysqli_fetch_assoc($cart_res)) {
    mysqli_query($mysqli, "
    INSERT INTO store_orders_items
    (order_id, sel_item_id, sel_item_qty,
     sel_item_size, sel_item_color, sel_item_price)
    VALUES (
    $order_id,
    {$item['sel_item_id']},
    {$item['sel_item_qty']},
    '{$item['sel_item_size']}',
    '{$item['sel_item_color']}',
    (SELECT item_price FROM store_items WHERE id={$item['sel_item_id']})
    )
    ");
}

// 4️⃣ Clear cart
mysqli_query($mysqli, "
DELETE FROM store_shoppertrack
WHERE session_id='{$_COOKIE['PHPSESSID']}'
");

mysqli_close($mysqli);

echo "<h1>Order Complete</h1>";
echo "<p>Your order ID is <strong>$order_id</strong></p>";
echo "
<p>
  <a href='seestore.php'>
    <button type='button'>⬅ Back to Store</button>
  </a>
</p>
";


?>
