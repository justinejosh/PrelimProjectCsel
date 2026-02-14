<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Checkout</title>
</head>
<body>

<h1>Checkout</h1>

<form method="post" action="processorder.php">

<p>
  <label>Name:</label><br>
  <input type="text" name="order_name" required>
</p>

<p>
  <label>Address:</label><br>
  <input type="text" name="order_address" required>
</p>

<p>
  <label>City:</label><br>
  <input type="text" name="order_city" required>
</p>

<p>
  <label>State:</label><br>
  <input type="text" name="order_state" maxlength="2" required>
</p>

<p>
  <label>Zip Code:</label><br>
  <input type="text" name="order_zip" required>
</p>

<p>
  <label>Phone:</label><br>
  <input type="text" name="order_tel" required>
</p>

<p>
  <label>Email:</label><br>
  <input type="email" name="order_email" required>
</p>
<p>
  <button onclick="history.back()">⬅ Back</button>
</p>
<button type="submit">Place Order</button>

</form>

</body>
</html>
