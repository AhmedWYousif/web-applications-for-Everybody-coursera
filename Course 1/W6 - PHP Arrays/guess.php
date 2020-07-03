
<!DOCTYPE html>
<html>
    <head>
        <title>Ahmed W. Yousif</title>
    </head>
<body>
<h1>Welcome to my guessing game</h1>
<p>
    <?php 
    
        $my_num = 26;
        
        if (!array_key_exists("guess",$_GET)) echo "Missing guess parameter";
        elseif (empty($_GET["guess"])) echo "Your guess is too short";
        elseif (!is_numeric($_GET["guess"])) echo "Your guess is not a number";
        elseif ($_GET["guess"] < $my_num) echo "Your guess is too low";
        elseif ($_GET["guess"] > $my_num) echo "Your guess is too high";
        elseif ($_GET["guess"] == $my_num) echo "Congratulations - You are right";

    ?>
</body>
</html>