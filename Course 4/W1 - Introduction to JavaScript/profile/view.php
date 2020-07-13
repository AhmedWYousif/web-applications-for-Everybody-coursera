<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Guardian: Make sure that profile_id is present
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
    <title>Ahmed W. Yousif's Profile View</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Profile information</h1>
        <p>First Name: <?= $fname ?></p>
        <p>Last Name: <?= $lname ?></p>
        <p>Email: <?= $email ?></p>
        <p>Headline:<br><?= $headline ?></p>
        <p>Summary:<br><?= $summary ?></p>
        <p>
        </p>
        <a href="index.php">Done</a>
    </div>
</body>

</html>