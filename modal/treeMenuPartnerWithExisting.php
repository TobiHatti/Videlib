<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);
?>

<h2>Select new Partner</h2>
<form id="newExistingPartnerForm">
    <input type="hidden" value="<?= $_GET["c"] ?>" id="modCID" name="CID"/>
    <select required name="partnerID">
        <option value="" selected disabled>Select Character</option>
        <?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT *, characters.ID as CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID WHERE PartyID = ? AND characters.ID != ? ORDER BY characters.Name ASC", $_SESSION["VidePID"], $characterID) as $c): ?>
            <option value="<?= $c["CID"] ?>"><?= $c["Name"] ?> (<?= $c["Symbol"]?>)</option>
        <?php endforeach; $sql->Close(); ?>
    </select>
    <br>   
    <br>
    <span style="color:white; margin: auto;">Relation Type</span>
    <br>
    <select required name="relationType">
        <option value="" selected disabled>Relationship</option>
        <?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT * FROM relation_types WHERE SubType = 'Partner' ORDER BY Weight ASC") as $c): ?>
            <option value="<?= $c["Type"] ?>"><?= $c["DisplayName"] ?></option>
        <?php endforeach; $sql->Close(); ?>
    </select>
    <br><br>
    <input type="submit" value="Save" style="width: 100px;" />
</form>