<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ahmed W. Yousif's Resume Registry</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Ahmed W. Yousif's Resume Registry</h1>

        <?php

        if (isset($_SESSION['error'])) {
            echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['success'])) {
            echo '<p style="color:green">' . $_SESSION['success'] . "</p>\n";
            unset($_SESSION['success']);
        }

        if ($count = $pdo->query("SELECT COUNT(*) FROM `profile`")) {
            if ($count->fetchColumn() > 0) {
                echo ('<table border="1">');
                echo ('<thead><tr><th>Name</th><th>Headline</th>');
                if (isset($_SESSION['user_id'])) {
                    echo ('<th>Action</th>');
                }
                echo ('</tr></thead>');
                echo ('<tbody>');
                $stmt = $pdo->query("SELECT * FROM `profile` ORDER BY first_name");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr><td><a href="view.php?profile_id=' . $row['profile_id'] . '">' . (htmlentities($row['first_name'] . " " . $row['last_name'])) . "</a></td>";
                    echo "<td>" . (htmlentities($row['headline'])) . "</td>";

                    if (isset($_SESSION['user_id'])) {
                        echo '<td>';
                    }

                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $row['user_id']) {
                        echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                        echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                    }

                    if (isset($_SESSION['user_id'])) {
                        echo '</td>';
                    }

                    echo ('</tr>');
                }
                echo ('</tbody>');
                echo ('</table>');
            } else {
                echo ('<p>No rows found</p>');
            }
        }

        if (isset($_SESSION['name'])) {
            echo  '<p><a href="add.php">Add New Entry</a></p>';
            echo  '<p><a href="logout.php">Logout</a></p>';
        } else {
            echo  '<p><a href="login.php">Please log in</a></p>';
            echo  '<p>Attempt to <a href="add.php">add data</a> without logging in</p>';
        }
        ?>
    </div>
</body>