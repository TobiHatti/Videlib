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
    $sql->ExecuteNonQuery("INSERT INTO characters (ID, PartyID, COwnerID, Name, Gender, Species, Birthdate,AgeMultiplier,AgeOffset) VALUES (?,?,?,?,?,?,?,?,?); ", 
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

    AddParentRelations($sql, $characterID, $_POST['biomother'], $_POST['biofather'], "BiologicalMother", "BiologicalFather");
    AddParentRelations($sql, $characterID, $_POST['adoptmother'], $_POST['adoptfather'], "AdoptedMother", "AdoptedFather");
    
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