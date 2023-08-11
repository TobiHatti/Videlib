<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);

$sql->Open();
?>


<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID"/>
<h2>Partner</h2>
<button id="modBtnAddNewPartner">New Partner</button>
<button id="modBtnAddExistingPartner">Add Existing Partner</button>

<?php if($sql->ExecuteScalar("SELECT COUNT(*) FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Partner' AND (CA_ID = ? OR CB_ID = ?)", $characterID, $characterID) != 0): ?>
<button id="btnEditRelations">Edit Partners</button>
<?php endif; $sql->Close(); ?>