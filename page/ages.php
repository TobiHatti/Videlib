<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/ageCalc.php");
require("../lib/familyTreeV2.php");

AgeCalc::Init($_SESSION["VidePID"]);
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);

$characterID = $_GET['c'];

$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="ageList">
            <?php foreach($sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters
            INNER JOIN users ON characters.COwnerID = users.ID
            LEFT JOIN character_img ON (
                characters.ID = character_img.CharacterID AND character_img.MainImg = (SELECT MAX(MainImg) FROM character_img WHERE character_img.CharacterID = characters.ID)
            ) WHERE PartyID = ? AND Birthdate != '0000-00-00' ORDER BY characters.Birthdate ASC", $_SESSION["VidePID"]) as $row): ?>

            <div class="tab" d-chid="<?= $row["CID"] ?>">
                <div class="imgContainer">
                    <img src="<?= Img($row["FullresPath"], $row["Name"]) ?>" />
                </div>
                <div class="textContainer">
                    <span class="main"><?= $row["Symbol"] ?> <?= $row["Name"] ?></span>
                    <span class="sub"><?= $row["Birthdate"] ?> (Nx)</span>
                </div>
                <div class="ageBox">
                    <?= AgeCalc::GetFromDate($row["Birthdate"]) ?>
                </div>
            </div>

            <?php endforeach; ?>
        </div>
    </div>
</div> 

<?php $sql->Close(); ?>