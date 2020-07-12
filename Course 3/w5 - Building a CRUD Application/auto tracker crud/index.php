<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ahmed W. Yousif - Autos Database</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h2>Welcome to the Automobiles Database</h2>

        <?php
        if (isset($_SESSION['name'])) {

            if (isset($_SESSION['error'])) {
                echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p style="color:green">' . $_SESSION['success'] . "</p>\n";
                unset($_SESSION['success']);
            }

            if ($count = $pdo->query("SELECT COUNT(*) FROM autos")) {
                if ($count->fetchColumn() > 0) {
                    echo ('<table border="1">');
                    echo ('<thead><tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr></thead>');
                    echo ('<tbody>');
                    $stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos ORDER BY make");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr><td>" . (htmlentities($row['make'])) . "</td>";
                        echo "<td>" . (htmlentities($row['model'])) . "</td>";
                        echo "<td>" . (htmlentities($row['year'])) . "</td>";
                        echo "<td>" . (htmlentities($row['mileage'])) . "</td>";
                        echo ('<td><a href="edit.php?autos_id=' . $row['autos_id'] . '">Edit</a> / ');
                        echo ('<a href="delete.php?autos_id=' . $row['autos_id'] . '">Delete</a></td></tr>');
                    }
                    echo ('</tbody>');
                    echo ('</table>');
                } else {
                    echo ('<p>No rows found</p>');
                }

                echo  '<p><a href="add.php">Add New Entry</a></p>';
                echo  '<p><a href="logout.php">Logout</a></p>';
            }
        } else {
            echo  '<p><a href="login.php">Please log in</a></p>';
            echo  '<p>Attempt to <a href="add.php">add data</a> without logging in</p>';
        } ?>
    </div>
</body>