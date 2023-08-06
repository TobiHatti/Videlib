<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/ageCalc.php");
require("../lib/familyTree.php");

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

            <br><br>

            <h2>Relatives</h2>


            <?php
            $sql->Close();
            $tree = FamilyTree::CreateTree($characterID);
            $sql->Open();
            ?>
            <div class="familyTreeView">
                <canvas id="canvas"></canvas>
                <div class="layerContainer">
                    <div class="parentContainer">
                        <?php foreach($tree->parents as $parent): ?>
                        <div class="treeNode" d-chid="<?= $parent->characterID ?>">
                            <div class="nodeImg">
                                <img src="<?= Img($parent->cdata["FullresPath"], $parent->cdata["Name"])?>" />
                            </div>
                            <span><?= $parent->cdata["Symbol"] ?> <?= $parent->cdata["Name"] ?></span>
                            <svg class="branch" viewbox="-250 -100 500 200"><?= $parent->GetSvgRelationPaths() ?></svg>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="midContainer">
                        <?php foreach($tree->partners as $partner): ?>
                        <div class="treeNode" d-chid="<?= $partner->characterID ?>">
                            <div class="nodeImg">
                                <img src="<?= Img($partner->cdata["FullresPath"], $partner->cdata["Name"])?>" />
                            </div>
                            <span><?= $partner->cdata["Symbol"] ?> <?= $partner->cdata["Name"] ?></span>
                            <svg class="branch" viewbox="-250 -100 500 200"><?= $partner->GetSvgRelationPaths() ?></svg>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="childContainer">
                        <?php foreach($tree->children as $child): ?>
                        <div class="treeNode" d-chid="<?= $child->characterID ?>">
                            <div class="nodeImg">
                                <img src="<?= Img($child->cdata["FullresPath"], $child->cdata["Name"])?>" />
                            </div>
                            <span><?= $child->cdata["Symbol"] ?> <?= $child->cdata["Name"] ?></span>
                            <svg class="branch" viewbox="-250 -100 500 200"><?= $child->GetSvgRelationPaths() ?></svg>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>


            <h2>Notes</h2>
            <div class="noteContainer">
                <form id="addNoteForm" method="post">
                    <div class="newPostContainer">
                    <textarea name="note" placeholder="Add note..." required></textarea>
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