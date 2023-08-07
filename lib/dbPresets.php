<?php


function CreateOrUpdatePartnerRelation($sql, $character1, $character2, $relationType){
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
            $sql->ExecuteNonQuery("UPDATE relations SET CA_ID = ?, CB_ID = ?, RelationType = ? WHERE ID = (SELECT ID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.SubType = 'Partner')",
            $character1,
            $character2,
            $relationType,
            $character1, $character2, $character2, $character1);
        }
}

