<?php

use FamilyNode as GlobalFamilyNode;

class FamilyTree{
    public static function CreateTree(string $rootCharacterID, int $depth = 1)
    {
        $sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
        $sql->Open();

        return new FamilyNode($rootCharacterID, $sql, $depth, "Root");

        $sql->Close();
    }
}


class FamilyNode{

    public array $parents;
    public array $partners;
    public array $children;

    public string $relationType;

    public function __construct(string $characterID, WrapMySQL $sql, $depth, $relationType)
    {
        $this->relationType = $relationType;

        $parentsRaw = $sql->ExecuteQuery("SELECT CB_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Relative'", $characterID);
        $childrenRaw = $sql->ExecuteQuery("SELECT CA_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Relative'", $characterID);
        $relativeRaw = $sql->ExecuteQuery(" SELECT CA_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION 
                                            SELECT CB_ID AS CID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $characterID, $characterID);    
    
        echo "<br><br>Parents: <br>";
        echo var_dump($parentsRaw);
        echo "<br><br>Parents: <br>";
        echo var_dump($childrenRaw);
        echo "<br><br>Parents: <br>";
        echo var_dump($relativeRaw);
    }
}

?>