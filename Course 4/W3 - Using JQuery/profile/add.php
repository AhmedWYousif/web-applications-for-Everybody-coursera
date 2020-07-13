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

function validatePos()
{
    if (isset($_POST['position'])) {
        foreach ($_POST['position'] as $rank => $value) {
            $year = $value['year'];
            $desc =  $value['desc'];
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

if (isset($_POST['add'])) {

    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['last_name'] = $_POST['last_name'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['headline'] = $_POST['headline'];
    $_SESSION['summary'] = $_POST['summary'];
    $_SESSION['position'] = $_POST['position'];


    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1  || strlen($_POST['email']) < 1  || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    } else if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    } else if (($res = validatePos()) !== true) {
        $_SESSION['error'] = $res;
        header("Location: add.php");
        return;
    } else {
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

            $profile_id = $pdo->lastInsertId();

            if (isset($_POST['position'])) {
                foreach ($_POST['position'] as $rank => $value) {
                    $stmt = $pdo->prepare('INSERT INTO position (profile_id, `rank`, year, description) VALUES ( :pid, :rank, :year, :desc)');
                    $stmt->execute(array(
                        ':pid' => $profile_id,
                        ':rank' => $rank,
                        ':year' => $value['year'],
                        ':desc' => $value['desc']
                    ));
                }
            }



            unset($_SESSION['first_name']);
            unset($_SESSION['last_name']);
            unset($_SESSION['email']);
            unset($_SESSION['headline']);
            unset($_SESSION['summary']);
            unset($_SESSION['position']);

            $_SESSION['success'] = "Record Added";
            header("Location: index.php");
            return;
        } catch (Exception $ex) {
            $_SESSION['error'] = "Internal error, please contact support";
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
                <textarea name="summary" rows="8" cols="80" ><?= htmlentities($_SESSION['summary'] ?? '') ?></textarea>
            </p>
            <p> Position: <input type="submit" id="addPos" value="+"></p>
            <div id="position_fields">
                <?php
                if (isset($_SESSION['position'])) {
                    foreach ($_SESSION['position'] as $rank => $value) {
                        $year = htmlentities($value['year'] ?? '');
                        $desc =  htmlentities($value['desc'] ?? '');
                        //$rank =  strval($index);
                        echo '<div id="position' . $rank . '">';
                        echo   '<p>Year: <input type="text" name="position[' . $rank . '][year]" value="' . $year . '" />';
                        echo   '<input type="button" value="-" onclick="$(\'#position' . $rank . '\').remove(); count--; reindex(); return false;"></p>';
                        echo '<textarea name="position[' . $rank . '][desc]" rows="8" cols="80">' . $desc . '</textarea>';
                        echo '</div>';
                    }
                }

                ?>

            </div>
            <p>
                <input type="submit" name="add" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
        <script>
            pos = <?= count($_SESSION['position'] ?? []) ?>;
            count = <?= count($_SESSION['position'] ?? []) ?>;

            function reindex() {
                $("div#position_fields div").each(function(i) {
                    $(this).find('input[type="text"]').attr('name', `position[${i}][year]`);
                    $(this).find('textarea').attr('name', `position[${i}][desc]`);
                    console.log($(this).find('input[type="text"]').attr('name', `position[${i}][year]`), i);
                    console.log($(this).find('textarea').attr('name'), i);
                });

            }


            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
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
                                                        <textarea name="position[${count}][desc]" rows="8" cols="80"></textarea>
                                                  </div>`);
                    pos++;
                    count++;
                });
            });
        </script>
    </div>
</body>

</html>