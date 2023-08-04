<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/ageCalc.php");
AgeCalc::Init(1);
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);

$characterID = $_GET['c'];

$sql->Open();
$cinfo;
foreach($sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $characterID) as $c) $cinfo = $c;
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="characterInfo">
            <h1><?= $cinfo["Symbol"] ?><?= $cinfo["Name"]?> (<?= AgeCalc::GetFromDate($cinfo["Birthdate"]) ?>)</h1>
            <h2 class="species"><?= $cinfo["Species"]?></h2>

            <div class="mainImg">
                <img src="<?= Img($cinfo["FullresPath"], $cinfo["Name"]) ?>" id="mainImg">
            </div>
            <div class="imgCarousel">
                <form id="addImageForm" method="post" enctype="multipart/form-data" >
                <input type="hidden" value="<?= $characterID ?>" name="character" id="cid"/>
                <label class="carouselItem addImage" for="imgBtn">
                    +
                </label>
                <input type="file" name="image" id="imgBtn" accept="image/*" hidden/>
                </form>
                <?php foreach($sql->ExecuteQuery("SELECT * FROM character_img WHERE CharacterID = ?", $characterID) as $img): ?>
                <div class="carouselItem carouselImg">
                    <img src="<?= Img($img["FullresPath"]) ?>" />
                </div>
                <?php endforeach;?>
            </div>   


            <h2>Relatives</h2>


            <h2>Notes</h2>
            <div class="noteContainer">
                <form id="addNoteForm" method="post">
                    <div class="newPostContainer">
                    <textarea name="note" placeholder="Add note..."></textarea>
                    <input type="hidden" value="<?= $characterID ?>" name="character"/>
                    <button type="submit">Post</button>
                    </div>
                </form>

                <?php foreach($sql->ExecuteQuery("SELECT * FROM character_notes INNER JOIN users ON character_notes.UserID = users.ID WHERE CharacterID = ?", $characterID) as $note): ?>

                <div class="note">
                    <span class="user">By <?= $note["Username"] ?> on <?= date_format(date_create($note["CreatedDate"]), "M. d Y \a\\t h:i A") ?></span>
                    <p><?= nl2br($note["Note"]) ?></p>
                </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php $sql->Close(); ?>