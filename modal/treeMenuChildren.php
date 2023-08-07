<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);
?>

<h2>Add Child</h2>
<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID"/>
<button id="modBtnAddChildWithExisting">with existing Partner</button>

<?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT characters.Name, characters.ID AS CID, users.Symbol FROM characters INNER JOIN users ON characters.COwnerID = users.ID WHERE characters.ID IN (
	SELECT relations.CA_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Partner' AND CB_ID = ? UNION
	SELECT relations.CB_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Partner' AND CA_ID = ?)", 
    $characterID, $characterID) as $partner): ?>

<button id="modBtnAddChildWithSuggestion" d-sid="<?= $partner["CID"]?>">with <?= $partner["Symbol"]?> <?= $partner["Name"]?></button>

<?php endforeach; ?>

<button id="modBtnAddChildWithNoPartner">with no Partner</button>