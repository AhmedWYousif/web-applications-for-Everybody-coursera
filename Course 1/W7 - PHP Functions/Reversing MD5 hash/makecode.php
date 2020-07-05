<?php
$error = false;
$md5 = false;
$code = "";
if ( isset($_GET['code']) ) {
    $code = $_GET['code'];
    if (empty($code)) {
        $error = "Input can't be empty";
    }  else {
        $md5 = hash('md5', $code);
    }
}

?>
<!DOCTYPE html>
<head><title>Ahmed Yousif PIN Code</title></head>
<body>
<h1>MD5 PIN Maker</h1>
<?php
if ($error) {
    print '<p style="color:red">';
    print htmlentities($error);
    print "</p>\n";
}

if ($md5) {
    print "<p>MD5 value: ".htmlentities($md5)."</p>";
}
?>
<p>Please enter any keys for encoding.</p>
<form>
<input type="text" name="code" value="<?= htmlentities($code) ?>"/>
<input type="submit" value="Compute MD5 for CODE"/>
</form>
<p><a href="makecode.php">Reset</a></p>
<p><a href="index.php">Back to Cracking</a></p>
</body>
</html>
