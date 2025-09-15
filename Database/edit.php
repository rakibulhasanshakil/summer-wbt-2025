<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 350px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        .box input[type=text], 
        .box input[type=number] { width: 95%; margin: 4px 0; }
        .box hr { margin: 8px 0; }
        .error { color: red; font-size: 13px; }
    </style>
</head>
<body>
<?php
$error_name = $error_buying = $error_selling = $general_error = "";
$row = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $general_error = "Product ID not provided in URL.";
} else {
    $id = (int) $_GET['id'];
    $result = $conn->query("SELECT * FROM products WHERE id=$id");
    if ($result->num_rows == 0) {
        $general_error = "Product not found.";
    } else {
        $row = $result->fetch_assoc();
    }
}

if (isset($_POST['update']) && $row) {
    $name = trim($_POST['name']);
    $buying = $_POST['buying_price'];
    $selling = $_POST['selling_price'];
    $display = isset($_POST['display']) ? 'Yes' : 'No';

    // Validation
    $valid = true;
    if (empty($name)) {
        $error_name = "Name is required.";
        $valid = false;
    }

    if (!is_numeric($buying) || $buying < 0) {
        $error_buying = "Buying price must be a positive number.";
        $valid = false;
    }

    if (!is_numeric($selling) || $selling < 0) {
        $error_selling = "Selling price must be a positive number.";
        $valid = false;
    }

    if ($valid) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE products SET name=?, buying_price=?, selling_price=?, display=? WHERE id=?");
        $stmt->bind_param("sddsi", $name, $buying, $selling, $display, $id);
        if ($stmt->execute()) {
            header("Location: display.php");
            exit;
        } else {
            $general_error = "Failed to update product. Try again.";
        }
        $stmt->close();
    }
}
?>

<form method="POST">
    <fieldset class="box">
        <legend>EDIT PRODUCT</legend>

        <?php if ($general_error): ?>
            <div class="error"><?= $general_error ?></div><br>
        <?php endif; ?>

        <?php if ($row): ?>
            Name<br>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"><br>
            <span class="error"><?= $error_name ?></span><br>

            Buying Price<br>
            <input type="number" step="0.01" name="buying_price" value="<?= htmlspecialchars($row['buying_price']) ?>"><br>
            <span class="error"><?= $error_buying ?></span><br>

            Selling Price<br>
            <input type="number" step="0.01" name="selling_price" value="<?= htmlspecialchars($row['selling_price']) ?>"><br>
            <span class="error"><?= $error_selling ?></span><br>

            <hr>
            <input type="checkbox" name="display" value="Yes" <?= ($row['display']=='Yes')?'checked':'' ?>> Display
            <hr>
            <input type="submit" name="update" value="SAVE">
        <?php endif; ?>
    </fieldset>
</form>
</body>
</html>
