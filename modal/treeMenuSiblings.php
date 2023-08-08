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
?>

<h2>Parents</h2>
<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID" name="CID"/>

<?php
$sql->Open();
foreach($parentGroups as $group):
    foreach($sql->ExecuteQuery("SELECT * FROM
    (
    SELECT 
        MotherTab.Name AS MotherName, MotherTab.ID AS MotherID, users.Symbol AS MotherSymbol, 'Linker' AS LinkerA 
    FROM characters AS MotherTab 
    INNER JOIN users ON MotherTab.COwnerID = users.ID 
    WHERE MotherTab.ID = 
    (
        SELECT CB_ID AS MotherID 
        FROM relations 
        WHERE CA_ID = ? AND relationType = ?
    )
    )
    AS T1
    INNER JOIN (
        SELECT 
        FatherTab.Name AS FatherName, FatherTab.ID AS FatherID, users.Symbol AS FatherSymbol, 'Linker' AS LinkerB  
        FROM characters AS FatherTab 
        INNER JOIN users ON FatherTab.COwnerID = users.ID 
        WHERE FatherTab.ID = 
        (
            SELECT CB_ID AS FatherID 
            FROM relations 
            WHERE CA_ID = ? AND relationType = ?
        )
    ) AS T2
    ON T1.LinkerA = T2.LinkerB", $characterID, $group[0], $characterID, $group[1]) as $parents): ?>

    <button class="modBtnAddSiblingByParents" d-pida="<?= $parents["MotherID"] ?>" d-pidb="<?= $parents["FatherID"] ?>"><?= $parents["MotherSymbol"] ?> <?=$parents["MotherName"] ?>&nbsp;&nbsp;and&nbsp;&nbsp;<?=$parents["FatherName"] ?> <?= $parents["FatherSymbol"] ?></button>

    <?php
    endforeach;
endforeach;
$sql->Close();
?>




