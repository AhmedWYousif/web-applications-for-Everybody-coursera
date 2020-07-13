<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// If the user requested cancel go back to index.php
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}


$stmt = $pdo->prepare("SELECT count(*) FROM Profile WHERE profile_id = :pro and user_id = :uid");
$stmt->execute(array(':uid' => $_SESSION['user_id'], ':pro' =>  $_REQUEST['profile_id']));

if ($stmt->fetchColumn() == 0) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['Save'])) {

    // Data validation
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1  || strlen($_POST['email']) < 1  || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    } else if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    } else {
        try {

            $sql = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he , summary = :su WHERE profile_id = :pro";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'],
                ':pro' =>  $_POST['profile_id']
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
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM `profile` where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}

$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ahmed W. Yousif's Editing Profile</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?= htmlentities($_SESSION['name'] ?? "")  ?></h1>
        <?php

        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }

        ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= $fname ?>"></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= $lname ?>"></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= $email ?>"></p>
            <p>Headline:<br>
                <input type="text" name="headline" size="80" value="<?= $headline ?>"></p>
            <p>Summary:<br>
                <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea>
            </p>
            <p>
                <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
                <input type="submit" name="Save" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

</html>