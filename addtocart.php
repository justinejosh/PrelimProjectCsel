<?php
session_start();
$mysqli = mysqli_connect("localhost","root","","testDB");

$add_sql = "INSERT INTO store_shoppertrack
(session_id, sel_item_id, sel_item_qty, sel_item_size, sel_item_color, date_added)
VALUES (
'{$_COOKIE['PHPSESSID']}',
'{$_POST['sel_item_id']}',
'{$_POST['sel_item_qty']}',
'{$_POST['sel_item_size']}',
'{$_POST['sel_item_color']}',
NOW()
)";
mysqli_query($mysqli,$add_sql);

mysqli_close($mysqli);
header("Location: showcart.php");
exit;
?>
