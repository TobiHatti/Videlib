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


    if($sql->ExecuteScalar("SELECT COUNT(*) FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.Subtype = 'Partner'", $currentCharacter, $otherCharacter, $otherCharacter, $currentCharacter) == 0)
    {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
            GUID(),
            $currentCharacter,
            $otherCharacter,
            $relationType
        );
    }
    else{
        $sql->ExecuteNonQuery("UPDATE relations SET CA_ID = ?, CB_ID = ?, RelationType = ? WHERE ID = (SELECT ID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.SubType = 'Partner')",
        $currentCharacter,
        $otherCharacter,
        $relationType,
        $currentCharacter, $otherCharacter, $otherCharacter, $currentCharacter);
    }

    $sql->Close();
    
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
