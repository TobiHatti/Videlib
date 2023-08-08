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
    $sql->ExecuteNonQuery("INSERT INTO character_notes (ID, UserID, CharacterID, Note) VALUES (?,?,?,?)", 
        GUID(),
        $_SESSION["VideUID"],
        $_POST["character"],
        $_POST["note"]
    );
    $sql->Close();
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
