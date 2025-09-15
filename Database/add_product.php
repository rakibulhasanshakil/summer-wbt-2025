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
    </style>
</head>
<body>
    <form method="POST">
        <fieldset class="box">
            <legend>ADD PRODUCT</legend>
            Name<br>
            <input type="text" name="name" required><br>
            Buying Price<br>
            <input type="number" step="0.01" name="buying_price" required><br>
            Selling Price<br>
            <input type="number" step="0.01" name="selling_price" required><br>
            <hr>
            <input type="checkbox" name="display" value="Yes"> Display
            <hr>
            <input type="submit" name="save" value="SAVE">
        </fieldset>
    </form>
</body>
</html>

<?php
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $buying = $_POST['buying_price'];
    $selling = $_POST['selling_price'];
    $display = isset($_POST['display']) ? 'Yes' : 'No';

    $sql = "INSERT INTO products (name, buying_price, selling_price, display) 
            VALUES ('$name','$buying','$selling','$display')";
    if ($conn->query($sql) === TRUE) {
        header("Location: display.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
