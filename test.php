<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV2.php");


$array = array();

$array["A"] = "a";
$array["B"] = "b";
?>

<pre>
    <?= var_dump($array)?>
</pre>