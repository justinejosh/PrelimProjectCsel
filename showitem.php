<?php
session_start();
$mysqli = mysqli_connect("localhost","root","","testDB");

$item_id = mysqli_real_escape_string($mysqli, $_GET['item_id']);

$item_sql = "SELECT * FROM store_items WHERE id='$item_id'";
$item_res = mysqli_query($mysqli, $item_sql);

$item = mysqli_fetch_assoc($item_res);

echo "<h1>{$item['item_title']}</h1>";
echo "<p>{$item['item_desc']}</p>";
echo "<p>Price: \${$item['item_price']}</p>";

echo "<form method='post' action='addtocart.php'>";

echo "<select name='sel_item_color'>";
$colors = mysqli_query($mysqli,"SELECT item_color FROM store_item_color WHERE item_id='$item_id'");
while ($c = mysqli_fetch_assoc($colors)) {
    echo "<option>{$c['item_color']}</option>";
}
echo "</select>";

echo "<select name='sel_item_size'>";
$sizes = mysqli_query($mysqli,"SELECT item_size FROM store_item_size WHERE item_id='$item_id'");
while ($s = mysqli_fetch_assoc($sizes)) {
    echo "<option>{$s['item_size']}</option>";
}
echo "</select>";

echo "<select name='sel_item_qty'>";
for ($i=1;$i<=10;$i++) echo "<option>$i</option>";
echo "</select>";

echo "<input type='hidden' name='sel_item_id' value='$item_id'>";
echo "<button type='submit'>Add to Cart</button>";
echo "</form>";

mysqli_close($mysqli);
?>
