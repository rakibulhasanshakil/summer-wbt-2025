<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 300px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
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

if (isset($_POST['delete'])) {
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: display.php");
    exit;
}
?>
    <form method="POST">
        <fieldset class="box">
            <legend>DELETE PRODUCT</legend>
            Name: <?= $row['name'] ?><br>
            Buying Price: <?= $row['buying_price'] ?><br>
            Selling Price: <?= $row['selling_price'] ?><br>
            Displayable: <?= $row['display'] ?><br>
            <hr>
            <input type="submit" name="delete" value="Delete">
        </fieldset>
    </form>
</body>
</html>
