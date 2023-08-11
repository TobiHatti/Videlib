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
    if($_POST["action"] == "update"){
        echo "1";
        CreateOrUpdatePartnerRelation($sql, $_POST["CID"], $_POST["PID"], $_POST["RelationType"]);
    }

    if($_POST["action"] == "delete"){
        echo "2";
        $sql->ExecuteNonQuery("DELETE FROM relations WHERE (CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)",
            $_POST["CID"], $_POST["PID"],
            $_POST["PID"], $_POST["CID"]
        );
    }
    $sql->Close();
}
catch(Exception $e) { $status = 500; }
finally{ 
    $sql->Close(); 
}

echo(http_response_code($status));
