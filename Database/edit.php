<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 300px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        .box input[type=text], 
        .box input[type=number] { width: 95%; margin: 4px 0; }
        .box hr { margin: 8px 0; }
    </style>
</head>
<body>
<?php
if (!isset($_GET['id'])) {
    die("Error: Product ID not provided in URL.");
}

$id = (int) $_GET['id']; // force integer
$result = $conn->query("SELECT * FROM products WHERE id=$id");

if ($result->num_rows == 0) {
    die("Error: Product not found.");
}

$row = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $buying = $_POST['buying_price'];
    $selling = $_POST['selling_price'];
    $display = isset($_POST['display']) ? 'Yes' : 'No';

    $conn->query("UPDATE products 
                  SET name='$name', buying_price='$buying', selling_price='$selling', display='$display' 
                  WHERE id=$id");

    header("Location: display.php");
    exit;
}
?>
    <form method="POST">
        <fieldset class="box">
            <legend>EDIT PRODUCT</legend>
            Name<br>
            <input type="text" name="name" value="<?= $row['name'] ?>" required><br>
            Buying Price<br>
            <input type="number" step="0.01" name="buying_price" value="<?= $row['buying_price'] ?>" required><br>
            Selling Price<br>
            <input type="number" step="0.01" name="selling_price" value="<?= $row['selling_price'] ?>" required><br>
            <hr>
            <input type="checkbox" name="display" value="Yes" <?= ($row['display']=='Yes')?'checked':'' ?>> Display
            <hr>
            <input type="submit" name="update" value="SAVE">
        </fieldset>
    </form>
</body>
</html>
