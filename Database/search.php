<?php include 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Product</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .box { border: 1px solid black; width: 400px; padding: 10px; margin: 20px auto; }
        .box legend { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 6px; text-align: center; }
        a { color: blue; text-decoration: none; margin: 0 4px; }
    </style>
</head>
<body>
    <fieldset class="box">
        <legend>SEARCH</legend>
        <form method="GET">
            <input type="text" name="q" value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
            <input type="submit" value="Search By Name">
        </form>

        <table>
            <tr>
                <th>NAME</th>
                <th>PROFIT</th>
                <th></th>
            </tr>
            <?php
            if (isset($_GET['q'])) {
                $q = $_GET['q'];
                $sql = "SELECT * FROM products WHERE display='Yes' AND name LIKE '%$q%'";
            } else {
                $sql = "SELECT * FROM products WHERE display='Yes'";
            }

            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $profit = $row['selling_price'] - $row['buying_price'];
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$profit}</td>
                            <td>
                                <a href='edit.php?id={$row['id']}'>edit</a>
                                <a href='delete.php?id={$row['id']}'>delete</a>
                            </td>
                          </tr>";
                }
            }
            ?>
        </table>
    </fieldset>
</body>
</html>
