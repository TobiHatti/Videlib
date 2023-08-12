<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/ageCalc.php");
require("../lib/familyTreeV3.php");

AgeCalc::Init($_SESSION["VidePID"]);
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);

$characterID = $_GET['c'];

$sql->Open();
$cinfo;
foreach($sql->ExecuteQuery("SELECT *, character_img.ID AS ImgID FROM characters 
    INNER JOIN users ON characters.COwnerID = users.ID 
    LEFT JOIN character_img ON characters.ID = character_img.CharacterID AND character_img.Active = 1 
    WHERE characters.ID = ?  ORDER BY MainImg DESC LIMIT 1", $characterID) as $c) $cinfo = $c;
$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <div class="backBtn" d-page="characterlist" d-pk="" d-pv=""><i class="fa-solid fa-chevron-left"></i> Back</div>
        <div class="characterInfo">
            <h1><?= $cinfo["Symbol"] ?><?= $cinfo["Name"]?> (<?= AgeCalc::GetFromDate($cinfo["Birthdate"], $cinfo["AgeMultiplier"], $cinfo["AgeOffset"]) ?>)</h1>
            <h2 class="species"><?= $cinfo["Species"]?></h2>

            <div class="mainImgContainer">
                <div class="mainImg">
                    <img src="<?= Img($cinfo["FullresPath"], $cinfo["Name"]) ?>" id="mainImg" class="<?= $cinfo["Sensitive"] ? "imgBlur" : "" ?>">
                </div>
                <?php if($cinfo["FullresPath"] != null): ?>
                <div class="operationsContainer">
                    <input type="hidden" id="modIID" value="<?= $cinfo["ImgID"] ?>">
                    <button noLoad id="btnSetPrimaryImg"><i class="fa-regular fa-star"></i></button>
                    <button noLoad id="btnEditAltText"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button noLoad id="btnMarkSensitive" d-sens="<?= $cinfo["Sensitive"] ? "true" : "false" ?>" ><i class="fa-regular fa-eye-slash"></i></button>
                    <button noLoad id="btnDeleteImage"><i class="fa-regular fa-trash-can"></i></button>
                </div>
                <?php endif; ?>
            </div>
            <button noLoad id="btnMoreActions" style="display: block; margin: 10px auto;"><i class="fa-solid fa-ellipsis"></i></button>
            <span id="imgDescription"><?= $cinfo["ImgDescription"] ?></span>
            <div class="imgCarousel">
                <form id="addImageForm" method="post" enctype="multipart/form-data" >
                <input type="hidden" value="<?= $characterID ?>" name="character" id="cid"/>
                <label class="carouselItem addImage" for="imgBtn">
                    +
                </label>
                <input type="file" name="image" id="imgBtn" accept="image/*" hidden/>
                </form>
                <?php $sql->Open(); foreach($sql->ExecuteQuery("SELECT * FROM character_img WHERE CharacterID = ? AND Active = 1 ORDER BY MainImg DESC", $characterID) as $img): ?>
                <div class="carouselItem carouselImg" d-sens="<?= $img["Sensitive"] ? "true" : "false" ?>" d-iid="<?= $img["ID"] ?>" d-idesc="<?= base64_encode($img["ImgDescription"]) ?>">
                    <img src="<?= Img($img["FullresPath"]) ?>" class="<?= $img["Sensitive"] ? "permaImgBlur" : "" ?>"/>
                </div>
                <?php endforeach; $sql->Close();?>
            </div>   
            <br><br>

            <h2>Relatives</h2>


            <?php
            $tree = FamilyTree::CreateTree($characterID, 2);
            $graph = $tree->GetSimpleGraph();
            ?>
            <div class="familyTreeView">
                <svg class="branch" viewBox="0 0 500 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" preserveAspectRatio="none">
                </svg>
                <div class="layerContainer">
                    <input type="hidden" id="pathDef" value="<?= TreePath::GetDefinition() ?>" />
                    <?php foreach($graph as $layer): ?>
                    <div class="treeLayer">
                        <?php foreach($layer as $node): ?>
                            <?php if($node->ID != "00000000-0000-0000-0000-000000000000"): ?>
                            <div class="treeNode" d-chid="<?= $node->ID ?>">
                                <div class="nodeImg">
                                    <img src="<?= Img($node->Img, $node->Name) ?>" />
                                </div>
                                <span class="nodeName"><?= $node->DisplayName ?></span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <h2>Notes</h2>
            <div class="noteContainer">
                <form id="addNoteForm" method="post">
                    <div class="newPostContainer">
                    <textarea name="note" placeholder="Add note..." required></textarea>
                    <input type="hidden" value="<?= $characterID ?>" name="character"/>
                    <input type="submit" value="Post" id="noteSubmitBtn"/>
                    </div>
                </form>

                <?php $sql->Open();foreach($sql->ExecuteQuery("SELECT * FROM character_notes INNER JOIN users ON character_notes.UserID = users.ID WHERE CharacterID = ? ORDER BY CreatedDate DESC", $characterID) as $note): ?>
                <div class="note">
                    <span class="user">By <?= $note["Username"] ?> on <?= date_format(date_create($note["CreatedDate"]), "M. d Y \a\\t h:i A") ?></span>
                    <p><?= nl2br($note["Note"]) ?></p>
                </div>
                <?php endforeach; $sql->Close(); ?>
            </div>
        </div>
    </div>
</div>


