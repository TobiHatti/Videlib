<?php
require("../lib/connect.php");
require("../lib/wrapsql.php");
$characterID = $_GET["c"];
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);
?>

<h2>Add Child with</h2>
<form>
<input type="hidden" value="<?= $_GET["c"] ?>" id="modCID"/>
<select>
    <option selected disabled>Select Character</option>
    <?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT *, characters.ID as CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID WHERE PartyID = ? AND characters.ID != ? ORDER BY characters.Name ASC", $_SESSION["VidePID"], $characterID) as $c): ?>
        <option value="<?= $c["CID"] ?>"><?= $c["Name"] ?> (<?= $c["Symbol"]?>)</option>
    <?php endforeach; $sql->Close()?>
</select>
</form>