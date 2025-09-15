<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 350px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        .box hr { margin: 8px 0; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
<?php
$error = "";
$row = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = "Error: Product ID not provided.";
} else {
    $id = (int) $_GET['id']; // force integer
    $result = $conn->query("SELECT * FROM products WHERE id=$id");

    if ($result->num_rows == 0) {
        $error = "Error: Product not found.";
    } else {
        $row = $result->fetch_assoc();
    }
}

// Handle deletion
if (isset($_POST['delete']) && $row) {
    if ($conn->query("DELETE FROM products WHERE id=$id")) {
        header("Location: display.php");
        exit;
    } else {
        $error = "Error: Could not delete product. Please try again.";
    }
}
?>

<form method="POST">
    <fieldset class="box">
        <legend>DELETE PRODUCT</legend>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
            <br>
        <?php endif; ?>

        <?php if ($row): ?>
            Name: <?= htmlspecialchars($row['name']) ?><br>
            Buying Price: <?= htmlspecialchars($row['buying_price']) ?><br>
            Selling Price: <?= htmlspecialchars($row['selling_price']) ?><br>
            Displayable: <?= htmlspecialchars($row['display']) ?><br>
            <hr>
            <input type="submit" name="delete" value="Delete">
        <?php endif; ?>
    </fieldset>
</form>
</body>
</html>
