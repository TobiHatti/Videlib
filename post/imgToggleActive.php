<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/dbPresets.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

try
{
    $sql->Open();
    $sql->ExecuteNonQuery("UPDATE character_img SET character_img.`Active` = (SELECT * FROM (SELECT NOT(character_img.`Active`) FROM character_img WHERE character_img.ID = ?) AS x) WHERE character_img.ID = ?", 
        $_POST["iid"], $_POST["iid"]
    );
    $sql->Close();
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
