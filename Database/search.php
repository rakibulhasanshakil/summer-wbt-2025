<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 500px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 6px; text-align: center; }
        a { color: blue; text-decoration: none; margin: 0 4px; }
        .error { color: red; font-size: 13px; margin-top: 4px; }
        .note { font-style: italic; margin-top: 10px; }
    </style>
</head>
<body>
    <fieldset class="box">
        <legend>SEARCH PRODUCT</legend>

        <?php
        $error = "";
        $products = [];

        if (isset($_GET['q'])) {
            $q = trim($_GET['q']);

            if (empty($q)) {
                $error = "Please enter a product name to search.";
            } else {
                // Prepared statement for safety
                $stmt = $conn->prepare("SELECT * FROM products WHERE display='Yes' AND name LIKE ?");
                $search = "%{$q}%";
                $stmt->bind_param("s", $search);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $products = $result->fetch_all(MYSQLI_ASSOC);
                } else {
                    $error = "No products found matching '$q'.";
                }
                $stmt->close();
            }
        } else {
            // Show all products by default
            $result = $conn->query("SELECT * FROM products WHERE display='Yes'");
            if ($result && $result->num_rows > 0) {
                $products = $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        ?>

        <form method="GET">
            <input type="text" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <input type="submit" value="Search By Name">
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
        </form>

        <?php if (!empty($products)): ?>
        <table>
            <tr>
                <th>NAME</th>
                <th>PROFIT</th>
                <th>ACTIONS</th>
            </tr>
            <?php foreach ($products as $row): 
                $profit = $row['selling_price'] - $row['buying_price'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($profit) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>">edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>">delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </fieldset>
</body>
</html>
