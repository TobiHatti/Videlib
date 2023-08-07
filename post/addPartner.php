<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$currentCharacter = $_POST["CID"];
$otherCharacter = $_POST["partnerID"];
$relationType = $_POST["relationType"];

try
{
    $sql->Open();
    CreateOrUpdatePartnerRelation($sql, $currentCharacter, $otherCharacter,$relationType);
    $sql->Close();
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
