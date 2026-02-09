<?php
session_start();
$mysqli = mysqli_connect("localhost","root","","testDB");

$id = mysqli_real_escape_string($mysqli, $_GET['id']);

mysqli_query($mysqli,
"DELETE FROM store_shoppertrack 
 WHERE id='$id' AND session_id='{$_COOKIE['PHPSESSID']}'");

mysqli_close($mysqli);
header("Location: showcart.php");
exit;
?>
