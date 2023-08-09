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
$cinfo;
foreach($sql->ExecuteQuery("SELECT *, character_img.ID AS ImgID FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $characterID) as $c) $cinfo = $c;
$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <h1>Hidden images of <?= $cinfo["Symbol"] ?> <?= $cinfo["Name"]?></h1>
        <input type="hidden" value="<?= $characterID ?>" name="character" id="cid"/>
        <div class="imgGallery">
            <?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT * FROM character_img WHERE CharacterID = ? AND Active = 0 ORDER BY MainImg DESC", $characterID) as $img): ?>
            <div class="galleryItem" d-sens="<?= $img["Sensitive"] ? "true" : "false" ?>" d-iid="<?= $img["ID"] ?>">
                <img src="<?= Img($img["FullresPath"]) ?>" class="<?= $img["Sensitive"] ? "permaImgBlur" : "" ?>"/>
            </div>
            <?php endforeach; $sql->Close();?>
        </div>
    </div>
</div>  