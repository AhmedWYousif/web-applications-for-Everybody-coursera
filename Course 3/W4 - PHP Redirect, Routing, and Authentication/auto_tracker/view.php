<?php

session_start();

include "pdo.php";

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

try {
    $stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    echo ("Internal error, please contact support");
    error_log("view.php, SQL error=" . $ex->getMessage());
    return;
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Ahmed W. Yousif's Automobile Tracker</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">

        <h1>Tracking Autos for <?php htmlentities($_SESSION['name'] ?? "") ?></h1>

        <?php

        if (isset($_SESSION['success'])) {
            echo ('<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n");
            unset($_SESSION['success']);
        }

        ?>

        <h2>Automobiles</h2>
        <ul>
            <?php
            foreach ($rows as $row) {
                echo "<li>";
                echo htmlentities("{$row['year']} {$row['make']} / {$row['mileage']}");
                echo ("</li>");
            }
            ?>
        </ul>
        <p>
            <a href="add.php">Add New</a> |
            <a href="logout.php">Logout</a>
        </p>

    </div>
</body>

</html>