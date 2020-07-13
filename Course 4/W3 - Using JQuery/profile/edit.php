<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Guardian: Make sure that autos_id is present
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

// If the user requested cancel go back to index.php
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

function validatePos()
{
    if (isset($_POST['position'])) {
        foreach ($_POST['position'] as $rank => $value) {
            $year = $value['year'];
            $desc =  $value['description'];
            if (strlen($year) == 0 || strlen($desc) == 0) {
                return "All fields are required";
            }

            if (!is_numeric($year)) {
                return "Position year must be numeric";
            }
        }
    }
    return true;
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
    } else if (($res = validatePos()) !== true) {
        $_SESSION['error'] = $res;
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


            // Clear out the old position entries
            $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
            $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

            // Insert the position entries
            if (isset($_POST['position'])) {
                foreach ($_POST['position'] as $rank => $value) {
                    $stmt = $pdo->prepare('INSERT INTO position (profile_id, `rank`, year, description) VALUES ( :pid, :rank, :year, :desc)');
                    $stmt->execute(array(
                        ':pid' => $_POST['profile_id'],
                        ':rank' => $rank,
                        ':year' => $value['year'],
                        ':desc' => $value['description']
                    ));
                }
            }

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

$stmt = $pdo->prepare("SELECT * FROM `position` where profile_id = :profile_id order by `rank`");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$position = $stmt->fetchAll();

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
            <p> Position: <input type="submit" id="addPos" value="+"></p>
            <div id="position_fields">
                <?php
                if (isset($position)) {
                    foreach ($position as $rank => $value) {
                        $year = htmlentities($value['year'] ?? '');
                        $desc =  htmlentities($value['description'] ?? '');
                        //$rank =  strval($index);
                        echo '<div id="position' . $rank . '">';
                        echo   '<p>Year: <input type="text" name="position[' . $rank . '][year]" value="' . $year . '" />';
                        echo   '<input type="button" value="-" onclick="$(\'#position' . $rank . '\').remove(); count--; reindex(); return false;"></p>';
                        echo '<textarea name="position[' . $rank . '][description]" rows="8" cols="80">' . $desc . '</textarea>';
                        echo '</div>';
                    }
                }

                ?>

            </div>
            <p>
            <p>
                <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
                <input type="submit" name="Save" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
        <script>
            pos = <?= count($position ?? []) ?>;
            count = <?= count($position ?? []) ?>;

            function reindex() {
                $("div#position_fields div").each(function(i) {
                    $(this).find('input[type="text"]').attr('name', `position[${i}][year]`);
                    $(this).find('textarea').attr('name', `position[${i}][description]`);
                    console.log($(this).find('input[type="text"]').attr('name', `position[${i}][year]`), i);
                    console.log($(this).find('textarea').attr('name'), i);
                });

            }

            $(document).ready(function() {
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event) {
                    event.preventDefault();
                    if (count >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }

                    window.console && console.log(`Adding position ${pos}`);
                    $('#position_fields').append(`<div id="position${pos}">
                                                        <p>Year: <input type="text" name="position[${count}][year]" value="" />
                                                        <input type="button" value="-" onclick="$('#position${pos}').remove(); count--; reindex(); return false;"></p>
                                                        <textarea name="position[${count}][description]" rows="8" cols="80"></textarea>
                                                  </div>`);
                    pos++;
                    count++;
                });
            });
        </script>
    </div>
</body>

</html>