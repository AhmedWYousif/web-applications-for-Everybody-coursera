<?php

include "pdo.php";

// Demand a GET parameter
if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name parameter missing");
}

// If the user requested logout go back to index.php
if (isset($_POST['logout'])) {
    header('Location: index.php');
    return;
}


$failure = false;  // If we have no POST data
$success = false;

if (isset($_POST['Add'])) {
    if (strlen($_POST['make']) < 1) {
        $failure = "Make is required";
    } else if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $failure = "Mileage and year must be numeric";
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES ( :mk, :yr, :mi)');
            $stmt->execute(array(':mk' => $_POST['make'], ':yr' => $_POST['year'], ':mi' => $_POST['mileage']));
            $success = "Record inserted";
            $_POST['year'] = '';
            $_POST['make'] ='';
            $_POST['mileage'] ='';
        } catch (Exception $ex) {
            echo ("Internal error, please contact support");
            error_log("error4.php, SQL error=" . $ex->getMessage());
            return;
        }
    }
}


$stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html>

<head>
    <title>Ahmed W. Yousif's Automobile Tracker</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">

        <h1>Tracking Autos for <?php echo isset($_REQUEST['name']) ? htmlentities($_REQUEST['name']) : ""  ?></h1>
        <?php
        // Note triple not equals and think how badly double
        // not equals would work here...
        if ($failure !== false) {
            echo ('<p style="color: red;">' . htmlentities($failure) . "</p>\n");
        } else if ($success !== false) {
            echo ('<p style="color: green;">' . htmlentities($success) . "</p>\n");
        }
        ?>

        <form method="post">
            <p>Make:
                <input type="text" name="make" size="60" value="<?= htmlentities($_POST['make'] ?? '') ?>"></p>
            <p>Year:
                <input type="text" name="year" value="<?= htmlentities($_POST['year'] ?? '') ?>"></p>
            <p>Mileage:
                <input type="text" name="mileage" value="<?= htmlentities($_POST['mileage'] ?? '') ?>"></p>
            <input type="submit" name="Add" value="Add">
            <input type="submit" name="logout" value="Logout">
        </form>

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


    </div>
</body>

</html>