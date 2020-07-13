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


if (isset($_POST['add'])) {

    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['last_name'] = $_POST['last_name'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['headline'] = $_POST['headline'];
    $_SESSION['summary'] = $_POST['summary'];

    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1  || strlen($_POST['email']) < 1  || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    } else if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    } 
    else {
        try {

            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary']
            ));

            unset($_SESSION['first_name']);
            unset($_SESSION['last_name']);
            unset($_SESSION['email']);
            unset($_SESSION['headline']);
            unset($_SESSION['summary']);


            $_SESSION['success'] = "Record Added";
            header("Location: index.php");
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
    <title>Ahmed W. Yousif's Profile Add</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>

    <div class="container">
        <h1>Adding Profile for <?= htmlentities($_SESSION['name'] ?? "")  ?></h1>
        <?php

        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }

        ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= htmlentities($_SESSION['first_name'] ?? '') ?>"></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= htmlentities($_SESSION['last_name'] ?? '') ?>"></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= htmlentities($_SESSION['email'] ?? '') ?>"></p>
            <p>Headline:<br>
                <input type="text" name="headline" size="80" value="<?= htmlentities($_SESSION['headline'] ?? '') ?>"></p>
            <p>Summary:<br>
                <textarea name="summary" rows="8" cols="80" value="<?= htmlentities($_SESSION['summary'] ?? '') ?>"></textarea>
            </p>
            <p>
                <input type="submit" name="add" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

</html>