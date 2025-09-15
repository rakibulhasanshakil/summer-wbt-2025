<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 300px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        .box input[type=text], 
        .box input[type=number] { width: 95%; margin: 4px 0; }
        .box hr { margin: 8px 0; }
        .error { color: red; font-size: 14px; margin: 2px 0; }
    </style>
</head>
<body>
<?php
// initialize variables
$errors = ['name'=>'','buying'=>'','selling'=>''];
$name = $buying = $selling = $display = "";

if (isset($_POST['save'])) {
    // Sanitize input
    $name = trim($_POST['name']);
    $buying = trim($_POST['buying_price']);
    $selling = trim($_POST['selling_price']);
    $display = isset($_POST['display']) ? 'Yes' : 'No';

    // Validation
    if (empty($name)) {
        $errors['name'] = "Product name is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9\s]+$/", $name)) {
        $errors['name'] = "Name can only contain letters, numbers, and spaces.";
    }

    if ($buying === "") {
        $errors['buying'] = "Buying price is required.";
    } elseif (!is_numeric($buying) || $buying <= 0) {
        $errors['buying'] = "Buying price must be a positive number.";
    }

    if ($selling === "") {
        $errors['selling'] = "Selling price is required.";
    } elseif (!is_numeric($selling) || $selling <= 0) {
        $errors['selling'] = "Selling price must be a positive number.";
    }

    // If no errors, insert into DB
    if (empty($errors['name']) && empty($errors['buying']) && empty($errors['selling'])) {
        $stmt = $conn->prepare("INSERT INTO products (name, buying_price, selling_price, display) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdds", $name, $buying, $selling, $display);

        if ($stmt->execute()) {
            header("Location: display.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

    <form method="POST">
        <fieldset class="box">
            <legend>ADD PRODUCT</legend>
            
            Name<br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
            <?php if ($errors['name']) echo "<span class='error'>{$errors['name']}</span><br>"; ?>

            Buying Price<br>
            <input type="number" step="0.01" name="buying_price" value="<?php echo htmlspecialchars($buying); ?>" required><br>
            <?php if ($errors['buying']) echo "<span class='error'>{$errors['buying']}</span><br>"; ?>

            Selling Price<br>
            <input type="number" step="0.01" name="selling_price" value="<?php echo htmlspecialchars($selling); ?>" required><br>
            <?php if ($errors['selling']) echo "<span class='error'>{$errors['selling']}</span><br>"; ?>

            <hr>
            <input type="checkbox" name="display" value="Yes" <?php if ($display=='Yes') echo "checked"; ?>> Display
            <hr>
            <input type="submit" name="save" value="SAVE">
        </fieldset>
    </form>
</body>
</html>
