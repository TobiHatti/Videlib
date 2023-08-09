<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="backBtn" d-page="menu" d-pk="" d-pv=""><i class="fa-solid fa-chevron-left"></i> Back</div>
        <div class="characterBoxContainer">
            <div class="characterBox" id="addCharacter">
                +
            </div>
            <?php foreach($sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters
INNER JOIN users ON characters.COwnerID = users.ID
LEFT JOIN character_img ON (
	characters.ID = character_img.CharacterID AND character_img.MainImg = (SELECT MAX(MainImg) FROM character_img WHERE character_img.CharacterID = characters.ID)
) WHERE PartyID = ? ORDER BY characters.Name ASC", $_SESSION["VidePID"]) as $row): ?>
            <div class="characterBox realCharacter" d-chid="<?= $row["CID"] ?>">
                <div class="imgContainer">
                    <img src="<?= Img($row["FullresPath"], $row["Name"]) ?>"> 
                </div>
                <span class="name"><?= $row["Symbol"] ?> <?= $row["Name"] ?></span>
                <span class="species"><?= $row["Species"] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 

<?php $sql->Close(); ?> 