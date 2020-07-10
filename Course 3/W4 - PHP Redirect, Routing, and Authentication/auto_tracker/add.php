<?php

session_start();

include "pdo.php";

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

// If the user requested logout go back to index.php
if (isset($_POST['logout'])) {
    header('Location: logout.php');
    return;
}


if (isset($_POST['Add'])) {
    $_SESSION['make'] = $_POST['make'];
    $_SESSION['year'] = $_POST['year'];
    $_SESSION['mileage'] = $_POST['mileage'];

    if (strlen($_POST['make']) < 1) {
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
        return;
    } else if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES ( :mk, :yr, :mi)');
            $stmt->execute(array(':mk' => $_POST['make'], ':yr' => $_POST['year'], ':mi' => $_POST['mileage']));

            unset($_SESSION['make']);
            unset($_SESSION['year']);
            unset($_SESSION['mileage']);
            

            $_SESSION['success'] = "Record inserted";
            header("Location: view.php");
            return;
        } catch (Exception $ex) {
            echo ("Internal error, please contact support");
            error_log("add.php, SQL error=" . $ex->getMessage());
            return;
        }
    }
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

        <h1>Tracking Autos for <?php echo htmlentities($_SESSION['name'] ?? "")  ?></h1>
        <?php

        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }

        ?>

        <form method="post">
            <p>Make:
                <input type="text" name="make" size="60" value="<?= htmlentities($_SESSION['make'] ?? '') ?>"></p>
            <p>Year:
                <input type="text" name="year" value="<?= htmlentities($_SESSION['year'] ?? '') ?>"></p>
            <p>Mileage:
                <input type="text" name="mileage" value="<?= htmlentities($_SESSION['mileage'] ?? '') ?>"></p>
            <input type="submit" name="Add" value="Add">
            <input type="submit" name="logout" value="Logout">
        </form>

    </div>
</body>

</html>