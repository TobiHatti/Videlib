<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);


$parentGroups = array(
    array("BiologicalMother", "BiologicalFather"),
    array("AdoptedMother", "AdoptedFather"),
    array("StepMother", "StepFather"),
    array("FosterMother", "FosterFather")
);


$sql->Open();

foreach($parentGroups as $group){
    
}


$sql->Close();




?>

<h2>Parents</h2>
<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID" name="CID"/>


