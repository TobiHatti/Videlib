<?php

function CreateOrUpdatePartnerRelation(WrapMySQL $sql, string $character1, string $character2, string $relationType, bool $onlyCreate = false){
    if($sql->ExecuteScalar("SELECT COUNT(*) FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.Subtype = 'Partner'", 
        $character1, $character2, $character2, $character1) == 0)
        {
            $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?)", 
                GUID(),
                $character1,
                $character2,
                $relationType
            );
        }
        else{
            if($onlyCreate) return;

            $sql->ExecuteNonQuery("UPDATE relations SET CA_ID = ?, CB_ID = ?, RelationType = ? WHERE ID = (SELECT * FROM (SELECT ID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.SubType = 'Partner') AS sub)",
            $character1,
            $character2,
            $relationType,
            $character1, $character2, $character2, $character1);
        }
}

function AddParentRelations(WrapMySQL $sql, string $childID, string $motherID, string $fatherID, string $motherType, string $fatherType, $edit = false){
    
    // CA_ID = CHILD
    // CB_ID = PARENT
    $motherRelationID = GUID();
    $fatherRelationID = GUID();
    
    if($edit){
        $motherRelationID = $sql->ExecuteScalar("SELECT ID FROM relations WHERE CA_ID = ? AND RelationType = ?", $childID, $motherType);
        $fatherRelationID = $sql->ExecuteScalar("SELECT ID FROM relations WHERE CA_ID = ? AND RelationType = ?", $childID, $fatherType);

        if(empty($motherRelationID)) $motherRelationID = GUID();
        if(empty($fatherRelationID)) $fatherRelationID = GUID();
    }


    // Mother Relations
    if(!empty($motherID)) {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE CB_ID = VALUES(CB_ID)", 
        $motherRelationID, $childID, $motherID, $motherType);
    }

    // Father Relation
    if(!empty($fatherID)) {
        $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE CB_ID = VALUES(CB_ID)", 
        $fatherRelationID, $childID, $fatherID, $fatherType);
    }

    // Fill Missing Parent
    if(!empty($motherID) && empty($fatherID))$sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000',?) ON DUPLICATE KEY UPDATE CB_ID = VALUES(CB_ID)", $fatherRelationID, $childID, $fatherType);
    if(empty($motherID) && !empty($fatherID)) $sql->ExecuteNonQuery("INSERT INTO relations (ID, CA_ID, CB_ID, RelationType) VALUES (?,?,'00000000-0000-0000-0000-000000000000',?) ON DUPLICATE KEY UPDATE CB_ID = VALUES(CB_ID)", $motherRelationID, $childID, $motherType);
    
    // Partner Parents
    if(!empty($motherID) || !empty($fatherID)){
        if(empty($motherID)) $motherID = "00000000-0000-0000-0000-000000000000";
        if(empty($fatherID)) $fatherID = "00000000-0000-0000-0000-000000000000";
        CreateOrUpdatePartnerRelation($sql, $motherID, $fatherID,"Partner", true);
    }
}

function FileUpload(WrapMySQL $sql, $files, $characterID, $forceMainImage = false){

    if(file_exists($files['tmp_name']) || is_uploaded_file($files['tmp_name'])) {

        $filename = GUID().'.'.pathinfo($files['name'], PATHINFO_EXTENSION);
        $absolute_upload_directory = "/files/characterImg/".$filename; 
        $relative_upload_directory = "..".$absolute_upload_directory;
        if(!move_uploaded_file($files['tmp_name'], $relative_upload_directory)) $absolute_upload_directory = "";
    
        $sql->Open();

        $mainImg = true;
        if(!$forceMainImage) $mainImg = $sql->ExecuteScalar("SELECT COUNT(*) FROM character_img WHERE CharacterID = ?", $characterID) == 0;
        $sql->ExecuteNonQuery("INSERT INTO character_img (ID, CharacterID, FullresPath, MainImg) VALUES (?,?,?,?)", 
            GUID(),
            $characterID,
            $absolute_upload_directory,
            $mainImg
        );
        $sql->Close();
    }
}