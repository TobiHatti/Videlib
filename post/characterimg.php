<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/dbPresets.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$characterID = $_POST["character"];

try
{
    FileUpload($sql, $_FILES['image'], $characterID);
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));