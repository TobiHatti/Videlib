<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/dbPresets.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$characterID = GUID();

try
{
    $sql->Open();
    $sql->ExecuteNonQuery("INSERT INTO characters (ID, PartyID, COwnerID, Name, Gender, Species, Birthdate) VALUES (?,?,?,?,?,?,?); ", 
        $characterID,
        $_SESSION["VidePID"],
        $_POST['owner'],
        $_POST['name'],
        $_POST['gender'],
        $_POST['species'],
        $_POST['birthdate']
    );

    

    // Add parent relations
    if(!empty($_POST['biomother'])) 
    {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['biomother'], "BiologicalMother");
    }

    if(!empty($_POST['biofather'])) 
    {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['biofather'], "BiologicalFather");
    }

    // Add empty entry for missing parents
    if(!empty($_POST['biomother']) && empty($_POST['biofather']))$sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000','BiologicalMother')", GUID(), $characterID);
    if(empty($_POST['biomother']) && !empty($_POST['biofather'])) $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000','BiologicalFather')", GUID(), $characterID);
    
    
    if(!empty($_POST['adoptmother'])) 
    {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['adoptmother'], "AdoptedMother");
    }

    if(!empty($_POST['adoptfather'])) 
    {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
        GUID(), $characterID, $_POST['adoptfather'], "AdoptedFather");
    }

    if(!empty($_POST['adoptmother']) && empty($_POST['adoptfather']))$sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000','AdoptedMother')", GUID(), $characterID);
    if(empty($_POST['adoptmother']) && !empty($_POST['adoptfather'])) $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000','AdoptedFather')", GUID(), $characterID);
    

    $bioMotherID = $_POST['biomother'];
    $bioFatherID = $_POST['biofather'];
    $adoptMotherID = $_POST['adoptmother'];
    $adoptFatherID = $_POST['adoptfather'];

    if(!empty($bioMotherID) || !empty($bioFatherID)){
        if(empty($bioMotherID)) $bioMotherID = "00000000-0000-0000-0000-000000000000";
        if(empty($bioFatherID)) $bioFatherID = "00000000-0000-0000-0000-000000000000";
        CreateOrUpdatePartnerRelation($sql, $bioMotherID, $bioFatherID,"Partner");
    }

    if(!empty($adoptMotherID) || !empty($adoptFatherID)){
        if(empty($adoptMotherID)) $adoptMotherID = "00000000-0000-0000-0000-000000000000";
        if(empty($adoptFatherID)) $adoptFatherID = "00000000-0000-0000-0000-000000000000";
        CreateOrUpdatePartnerRelation($sql, $adoptMotherID, $adoptFatherID,"Partner");
    }

    if(isset($_POST["partner"]))
    {
        CreateOrUpdatePartnerRelation($sql, $characterID, $_POST["partner"],$_POST["relation"]);
    }

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