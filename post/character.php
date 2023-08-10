<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/dbPresets.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";

$characterID = GUID();
$edit = false;
if(isset($_POST["editID"])) {
    $characterID = $_POST["editID"];
    $edit = true;
}

try
{
    $sql->Open();

    $sql->ExecuteNonQuery("INSERT INTO characters 
        (ID, PartyID, COwnerID, `Name`, Gender, Species, Birthdate,AgeMultiplier,AgeOffset) 
        VALUES (?,?,?,?,?,?,?,?,?) 
        ON DUPLICATE KEY UPDATE
        COwnerID = VALUES(COwnerID), 
        `Name` = VALUES(`Name`), 
        Gender = VALUES(Gender), 
        Species = VALUES(Species), 
        Birthdate = VALUES(Birthdate), 
        AgeMultiplier = VALUES(AgeMultiplier), 
        AgeOffset = VALUES(AgeOffset)", 
        $characterID,
        $_SESSION["VidePID"],
        $_POST['owner'],
        $_POST['name'],
        $_POST['gender'],
        $_POST['species'],
        $_POST['birthdate'],
        $_POST['ageMultiplier'],
        $_POST['ageOffset']
    );

    AddParentRelations($sql, $characterID, $_POST['biomother'], $_POST['biofather'], "BiologicalMother", "BiologicalFather", $edit);
    AddParentRelations($sql, $characterID, $_POST['adoptmother'], $_POST['adoptfather'], "AdoptedMother", "AdoptedFather", $edit);
    AddParentRelations($sql, $characterID, $_POST['stepmother'], $_POST['stepfather'], "StepMother", "StepFather", $edit);
    AddParentRelations($sql, $characterID, $_POST['fostermother'], $_POST['fosterfather'], "FosterMother", "FosterFather", $edit);

    if(isset($_POST["partner"])) {
        CreateOrUpdatePartnerRelation($sql, $characterID, $_POST["partner"],$_POST["relation"]);
    }

    $sql->Close();

    FileUpload($sql, $_FILES['image'], $characterID, true);
}
catch(Exception $e) { $status = 500;  }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));