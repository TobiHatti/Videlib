<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$characterID = $_POST["character"];

try
{

    if(file_exists($_FILES['image']['tmp_name']) || is_uploaded_file($_FILES['image']['tmp_name'])) {

        $filename = GUID().'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $absolute_upload_directory = "/files/characterImg/".$filename; 
        $relative_upload_directory = "..".$absolute_upload_directory;
        if(!move_uploaded_file($_FILES['image']['tmp_name'], $relative_upload_directory)) $absolute_upload_directory = "";
    
        $sql->Open();

        $mainImg = $sql->ExecuteScalar("SELECT COUNT(*) FROM character_img WHERE CharacterID = ?", $characterID) == 0;
        $sql->ExecuteNonQuery("INSERT INTO character_img (ID, CharacterID, FullresPath, MainImg) VALUES (?,?,?,?)", 
            GUID(),
            $characterID,
            $absolute_upload_directory,
            $mainImg
        );
        $sql->Close();
    }
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));


function GUID()
{
    if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}