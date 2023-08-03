<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$status = 200;
$statusMessage = "OK";
try
{
    $sql->Open();
    $sql->ExecuteNonQuery("INSERT INTO characters (PartyID, COwnerID, Name, Gender, Species, Birthdate) VALUES (?,?,?,?,?,?)", 
        1,
        $_POST['owner'],
        $_POST['name'],
        $_POST['gender'],
        $_POST['species'],
        $_POST['birthdate']
    );
}
catch(Exception $e) { $status = 500;  }
finally{ $sql->Close(); }

echo(http_response_code($status));


