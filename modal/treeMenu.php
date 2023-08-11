<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$siblingParent1 = "";
$siblingParent2 = "";
$ctr = 0;
$parentGroups = array(
    array("BiologicalMother", "BiologicalFather"),
    array("AdoptedMother", "AdoptedFather"),
    array("StepMother", "StepFather"),
    array("FosterMother", "FosterFather")
);
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);
$sql->Open();
foreach($parentGroups as $group){
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
    ON T1.LinkerA = T2.LinkerB", $characterID, $group[0], $characterID, $group[1]) as $parents){

    $siblingParent1 = $parents["MotherID"];
    $siblingParent2 = $parents["FatherID"];
    $ctr++;
    }
}
$sql->Close();
?>


<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID"/>
<button id="btnEditCharacter" noLoad>Edit Character</button>
<button id="modBtnAddPartner">Partners</button>

<?php if($ctr > 1): ?>
    <button id="modBtnAddSibling">Add Sibling</button>
<?php else: if($ctr == 1): ?>
    <button class="modBtnAddSiblingByParents" d-pida="<?= $siblingParent1 ?>" d-pidb="<?= $siblingParent2 ?>">Add Sibling</button>
<?php endif;endif; ?>

<button id="modBtnAddChild">Add Child</button>