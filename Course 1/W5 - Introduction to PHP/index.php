
<!DOCTYPE html>
<html>
<head>
<title>Ahmed Yousif - PHP</title>
</head>
<body>
<h1>Ahmed Yousif PHP</h1>
<p>
<?php
    $hashed_name = hash('sha256', 'Ahmed Yousif');
    echo "The SHA256 hash of \"Ahmed Yousif\" is {$hashed_name}" ;
?>   
</p>
<pre>ASCII ART:

            **
           ****
          **   **
         **     **
        ***********
       *************
      **           **
     **             **
    ****           ****
</pre>
<a href="check.php">Click here to check the error setting</a>
<br/>
<a href="fail.php">Click here to cause a traceback</a>
</body>