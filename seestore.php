<?php
//connect to database
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

// Kept the top header clean and perfectly centered
$display_block = "
<div style='text-align: center;'>
    <h1 style='border-bottom: none; padding-bottom: 0; margin: 0 0 10px 0;'>My Categories</h1>
    <p style='margin: 0; font-size: 16px;'>Select a category to see its items.</p>
</div>
<hr style='border: 0; border-top: 1px solid #eee; margin-top: 25px; margin-bottom: 20px;'>";

//show categories first
$get_cats_sql = "SELECT id, cat_title, cat_desc FROM
store_categories ORDER BY cat_title";
$get_cats_res = mysqli_query($mysqli, $get_cats_sql)
or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cats_res) < 1) {
    $display_block .= "<p style='text-align: center;'><em>Sorry, no categories to browse.</em></p>";
} else {
    // Flex container to center the category blocks
    $display_block .= "<div style='display: flex; flex-direction: column; align-items: center; gap: 20px;'>";
    
    while ($cats = mysqli_fetch_array($get_cats_res)) {
        $cat_id = $cats['id'];
        $cat_title = strtoupper(stripslashes($cats['cat_title']));
        $cat_desc = stripslashes($cats['cat_desc']);

        // Category Card Design
        $display_block .= "<div style='text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px; width: 80%; border: 1px solid #e0e0e0;'>
            <p style='margin-top: 0;'><strong><a href=\"".$_SERVER['PHP_SELF'].
            "?cat_id=".$cat_id."\" style='font-size: 20px;'>".$cat_title."</a></strong><br/>"
            .$cat_desc."</p>";

        if (isset($_GET['cat_id']) && ($_GET['cat_id'] == $cat_id)) {
            $safe_cat_id = mysqli_real_escape_string($mysqli, $_GET['cat_id']);
            $get_items_sql = "SELECT id, item_title, item_price
                FROM store_items WHERE
                cat_id = '".$cat_id."' ORDER BY item_title";
            $get_items_res = mysqli_query($mysqli, $get_items_sql)
            or die(mysqli_error($mysqli));

            if (mysqli_num_rows($get_items_res) < 1) {
                $display_block .= "<p><em>Sorry, no items in this category.</em></p>";
            } else {
                // Centered, unbulleted list for items
                $display_block .= "<ul style='list-style-type: none; padding: 0; border-top: 1px solid #ddd; padding-top: 15px;'>";
                while ($items = mysqli_fetch_array($get_items_res)) {
                    $item_id = $items['id'];
                    $item_title = stripslashes($items['item_title']);
                    $item_price = $items['item_price'];

                    $display_block .= "<li style='margin: 10px 0;'><a href=\"showitem.php?item_id=".
                        $item_id."\">".$item_title."</a>
                        <strong>(\$".$item_price.")</strong></li>";
                }
                $display_block .= "</ul>";
            }
            mysqli_free_result($get_items_res);
        }
        $display_block .= "</div>"; 
    }
    $display_block .= "</div>"; 
}
mysqli_free_result($get_cats_res);
mysqli_close($mysqli);

// Added the View Cart button safely at the BOTTOM center, outside the category list!
$display_block .= "
<div style='text-align: center; margin-top: 40px;'>
    <button onclick=\"window.location.href='showcart.php'\" style=\"background-color: #f4f4f4; color: black; border: 1px solid #888; padding: 6px 16px; border-radius: 3px; font-size: 15px; font-weight: normal; cursor: pointer;\">View Cart</button>
</div>";
?>
<!DOCTYPE html>
<html>
<head>
<title>My Categories</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php echo $display_block; ?>
    </div>
</body>
</html>