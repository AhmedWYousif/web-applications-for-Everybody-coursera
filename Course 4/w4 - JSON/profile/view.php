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

$stmt = $pdo->prepare("SELECT * FROM `profile` where profile_id = :profile_id");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
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

$stmt = $pdo->prepare("SELECT * FROM `position` where profile_id = :profile_id order by `rank`");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$position = $stmt->fetchAll();


$stmt = $pdo->prepare("SELECT `profile_id`, `rank`, `year`, `institution`.`name` as 'school' FROM `education` inner join `institution` on `institution`.`institution_id` = `education`.`institution_id` WHERE profile_id = :profile_id order by `rank`");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$education = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ahmed W. Yousif's Profile View</title>
    <?php require_once "head.php"; ?>
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

        <p>Education</p>
        <ul>
            <?php

            foreach ($education as $row) {
                $year = htmlentities($row['year'] ?? '');
                $school =  htmlentities($row['school'] ?? '');
                echo '<li>' . $year . ': ' . $school . '</li>';
            }

            ?>
        </ul>

        <p>Position</p>
        <ul>
            <?php

            foreach ($position as $row) {
                $year = htmlentities($row['year'] ?? '');
                $desc =  htmlentities($row['description'] ?? '');
                echo '<li>' . $year . ': ' . $desc . '</li>';
            }

            ?>
        </ul>


        <a href="index.php">Done</a>
    </div>
</body>

</html>