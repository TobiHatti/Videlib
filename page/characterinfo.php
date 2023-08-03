<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$characterID = $_GET['c'];

$sql->Open();
$cinfo;
foreach($sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID WHERE characters.ID = ?", $characterID) as $c) $cinfo = $c;
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="characterInfo">
            <h1><?= $cinfo["Symbol"] ?><?= $cinfo["Name"]?> (Age)</h1>
            <h2><?= $cinfo["Species"]?></h2>

            <div class="mainImg">
                <img src="">
            </div>
            <div class="imgCarousel">
                <?php foreach($sql->ExecuteQuery("SELECT * FROM character_img WHERE CharacterID = ?", $characterID) as $img): ?>
                    <div class="carouselItem">
                        <img src="<?= $img["FullresPath"]?>" />
                    </div>
                <?php endforeach;?>
            </div>

            <h2>Relatives</h2>
        </div>
    </div>
</div> 

<?php $sql->Close(); ?>