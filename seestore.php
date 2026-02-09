<?php
$mysqli = mysqli_connect("localhost","root","","testDB");

$display_block = "<h1>My Categories</h1><p>Select a category.</p>";

$get_cats_sql = "SELECT id, cat_title, cat_desc FROM store_categories ORDER BY cat_title";
$get_cats_res = mysqli_query($mysqli, $get_cats_sql);

if (mysqli_num_rows($get_cats_res) < 1) {
    $display_block .= "<p>No categories.</p>";
} else {
    while ($cat = mysqli_fetch_assoc($get_cats_res)) {
        $display_block .= "<p><strong>
        <a href='?cat_id={$cat['id']}'>{$cat['cat_title']}</a>
        </strong><br>{$cat['cat_desc']}</p>";

        if (isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['id']) {
            $get_items_sql = "SELECT id, item_title, item_price 
                              FROM store_items 
                              WHERE cat_id={$cat['id']}";
            $get_items_res = mysqli_query($mysqli, $get_items_sql);

            if (mysqli_num_rows($get_items_res) > 0) {
                $display_block .= "<ul>";
                while ($item = mysqli_fetch_assoc($get_items_res)) {
                    $display_block .= "<li>
                    <a href='showitem.php?item_id={$item['id']}'>
                    {$item['item_title']}</a>
                    (\${$item['item_price']})
                    </li>";
                }
                $display_block .= "</ul>";
            }
        }
    }
}
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<body>
<?= $display_block ?>
</body>
</html>
