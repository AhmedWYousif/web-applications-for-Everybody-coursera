<!DOCTYPE html>
<head><title>Ahmed Yousif - MD5 Cracker</title></head>
<body>
<h1>MD5 cracker</h1>
<p>This application takes an MD5 hash of a 3-7 characters based on user selection <br/>
digits,upper,lower or special chars based on user selection <br/>
then attempts to hash all selected characters combinations
to determine the original characters.</p>
<pre>
Debug Output:
<?php

// $total_check it represents total count of checks
$total_checks = 0;

/**
 * this function its recursive function get original text of md5 code 
 * $lprint_time it represents last print debug time 
 * $md5 it represents md5 code to forward 
 * $try_array it represents array of chars for checked text
 * $pos_chars it represents all checked characters
 * $cur_pos it represents current character position in $try_array
 * $len it represents length of original text
 */
function get_original_txt($lprint_time, $md5, $try_array, $pos_chars, $cur_pos, $len)
{
    if ($len === 0) {
        // join try array chars to try it
        $try = implode("", $try_array);
        // Run the hash and then return match result
        $code = hash('md5', $try);
        // increment total checks count
        global $total_checks;
        ++$total_checks;
        return array("match" => ($code === $md5), "text" => $try, "code" => $code);
    } else {
        for ($i = 0; $i < strlen($pos_chars); $i++) {
            // set the char in current position
            $try_array[$cur_pos] = $pos_chars[$i];
            $result = get_original_txt($lprint_time, $md5, $try_array, $pos_chars, $cur_pos + 1, $len - 1);
            // check if match result is set
            if (isset($result)) {
                // if it is matched the return the result and break all loops
                if ($result["match"]) {
                    return $result;
                } else {
                    // if it is not matched the check elapsed time form last debug print 
                    // then if .01 second passed print new debug recode
                    $cur_time = microtime(true);
                    //echo $cur_time - $lprint_time;
                    if ($cur_time - $lprint_time >= .1) {
                        print "{$result['code']} {$result['text']}\n";
                        $lprint_time = $cur_time;
                    }
                }
            }

        }
    }
}

$goodtext = "Not found";
// If there is no parameter, this code is all skipped
if (isset($_GET['md5'])) {
    // start time
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];

    // set Checked Characters according to user selection 
    $txt = "";

    if(isset($_GET['digits']))
      $txt .= "0123456789";

    if(isset($_GET['lower']))
      $txt .= "abcdefghijklmnopqrstuvwxyz";

    if(isset($_GET['upper']))
      $txt .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    if(isset($_GET['special']))
      $txt .= "!@#$%^&*()_-+=?~";

    // check original text 
    $result = get_original_txt($time_pre, $md5, array(), $txt, 0, $_GET['len']?? 4 );

    // if matched then sent original text
    if (isset($result) && $result["match"]) {
        $goodtext = $result["text"];
    }

    // Compute elapsed time
    $time_post = microtime(true);
    print "Total Checks: {$total_checks}";
    print "\n";
    print "Elapsed time: ";
    print $time_post - $time_pre;
    print "\n";
}
?>
</pre>
<!-- Use the very short syntax and call htmlentities() -->
<p>Original Text: <?=htmlentities($goodtext);?></p>
<form>

<label>Checked Characters:</label>
<input type="checkbox" id="digits" name="digits" <?= !(isset($_GET['digits']) || isset($_GET['lower']) || isset($_GET['upper']) || isset($_GET['special']))  || ($_GET['digits']??'off' == 'on') ? ' checked' : '';?> /><label for="digits">digits</label> &nbsp;
<input type="checkbox" id="lower" name="lower" <?= $_GET['lower']??'off' == 'on' ? ' checked' : '';?> /><label for="lower">lowercase letters</label> &nbsp;
<input type="checkbox" id="upper" name="upper"  <?= $_GET['upper']??'off' == 'on' ? ' checked' : '';?>/><label for="upper">uppercase letters</label> &nbsp;
<input type="checkbox" id="special" name="special" <?= $_GET['special']??'off' == 'on' ? ' checked' : '';?> /><label for="special">special letters</label>

<br/>
<label for="len">Origrinal text length:</label>
<select name="len" id="len">
  <option value="5" <?= ($_GET['len']??'0') == '3' ? ' selected="selected"' : '';?> >3</option>
  <option value="4" <?= !isset($_GET['len']) || ($_GET['len']??'0') == '4' ? ' selected="selected"' : '';?> >4</option>
  <option value="5" <?= ($_GET['len']??'0') == '5' ? ' selected="selected"' : '';?> >5</option>
  <option value="6" <?= ($_GET['len']??'0') == '6' ? ' selected="selected"' : '';?> >6</option>
  <option value="7" <?= ($_GET['len']??'0') == '7' ? ' selected="selected"' : '';?> >7</option>
</select>
<br/>
<br/>
<input type="text" name="md5" size="60" value="<?=htmlentities($_GET['md5']??'');?>" />

<input type="submit" value="Crack MD5"/>
</form>
<ul>
<li><a href="index.php">Reset</a></li>
<li><a href="md5.php">MD5 Encoder</a></li>
<li><a href="makecode.php">MD5 Code Maker</a></li>
<li><a href="https://github.com/csev/wa4e/tree/master/code/crack" target="_blank">Source code for this application</a></li>
</ul>
</body>
</html>

