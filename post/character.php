<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$characterID = GUID();

try
{
    $sql->Open();
    $sql->ExecuteNonQuery("INSERT INTO characters (ID, PartyID, COwnerID, Name, Gender, Species, Birthdate) VALUES (?,?,?,?,?,?,?); ", 
        $characterID,
        1,
        $_POST['owner'],
        $_POST['name'],
        $_POST['gender'],
        $_POST['species'],
        $_POST['birthdate']
    );

    if(!empty($_POST['biomother'])) 
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, ChildID, ParentID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['biomother'], "BiologicalMother");

    if(!empty($_POST['biofather'])) 
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, ChildID, ParentID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['biofather'], "BiologicalFather");

    if(!empty($_POST['adoptmother'])) 
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, ChildID, ParentID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['adoptmother'], "AdoptedMother");

    if(!empty($_POST['adoptfather'])) 
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, ChildID, ParentID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['adoptfather'], "AdoptedFather");


    $sql->Close();

    if(file_exists($_FILES['image']['tmp_name']) || is_uploaded_file($_FILES['image']['tmp_name'])) {

        $filename = GUID().'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $absolute_upload_directory = "/files/characterImg/".$filename; 
        $relative_upload_directory = "..".$absolute_upload_directory;
        if(!move_uploaded_file($_FILES['image']['tmp_name'], $relative_upload_directory)) $absolute_upload_directory = "";
    
        $sql->Open();
        $sql->ExecuteNonQuery("INSERT INTO character_img (ID, CharacterID, FullresPath, MainImg) VALUES (?,?,?,?)", 
            GUID(),
            $characterID,
            $absolute_upload_directory,
            true
        );
        $sql->Close();
    }
}
catch(Exception $e) { $status = 500;  }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));