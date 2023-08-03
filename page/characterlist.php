<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="characterBoxContainer">
            <div class="characterBox" id="addCharacter">
                +
            </div>
            <?php foreach($sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID ORDER BY Name ASC") as $row): ?>
            <div class="characterBox realCharacter" d-chid="<?= $row["CID"] ?>">
                <div class="imgContainer">
                    <img src="<?= $row["FullresPath"] ?>">
                </div>
                <span class="name"><?= $row["Symbol"] ?> <?= $row["Name"] ?></span>
                <span class="species"><?= $row["Species"] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 

<?php $sql->Close(); ?> 