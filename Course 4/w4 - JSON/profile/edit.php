<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Guardian: Make sure that profile_id is present
if (!isset($_REQUEST['profile_id'])) {
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

function validateEdu()
{
    if (isset($_POST['education'])) {
        foreach ($_POST['education'] as $rank => $value) {
            $year = $value['year'];
            $school =  $value['school'];
            if (strlen($year) == 0 || strlen($school) == 0) {
                return "All fields are required";
            }

            if (!is_numeric($year)) {
                return "Education year must be numeric";
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
    } else if (($res = validateEdu()) !== true) {
        $_SESSION['error'] = $res;
        header("Location: add.php");
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

            // Clear out the old educations entries
            $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
            $stmt->execute(array(':pid' => $_POST['profile_id']));

            if (isset($_POST['education'])) {
                foreach ($_POST['education'] as $rank => $value) {
                    $stmt = $pdo->prepare("SELECT `institution_id` FROM `institution` WHERE name = :school");
                    $stmt->execute(array(':school' =>  $value['school']));
                    $institution_id = $stmt->fetchColumn();

                    if($institution_id === false){
                        $stmt = $pdo->prepare("INSERT INTO `institution` (`name`) VALUES (:school)");
                        $stmt->execute(array(':school' =>  $value['school']));
                        $institution_id = $pdo->lastInsertId();
                    }

                    $stmt = $pdo->prepare('INSERT INTO `education`(`profile_id`, `institution_id`, `rank`, `year`) VALUES ( :pid,:sid, :rank, :year)');
                    $stmt->execute(array(
                        ':pid' => $_POST['profile_id'],
                        ':sid' => $institution_id,
                        ':rank' => $rank,
                        ':year' => $value['year']
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



$stmt = $pdo->prepare("SELECT * FROM `profile` where profile_id = :proId");
$stmt->execute(array(":proId" => $_GET['profile_id']));
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
    <title>Ahmed W. Yousif's Editing Profile</title>
    <?php require_once "head.php"; ?>
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
            <p> Education: <input type="submit" id="addEdu" value="+"> </p>
            <div id="education_fields">
                <?php
                if (isset($education)) {
                    foreach ($education as $rank => $value) {
                        $year = htmlentities($value['year'] ?? '');
                        $school =  htmlentities($value['school'] ?? '');
                        //$rank =  strval($index);
                        echo '<div id="education' . $rank . '">';
                        echo   '<p>Year: <input type="text" name="education[' . $rank . '][year]" value="' . $year . '" />';
                        echo   '<input type="button" value="-" onclick="$(\'#education' . $rank . '\').remove(); count--; reindex(); return false;"></p>';
                        echo   '<p>School: <input type="text" size="80" name="education[' . $rank . '][school]" class="school" value="' . $school . '" autocomplete="off"></p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
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

            pos_edu = <?= count($education ?? []) ?>;
            count_edu = <?= count($education ?? []) ?>;


            function reindex() {
                $("div#position_fields div").each(function(i) {
                    $(this).find('input[type="text"]').attr('name', `position[${i}][year]`);
                    $(this).find('textarea').attr('name', `position[${i}][description]`);
                });
            }

            function reindex_edu() {
                $("div#education_fields div").each(function(i) {
                    $(this).find('input[type="text"].year').attr('name', `education[${i}][year]`);
                    $(this).find('input[type="text"].school').attr('name', `education[${i}][school]`);
                });
            }

            $(document).ready(function() {
                window.console && console.log('Document ready called');

                $('.school').autocomplete({
                    source: "school.php"
                });

                $('#addEdu').click(function(event) {
                    event.preventDefault();
                    if (count_edu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }

                    window.console && console.log(`Adding education ${pos_edu}`);
                    $('#education_fields').append(`<div id="education${pos_edu}">
                                                        <p>Year: <input type="text" name="education[${count_edu}][year]" value="" class="year" />
                                                        <input type="button" value="-" onclick="$('#education${pos_edu}').remove(); count--; reindex_edu(); return false;"></p>
                                                        <p>School: <input type="text" size="80" name="education[${count_edu}][school]" class="school" value="" autocomplete="off"></p>
                                                  </div>`);
                    $('.school').autocomplete({
                        source: "school.php"
                    });

                    pos_edu++;
                    count_edu++;
                });

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