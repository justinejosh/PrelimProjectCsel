<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

// Start the display block with the Navigation Bar
$display_block = "
<header>
    <h1>My Shop</h1>
    <a href='showcart.php' class='btn-cart'>View Cart 🛒</a>
</header>";

$display_block .= "<div style='max-width: 800px; margin: 0 auto;'>";
$display_block .= "<p>Select a category to see its items.</p>";

// Get Categories
$get_cats_sql = "SELECT id, cat_title, cat_desc FROM store_categories ORDER BY cat_title";
$get_cats_res = mysqli_query($mysqli, $get_cats_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_cats_res) < 1) {
    $display_block .= "<p><em>Sorry, no categories to browse.</em></p>";
} else {
    while ($cats = mysqli_fetch_array($get_cats_res)) {
        $cat_id = $cats['id'];
        $cat_title = strtoupper(stripslashes($cats['cat_title']));
        $cat_desc = stripslashes($cats['cat_desc']);

        $display_block .= "<div style='margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 20px;'>
        <h3><a href=\"".$_SERVER['PHP_SELF']."?cat_id=".$cat_id."\">".$cat_title."</a></h3>
        <p>".$cat_desc."</p>";

        // If this category is selected, show its items
        if (isset($_GET['cat_id']) && ($_GET['cat_id'] == $cat_id)) {
            $safe_cat_id = mysqli_real_escape_string($mysqli, $_GET['cat_id']);
            $get_items_sql = "SELECT id, item_title, item_price, item_image FROM store_items WHERE cat_id = '".$cat_id."' ORDER BY item_title";
            $get_items_res = mysqli_query($mysqli, $get_items_sql) or die(mysqli_error($mysqli));

            if (mysqli_num_rows($get_items_res) < 1) {
                $display_block .= "<p><em>Sorry, no items in this category.</em></p>";
            } else {
                $display_block .= "<ul>";
                while ($items = mysqli_fetch_array($get_items_res)) {
                    $item_id = $items['id'];
                    $item_title = stripslashes($items['item_title']);
                    $item_price = $items['item_price'];
                    $item_image = $items['item_image'];

                    $display_block .= "<li>
                        <a href=\"showitem.php?item_id=".$item_id."\">
                        <img src=\"".$item_image."\" alt=\"".$item_title."\" />
                        <br/><strong>".$item_title."</strong>
                        </a>
                        <br/>$".$item_price."
                    </li>";
                }
                $display_block .= "</ul>";
            }
            mysqli_free_result($get_items_res);
        }
        $display_block .= "</div>"; // Close Category Div
    }
}
$display_block .= "</div>"; // Close Main Container

mysqli_free_result($get_cats_res);
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Categories</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php echo $display_block; ?>
</body>
</html>