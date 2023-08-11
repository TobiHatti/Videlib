<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);

$sql->Open();
$relationTypes = array();
foreach($sql->ExecuteQuery("SELECT * FROM relation_types WHERE SubType = 'Partner'") as $type) $relationTypes[$type["Type"]] = $type["DisplayName"];
?>

<h2>Relationships</h2>
<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID"/>

<?php foreach($sql->ExecuteQuery("SELECT *,characters.ID AS CID FROM characters 
INNER JOIN relations ON characters.ID = relations.CA_ID 
INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` 
INNER JOIN users ON characters.COwnerID = users.ID 
WHERE CB_ID = ? AND relation_types.SubType = 'Partner'
UNION
SELECT *,characters.ID AS CID FROM characters 
INNER JOIN relations ON characters.ID = relations.CB_ID 
INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` 
INNER JOIN users ON characters.COwnerID = users.ID 
WHERE CA_ID = ? AND relation_types.SubType = 'Partner'", $characterID, $characterID) as $partner): ?>

<?php if($partner["CID"] == "00000000-0000-0000-0000-000000000000") continue; ?>

<div class="partnerBox">
    <form method="post" class="editRelationForm">
        <input type="hidden" value="" name="action" class="formAction"/>
        <input type="hidden" value="<?= $_GET["c"] ?>" name="CID"/>
        <input type="hidden" value="<?= $partner["CID"] ?>" name="PID" />
        <span><?= $partner["Symbol"] ?> <?= $partner["Name"] ?></span>
        <select name="RelationType">
            <?php foreach ($relationTypes as $id => $display): ?>
                <option value="<?= $id ?>" <?= $id == $partner["RelationType"] ? "selected" : "" ?>><?= $display ?></option>
            <?php endforeach; ?>
        </select>
        <button name="update" type="submit" class="btnUpdateRelation" noLoad><i class="fa-solid fa-check"></i></button>
        <button name="delete" type="submit" class="btnDeleteRelation" noLoad><i class="fa-solid fa-trash"></i></button>
    </form>
</div>
<?php endforeach; $sql->Close(); ?>


