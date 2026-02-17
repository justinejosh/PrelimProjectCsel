<?php
session_start();
//connect to database
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1>My Store - Item Detail</h1>";

//create safe values for use
$safe_item_id = mysqli_real_escape_string($mysqli, $_GET['item_id']);

//validate item
$get_item_sql = "SELECT c.id as cat_id, c.cat_title, si.item_title,
    si.item_price, si.item_desc, si.item_image FROM store_items
    AS si LEFT JOIN store_categories AS c on c.id = si.cat_id
    WHERE si.id = '".$safe_item_id."'";
$get_item_res = mysqli_query($mysqli, $get_item_sql)
    or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_item_res) < 1) {
    $display_block .= "<p><em>Invalid item selection.</em></p>";
    $display_block .= "<p><a class=\"btn-back\" href=\"seestore.php\">&larr; Back to Shopping</a></p>";
} else {
    while ($item_info = mysqli_fetch_array($get_item_res)) {
        $cat_id     = $item_info['cat_id'];
        $cat_title  = strtoupper(stripslashes($item_info['cat_title']));
        $item_title = stripslashes($item_info['item_title']);
        $item_price = $item_info['item_price'];
        $item_desc  = stripslashes($item_info['item_desc']);
        $item_image = $item_info['item_image'];
    }

    $display_block .= <<<END_OF_TEXT
<a class="btn-back" href="seestore.php?cat_id=$cat_id">&larr; Back to Shopping</a>
<p class="breadcrumb">
    <a href="seestore.php?cat_id=$cat_id">$cat_title</a> &rsaquo; $item_title
</p>

<div class="item-wrapper">
    <div class="item-image">
        <img src="$item_image" alt="$item_title" width="400" height="400" />
    </div>
    <div class="item-details">
        <h2>$item_title</h2>
        <p class="item-price">\$$item_price</p>
        <p class="item-desc">$item_desc</p>
        <form method="post" action="addtocart.php">
END_OF_TEXT;

    mysqli_free_result($get_item_res);

    //get colors
    $get_colors_sql = "SELECT item_color FROM store_item_color WHERE
        item_id = '".$safe_item_id."' ORDER BY item_color";
    $get_colors_res = mysqli_query($mysqli, $get_colors_sql)
        or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_colors_res) > 0) {
        $display_block .= "<div class=\"form-row\">
            <label for=\"sel_item_color\">Color:</label>
            <select id=\"sel_item_color\" name=\"sel_item_color\">";
        while ($colors = mysqli_fetch_array($get_colors_res)) {
            $item_color = $colors['item_color'];
            $display_block .= "<option value=\"".$item_color."\">".$item_color."</option>";
        }
        $display_block .= "</select></div>";
    }
    mysqli_free_result($get_colors_res);

    //get sizes
    $get_sizes_sql = "SELECT item_size FROM store_item_size WHERE
        item_id = ".$safe_item_id." ORDER BY item_size";
    $get_sizes_res = mysqli_query($mysqli, $get_sizes_sql)
        or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_sizes_res) > 0) {
        $display_block .= "<div class=\"form-row\">
            <label for=\"sel_item_size\">Size:</label>
            <select id=\"sel_item_size\" name=\"sel_item_size\">";
        while ($sizes = mysqli_fetch_array($get_sizes_res)) {
            $item_size = $sizes['item_size'];
            $display_block .= "<option value=\"".$item_size."\">".$item_size."</option>";
        }
        $display_block .= "</select></div>";
    }
    mysqli_free_result($get_sizes_res);

    $display_block .= "<div class=\"form-row\">
        <label for=\"sel_item_qty\">Quantity:</label>
        <select id=\"sel_item_qty\" name=\"sel_item_qty\">";
    for ($i = 1; $i < 11; $i++) {
        $display_block .= "<option value=\"".$i."\">".$i."</option>";
    }

    $display_block .= <<<END_OF_TEXT
        </select>
    </div>
    <input type="hidden" name="sel_item_id" value="$_GET[item_id]" />
    <div class="form-actions">
        <button type="submit" name="submit" value="submit" class="btn-cart">
            &#128722; Add to Cart
        </button>
    </div>
    </form>
    </div>
</div>
END_OF_TEXT;
}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Store - Item Detail</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="wrapper">
    <?php echo $display_block; ?>
</div>
</body>
</html>