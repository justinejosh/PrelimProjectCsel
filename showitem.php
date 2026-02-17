<?php
session_start();
$mysqli = mysqli_connect("localhost", "root", "", "testDB");

// Header with View Cart
$display_block = "
<header>
    <h1>Item Detail</h1>
    <a href='showcart.php' class='btn-cart'>View Cart 🛒</a>
</header>";

// Check if ID exists
if (!isset($_GET['item_id'])) {
    header("Location: seestore.php");
    exit;
}

$safe_item_id = mysqli_real_escape_string($mysqli, $_GET['item_id']);

// Get Item Info
$get_item_sql = "SELECT c.id as cat_id, c.cat_title, si.item_title, si.item_price, si.item_desc, si.item_image FROM store_items AS si LEFT JOIN store_categories AS c on c.id = si.cat_id WHERE si.id = '".$safe_item_id."'";
$get_item_res = mysqli_query($mysqli, $get_item_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_item_res) < 1) {
    $display_block .= "<p><em>Invalid item selection.</em></p>";
} else {
    // Fetch data first
    while ($item_info = mysqli_fetch_array($get_item_res)) {
        $cat_id = $item_info['cat_id'];
        $cat_title = strtoupper(stripslashes($item_info['cat_title']));
        $item_title = stripslashes($item_info['item_title']);
        $item_price = $item_info['item_price'];
        $item_desc = stripslashes($item_info['item_desc']);
        $item_image = $item_info['item_image'];
    }
    mysqli_free_result($get_item_res); // Free this result now that we have the data

    // Start building the HTML
    $display_block .= "
    <div style='max-width: 800px; margin: 0 auto;'>
    <p><em>You are viewing:</em> <strong><a href='seestore.php?cat_id=$cat_id'>$cat_title</a> &gt; $item_title</strong></p>
    
    <div class='item-container'>
        <div class='item-image'>
            <img id='mainImage' src='$item_image' alt='$item_title' />
        </div>
        <div class='item-info'>
            <p><strong>Description:</strong><br/>$item_desc</p>
            <p><strong>Price:</strong> \$$item_price</p>
            <form method='post' action='addtocart.php'>";

    // Colors
    $get_colors_sql = "SELECT item_color FROM store_item_color WHERE item_id = '".$safe_item_id."' ORDER BY item_color";
    $get_colors_res = mysqli_query($mysqli, $get_colors_sql) or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_colors_res) > 0) {
        $display_block .= "<p><label for='sel_item_color'>Available Colors:</label>
        <select id='sel_item_color' name='sel_item_color'>";
        while ($colors = mysqli_fetch_array($get_colors_res)) {
            $item_color = $colors['item_color'];
            $display_block .= "<option value='".$item_color."'>".$item_color."</option>";
        }
        $display_block .= "</select></p>";
    }
    mysqli_free_result($get_colors_res);

    // Sizes
    $get_sizes_sql = "SELECT item_size FROM store_item_size WHERE item_id = ".$safe_item_id." ORDER BY item_size";
    $get_sizes_res = mysqli_query($mysqli, $get_sizes_sql) or die(mysqli_error($mysqli));

    if (mysqli_num_rows($get_sizes_res) > 0) {
        $display_block .= "<p><label for='sel_item_size'>Available Sizes:</label>
        <select id='sel_item_size' name='sel_item_size'>";
        while ($sizes = mysqli_fetch_array($get_sizes_res)) {
            $item_size = $sizes['item_size'];
            $display_block .= "<option value='".$item_size."'>".$item_size."</option>";
        }
        $display_block .= "</select></p>";
    }
    mysqli_free_result($get_sizes_res);

    // Quantity & Buttons
    $display_block .= "
        <p><label for='sel_item_qty'>Select Quantity:</label>
        <select id='sel_item_qty' name='sel_item_qty'>";

    for($i=1; $i<11; $i++) {
        $display_block .= "<option value='".$i."'>".$i."</option>";
    }

    $display_block .= "
        </select></p>
        <input type='hidden' name='sel_item_id' value='$safe_item_id' />
        
        <a href='seestore.php' class='btn btn-back'>Back to Store</a>
        <button type='submit' name='submit' value='submit'>Add to Cart</button>
        </form>
        </div>
    </div>
    </div>";

    // --- INTERACTIVE SCRIPT START (Change Hat Color) ---
    // Only for Baseball Hat (ID 1)
    if ($safe_item_id == 1) {
        $display_block .= "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var colorSelect = document.getElementById('sel_item_color');
            var mainImage = document.getElementById('mainImage');
            
            // Define URLs for each color
            var hatImages = {
                'red': 'https://images.unsplash.com/photo-1588850561407-ed78c282e89f?w=300&q=80',
                'black': 'https://images.unsplash.com/photo-1556306535-0f09a537f0a3?w=300&q=80',
                'blue': 'https://images.unsplash.com/photo-1533827432537-70133748f5c8?w=300&q=80'
            };

            if(colorSelect && mainImage) {
                colorSelect.addEventListener('change', function() {
                    var selectedColor = this.value;
                    if(hatImages[selectedColor]) {
                        mainImage.src = hatImages[selectedColor];
                    }
                });
            }
        });
        </script>";
    }
    // --- INTERACTIVE SCRIPT END ---
}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>Item Detail</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php echo $display_block; ?>
</body>
</html>