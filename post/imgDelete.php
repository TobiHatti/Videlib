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
    $filename = $sql->ExecuteScalar("SELECT FullresPath FROM character_img WHERE ID = ?", $_POST["iid"]);
    $sql->ExecuteNonQuery("DELETE FROM character_img WHERE ID = ?", $_POST["iid"]);
    $sql->Close();

    unlink("..".$filename);
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
