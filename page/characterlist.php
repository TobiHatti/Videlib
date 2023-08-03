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
            <?php foreach($sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID ORDER BY Name ASC") as $row): ?>
            <div class="characterBox">
                <div class="imgContainer">
                    <img src="">
                </div>
                <span><?= $row["Symbol"] ?> <?= $row["Name"] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 

<?php $sql->Close(); ?> 