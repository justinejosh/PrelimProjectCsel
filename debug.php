<?php
$mysqli = mysqli_connect("localhost", "root", "", "testDB");
$result = mysqli_query($mysqli, "SELECT id, item_title, item_image FROM store_items");

echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Image URL (Check this!)</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['item_title'] . "</td>";
    echo "<td>" . $row['item_image'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>