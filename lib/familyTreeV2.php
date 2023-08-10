<?php

class FamilyTree {
    public static function CreateTree(string $rootNodeID, int $depth = 1) {

        $sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
        return new TreeNode($rootNodeID, $sql, $depth, 0);
    }
}

class FamilyEntity {
    public string $characterID;
    public string $name;
    public string $displayName;
    public ?string $filepath;

    public function __construct(string $characterID, WrapMySQL $sql)
    {
        if($characterID == "dummy") $characterID = "00000000-0000-0000-0000-000000000000";
        $sql->Open();
        $data = $sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $characterID)[0];
        $sql->Close();

        $this->characterID = $data["CID"];
        $this->name = $data["Name"];
        $this->displayName = $data["Symbol"]." ".$data["Name"];
        $this->filepath = $data["FullresPath"];
    }
}

class TreeNode {
    public FamilyEntity $entity;
    public array $parentNodes;
    public array $partnerNodes;
    public int $layer;

    public function __construct(string $characterID, WrapMySQL $sql, int $depth, int $layer)
    {
        $this->layer = $layer;

        $this->parentNodes = array();
        $this->partnerNodes = array();

        $this->entity = new FamilyEntity($characterID, $sql);
        if($characterID == "dummy") return;

        if($depth > 0)
        {
            $sql->Open();
            $partnersRaw = $sql->ExecuteQuery("SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION
            SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $characterID, $characterID);
            
            $parentsRaw = $sql->ExecuteQuery("SELECT CB_ID AS CID, relations.RelationType AS RT, relation_types.Weight, relation_types.ParentalSubtype 
            FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` 
            WHERE CA_ID = ? AND relation_types.SubType = 'Relative' ORDER BY Weight ASC", $characterID);
            $sql->Close();

            // alternate positions for partners
            $alternator = false;
            foreach($partnersRaw as $partnerRelation){
                if($alternator) array_push($this->partnerNodes, new TreeRelation($this, $partnerRelation["CID"], $sql, $depth, $layer));
                else array_push($this->partnerNodes, new TreeRelation($partnerRelation["CID"], $this, $sql, $depth, $layer));
                $alternator != $alternator;
            }

            // Split up types of parents
            $biologicalParents = array();
            $fosterParents = array();
            $stepParents = array();
            $adoptedParents = array();
            foreach($parentsRaw as $parentRelations) {
                switch($parentRelations["ParentalSubtype"]) {
                    case "Biological": array_push($biologicalParents, $parentRelations); break;
                    case "Adopted": array_push($adoptedParents, $parentRelations); break;
                    case "Step": array_push($stepParents, $parentRelations); break;
                    case "Foster": array_push($fosterParents, $parentRelations); break;
                }
            }

            $this->InstanciateParentNode($biologicalParents, "Biological", $sql, $depth);
            $this->InstanciateParentNode($adoptedParents, "Adopted", $sql, $depth);
            $this->InstanciateParentNode($stepParents, "Step", $sql, $depth);
            $this->InstanciateParentNode($fosterParents, "Foster", $sql, $depth);
        }
    }

    private function InstanciateParentNode($parentArray, $parentType, $sql, $depth){
        switch(count($parentArray)){
            case 2: array_push($this->parentNodes, new TreeRelation($parentArray[0]["CID"], $parentArray[1]["CID"], $sql, $depth, $this->layer-1, $this)); break;
            case 1: array_push($this->parentNodes, new TreeRelation($parentArray[0]["CID"], null, $sql, $depth, $this->layer-1, $this)); break;
        }
    }

    public function GetStructuralGraph()
    {
        $graph = array();

        array_push(TreeGraphLayer::GetLayer($graph, 0), $this->entity);
        
        foreach($this->parentNodes as $parentNode){
            array_push(TreeGraphLayer::GetLayer($graph, -1), $parentNode->leftNode->entity);
            array_push(TreeGraphLayer::GetLayer($graph, -1), $parentNode->rightNode->entity);
        }
        
        foreach($this->partnerNodes as $partnerNode){
            if($partnerNode->rightNode->entity->characterID == $this->entity->characterID)
                array_push(TreeGraphLayer::GetLayer($graph, 0), $partnerNode->leftNode->entity);
            else
                array_unshift(TreeGraphLayer::GetLayer($graph, 0), $partnerNode->rightNode->entity);
        }

        foreach($this->partnerNodes as $partnerNode){
            foreach($partnerNode->childrenNodes as $childNode){
                array_push(TreeGraphLayer::GetLayer($graph, 1), $childNode->entity);
            }
        }
    
        usort($graph, function(TreeGraphLayer $a, TreeGraphLayer $b){
            return strcmp($a->layer, $b->layer);
        });
        return $graph;
    }



}

class TreeGraphLayer{
    public int $layer;
    public array $nodes;

    public function __construct(int $layer){
        $this->layer = $layer;
        $this->nodes = array();
    }

    public static function &GetLayer(array &$graph, int $layer) {
        foreach($graph as $glayer){
            if($glayer->layer == $layer) return $glayer->nodes;
        }
        $newLayer = new TreeGraphLayer($layer);
        array_push($graph, $newLayer);
        return $newLayer->nodes;
    }
}

class TreeRelation {
    public array $childrenNodes;
    public TreeNode $leftNode;
    public TreeNode $rightNode;
    
    public string $nodeColor;

    public function __construct($leftNode, $rightNode,WrapMySQL $sql,int $depth, int $layer,?TreeNode $callingChild = null)
    {
        $this->nodeColor = RandomColor();

        if(is_object($leftNode)) $this->leftNode = $leftNode;
        else $this->leftNode = new TreeNode($leftNode, $sql, $depth - 1, $layer);

        $childrenIDs = null;

        if($rightNode != null) {
            if(is_object($rightNode)) $this->rightNode = $rightNode;
            else $this->rightNode = new TreeNode($rightNode, $sql, $depth - 1, $layer);
        }
        else{
            $this->rightNode = new TreeNode("dummy", $sql, $depth - 1, $layer);
        }

        $sql->Open();
            $childrenIDs = $sql->ExecuteQuery("SELECT MotherSide.ChildID FROM (
                SELECT CA_ID AS ChildID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND CB_ID = ?) AS MotherSide
                INNER JOIN (
                SELECT CA_ID AS ChildID FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND CB_ID = ?
                ) AS FatherSide ON MotherSide.ChildID = FatherSide.ChildID",
            $this->leftNode->entity->characterID,
            $this->rightNode->entity->characterID,
        );
        $sql->Close();
    
        $this->childrenNodes = array();
        foreach($childrenIDs as $child){
            if($callingChild != null && $child["ChildID"] == $callingChild->entity->characterID) array_push($this->childrenNodes, $callingChild);
            else array_push($this->childrenNodes, new TreeNode($child["ChildID"], $sql, $depth - 1, $layer + 1 ));
        }
    } 
}