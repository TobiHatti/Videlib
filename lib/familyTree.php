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

    public string $characterID;
    public string $relationType;
    public array $cdata;

    public function __construct(string $characterID, WrapMySQL $sql, $depth, $relationType)
    {
        $this->characterID = $characterID;
        $this->relationType = $relationType;

        $this->cdata = $sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $characterID)[0];

        if($depth <= 0) return;

        $parentsRaw = $sql->ExecuteQuery("SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Relative'", $characterID);
        $childrenRaw = $sql->ExecuteQuery("SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Relative'", $characterID);
        $partnersRaw = $sql->ExecuteQuery(" SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION 
                                            SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $characterID, $characterID);    
    
        $this->parents = array();
        $this->partners = array();
        $this->children = array();

        foreach($parentsRaw as $parent) array_push($this->parents, new FamilyNode($parent["CID"], $sql, $depth - 1, $parent["RT"]));
        foreach($childrenRaw as $children) array_push($this->children, new FamilyNode($children["CID"], $sql, $depth - 1, $children["RT"]));
        foreach($partnersRaw as $partner) array_push($this->partners, new FamilyNode($partner["CID"], $sql, $depth - 1, $partner["RT"]));

        // echo "<br><br>Parents: <br>";
        // echo var_dump($this->parents);
        // echo "<br><br>Children: <br>";
        // echo var_dump($this->children);
        // echo "<br><br>Partners: <br>";
        // echo var_dump($this->partners);
    }
}

?>