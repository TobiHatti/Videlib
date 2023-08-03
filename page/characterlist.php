<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
?>

<div class="contentWrapper">
    <div class="contentContainer">

        <div class="characterBoxContainer">

        <div class="characterBox" id="addCharacter">
            +
        </div>


        <?php 
        $sql->Open();
        foreach($sql->ExecuteQuery("SELECT * FROM characters") as $row):
        ?>

        <div class="characterBox">
            <div class="imgContainer">
                <img src="">
            </div>
            <span><?= $row["Name"] ?></span>
        </div>

        <?php endforeach; 
            $sql->Close();
        ?>


        </div>
    </div>
</div> 