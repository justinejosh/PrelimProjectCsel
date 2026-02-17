<?php
session_start();

if (isset($_POST['sel_item_id'])) {
    //connect to database - CHANGE CREDENTIALS HERE
    $mysqli = mysqli_connect("localhost", "root", "", "testDB");

    //create safe values for use
    $safe_sel_item_id = mysqli_real_escape_string($mysqli, $_POST['sel_item_id']);
    $safe_sel_item_qty = mysqli_real_escape_string($mysqli, $_POST['sel_item_qty']);
    $safe_sel_item_size = mysqli_real_escape_string($mysqli, $_POST['sel_item_size']);
    $safe_sel_item_color = mysqli_real_escape_string($mysqli, $_POST['sel_item_color']);

    //validate item and get title and price
    $get_iteminfo_sql = "SELECT item_title FROM store_items WHERE id = '".$safe_sel_item_id."'";
    $get_iteminfo_res = mysqli_query($mysqli, $get_iteminfo_sql) or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_iteminfo_res) < 1) {
        mysqli_free_result($get_iteminfo_res);
        mysqli_close($mysqli);
        header("Location: seestore.php");
        exit;
    } else {
        while ($item_info = mysqli_fetch_array($get_iteminfo_res)) {
            $item_title = stripslashes($item_info['item_title']);
        }
        mysqli_free_result($get_iteminfo_res);

        //add info to cart table
        $addtocart_sql = "INSERT INTO store_shoppertrack (session_id, sel_item_id, sel_item_qty, sel_item_size, sel_item_color, date_added) VALUES ('".$_COOKIE['PHPSESSID']."', '".$safe_sel_item_id."', '".$safe_sel_item_qty."', '".$safe_sel_item_size."', '".$safe_sel_item_color."', now())";
        
        $addtocart_res = mysqli_query($mysqli, $addtocart_sql) or die(mysqli_error($mysqli));

        mysqli_close($mysqli);
        header("Location: showcart.php");
        exit;
    }
} else {
    header("Location: seestore.php");
    exit;
}
?>