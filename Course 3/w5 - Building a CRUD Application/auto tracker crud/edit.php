<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['Save'])) {

    // Data validation
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1  || strlen($_POST['year']) < 1  || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?autos_id=" . $_POST['autos_id']);
        return;
    } else if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: edit.php?autos_id=" . $_POST['autos_id']);
        return;
    } else {
        try {

            $sql = "UPDATE autos SET make = :make, model = :model, year = :year, mileage = :mileage WHERE autos_id = :autos_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':make' => $_POST['make'],
                ':model' => $_POST['model'],
                ':year' => $_POST['year'],
                ':mileage' => $_POST['mileage'],
                ':autos_id' => $_POST['autos_id']
            ));
            $_SESSION['success'] = 'Record updated';
            header('Location: index.php');
            return;
        } catch (Exception $ex) {
            error_log("add.php, SQL error=" . $ex->getMessage());
            $_SESSION['error'] = "Internal error, please contact support";
            header("Location: edit.php?autos_id=" . $_POST['autos_id']);
            return;
        }
    }
}

// Guardian: Make sure that autos_id is present
if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header('Location: index.php');
    return;
}


$make = htmlentities($row['make']);
$model = htmlentities($row['model']);
$year = htmlentities($row['year']);
$mileage = htmlentities($row['mileage']);
$autos_id = $row['autos_id'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ahmed W. Yousif's Automobile Tracker</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Editing Automobile</h1>
        <?php

        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }

        ?>
        <form method="post">
            <p>Make:
                <input type="text" name="make" size="60" value="<?= $make ?>"></p>
            <p>Model:
                <input type="text" name="model" size="60" value="<?= $model ?>"></p>
            <p>Year:
                <input type="text" name="year" value="<?= $year ?>"></p>
            <p>Mileage:
                <input type="text" name="mileage" value="<?= $mileage ?>"></p>
            <input type="hidden" name="autos_id" value="<?= $autos_id ?>">
            <p><input type="submit" name="Save" value="Save" />
                <a href="index.php">Cancel</a></p>
        </form>
        <p>
        </p>
    </div>
</body>

</html>