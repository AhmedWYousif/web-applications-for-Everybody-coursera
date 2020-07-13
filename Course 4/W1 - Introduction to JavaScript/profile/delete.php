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

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
  $sql = "DELETE FROM profile WHERE profile_id = :zip";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['profile_id']));
  $_SESSION['success'] = 'Record deleted';
  header('Location: index.php');
  return;
}

// Guardian: Make sure that profile_id is present
if (!isset($_GET['profile_id'])) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pro and user_id = :uid");
$stmt->execute(array(':uid' => $_SESSION['user_id'], ':pro' =>  $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
  $_SESSION['error'] = 'Could not load profile';
  header('Location: index.php');
  return;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Ahmed W. Yousif's Deleteing Profile</title>
  <?php require_once "bootstrap.php"; ?>
</head>

<body>
  <div class="container">
    <h1>Deleteing Profile</h1>
    <p>First Name: <?= htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?></p>

    <form method="post">
      <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
      <input type="submit" value="Delete" name="delete">
      <input type="submit" name="cancel" value="Cancel">
    </form>
  </div>
</body>

</html>