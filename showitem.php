<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

$display_block = "<h1 style='text-align: center;'>My Store - Item Detail</h1>";
$safe_item_id = mysqli_real_escape_string($mysqli, $_GET['item_id']);

$get_item_sql = "SELECT c.id as cat_id, c.cat_title, si.item_title,
si.item_price, si.item_desc, si.item_image FROM store_items
AS si LEFT JOIN store_categories AS c on c.id = si.cat_id
WHERE si.id = '".$safe_item_id."'";
$get_item_res = mysqli_query($mysqli, $get_item_sql)
or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_item_res) < 1) {
    $display_block .= "<p style='text-align: center;'><em>Invalid item selection.</em></p>";
} else {
    while ($item_info = mysqli_fetch_array($get_item_res)) {
        $cat_id = $item_info['cat_id'];
        $cat_title = strtoupper(stripslashes($item_info['cat_title']));
        $item_title = stripslashes($item_info['item_title']);
        $item_price = $item_info['item_price'];
        $item_desc = stripslashes($item_info['item_desc']);
        $item_image = $item_info['item_image'];
    }

    $display_block .= <<<END_OF_TEXT
<div style="text-align: center; margin-bottom: 25px;">
    <p><em>You are viewing:</em><br/>
    <strong><a href="seestore.php?cat_id=$cat_id">$cat_title</a> &gt; $item_title</strong></p>
</div>

<div style="display: flex; justify-content: center; align-items: flex-start; gap: 40px; flex-wrap: wrap;">
    
    <div style="text-align: center;">
        <img id="product_image" src="$item_image" alt="$item_title" style="max-width: 300px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
    </div>
    
    <div style="max-width: 350px; text-align: left; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
        <p style="margin-top: 0;"><strong>Description:</strong><br/>$item_desc</p>
        <p style="font-size: 18px;"><strong>Price:</strong> <span style="color: #28a745;">\$$item_price</span></p>
        <form method="post" action="addtocart.php">
END_OF_TEXT;

    mysqli_free_result($get_item_res);

    $get_colors_sql = "SELECT item_color FROM store_item_color WHERE
        item_id = '".$safe_item_id."' ORDER BY item_color";
    $get_colors_res = mysqli_query($mysqli, $get_colors_sql)
    or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_colors_res) > 0) {
        $display_block .= "<p><label for=\"sel_item_color\">
Available Colors:</label><br/>
<select id=\"sel_item_color\" name=\"sel_item_color\" onchange=\"updateImage(this.value)\" style=\"width: 100%;\">";
        while ($colors = mysqli_fetch_array($get_colors_res)) {
            $item_color = $colors['item_color'];
            $display_block .= "<option value=\"".$item_color."\">".$item_color."</option>";
        }
        $display_block .= "</select></p>";
    }
    mysqli_free_result($get_colors_res);

    $get_sizes_sql = "SELECT item_size FROM store_item_size WHERE
        item_id = ".$safe_item_id." ORDER BY item_size";
    $get_sizes_res = mysqli_query($mysqli, $get_sizes_sql)
    or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_sizes_res) > 0) {
        $display_block .= "<p><label for=\"sel_item_size\">
Available Sizes:</label><br/>
<select id=\"sel_item_size\" name=\"sel_item_size\" style=\"width: 100%;\">";
        while ($sizes = mysqli_fetch_array($get_sizes_res)) {
            $item_size = $sizes['item_size'];
            $display_block .= "<option value=\"".$item_size."\">".$item_size."</option>";
        }
        $display_block .= "</select></p>";
    }
    mysqli_free_result($get_sizes_res);

    $display_block .= "
<p><label for=\"sel_item_qty\">Select Quantity:</label><br/>
<select id=\"sel_item_qty\" name=\"sel_item_qty\" style=\"width: 100%;\">";
    for($i=1; $i<11; $i++) {
        $display_block .= "<option value=\"".$i."\">".$i."</option>";
    }
    $display_block .= <<<END_OF_TEXT
</select></p>
<input type="hidden" name="sel_item_id" value="$_GET[item_id]" />
<br/>
<button type="submit" name="submit" value="submit" style="width: 100%;">Add to Cart</button>
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
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php echo $display_block; ?>
    </div>

<script>
    var defaultImage = "<?php echo isset($item_image) ? $item_image : ''; ?>";
    function updateImage(color) {
        var imgElement = document.getElementById("product_image");
        if (color === "red") {
            imgElement.src = "red.jpg";
        } else if (color === "blue") {
            imgElement.src = "blue.jpg";
        } else if (color === "black") {
            imgElement.src = "black_hat.jpg";
        } else {
            imgElement.src = defaultImage;
        }
    }
</script>
</body>
</html>