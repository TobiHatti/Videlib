<?php

enum ConnectionPoint: string{
    case Top = "T";
    case Left = "D";
    case Right = "C";
    case Bottom = "S";
}

class FamilyTree{
    
    public static function CreateTree(string $startNode, int $depth){
        $sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
        TreePath::Init($sql);
        return new TreeNode($sql,$startNode, $depth);
    }
}

class TreePath{
    public static array $pathData = array();
    public static array $pathLog = array();


    private string $nodeA;
    private ?string $nodeA2;
    private string $nodeB;
    private ConnectionPoint $pointA;
    private ?ConnectionPoint $pointA2;
    private ConnectionPoint $pointB;
    private string $relationType;

    public function __construct(TreeNode $nodeA, ?TreeNode $nodeA2, TreeNode $nodeB, ConnectionPoint $pointA, ?ConnectionPoint $pointA2, ConnectionPoint $pointB, string $relation)
    {
        $this->nodeA = $nodeA->shortID;
        if($nodeA2 != null) $this->nodeA2 = $nodeA2->shortID;
        $this->nodeB = $nodeB->shortID;

        $this->pointA = $pointA;
        $this->pointA2 = $pointA2;
        $this->pointB = $pointB;

        $this->relationType = $relation;
    }

    public static function AddPath(TreeNode $nodeA, TreeNode $nodeB, ConnectionPoint $pointA, ConnectionPoint $pointB, string $relation){
        array_push(TreePath::$pathLog, new TreePath($nodeA, null, $nodeB, $pointA, null, $pointB, $relation));
    }

    public static function AddPathFromBranch(TreeBranch $branch, TreeNode $node, ConnectionPoint $point, string $relation){
        array_push(TreePath::$pathLog, new TreePath($branch->nodeA, $branch->nodeB, $node, $branch->pointA, $branch->pointB, $point, $relation));
    }

    public static function Init(WrapMySQL $sql){
        $sql->Open();
        $graphData = $sql->ExecuteQuery("SELECT PathColor, PathWidth, PathType, `Type` AS RType FROM relation_types WHERE SubType = 'Partner'
            UNION SELECT PathColor, PathWidth, PathType, ParentalSubType AS RType  FROM relation_types WHERE SubType = 'Relative' GROUP BY ParentalSubType");
        $sql->Close();

        foreach($graphData as $gd){
            TreePath::$pathData[$gd["RType"]] = array(
                "color" => $gd["PathColor"],
                "type" => $gd["PathType"],
                "size" => $gd["PathWidth"]
            );
        }
    }

    public static function GetDefinition(){
        $def = "";
        $first = true;
        foreach(TreePath::$pathLog as $path){
            if(!$first) $def .= ";";



            if(!isset($path->nodeA2)){
                $def .= "N:{$path->pointA->value}-{$path->nodeA}-{$path->pointB->value}-{$path->nodeB}:".TreePath::$pathData[$path->relationType]["color"].":".TreePath::$pathData[$path->relationType]["type"].":".TreePath::$pathData[$path->relationType]["size"];
            }
            else {
                $def .= "B:{$path->pointA->value}-{$path->nodeA}-{$path->pointA2->value}-{$path->nodeA2}-{$path->pointB->value}-{$path->nodeB}:".TreePath::$pathData[$path->relationType]["color"].":".TreePath::$pathData[$path->relationType]["type"].":".TreePath::$pathData[$path->relationType]["size"];
            }

            $first = false;
        }
        return $def;
    }
}

class TreeNode {
    private static $nodeCtr = 0;

    public string $ID;
    public string $shortID;
    public string $Symbol;
    public string $Name;
    public ?string $Img;
    public string $Gender;

    public array $parentBranches = array();
    public array $leftPartnerBranches = array();
    public array $rightPartnerBranches = array();
    public array $childrenBranches = array();

    public function __construct(WrapMySQL $sql, string $id, int $depth)
    {
        // Load Personal Data
        $sql->Open();
        $data = $sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $id)[0];
        $sql->Close();

        $this->ID = $id;
        $this->shortID = TreeNode::$nodeCtr++;
        $this->Symbol = $data["Symbol"];
        $this->Name = $data["Name"];
        $this->Img = $data["FullresPath"];
        $this->Gender = $data["Gender"];

        
        if($depth > 0){
            // Fetch Data
            $sql->Open();
            $parentData = $sql->ExecuteQuery("SELECT MaleSide.CB_ID AS FatherID, FemaleSide.CB_ID AS MotherID, MaleSide.ParentalSubType FROM 
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'M') 
                AS MaleSide INNER JOIN
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'F') 
                AS FemaleSide ON MaleSide.ParentalSubType = FemaleSide.ParentalSubType AND MaleSide.CA_ID = FemaleSide.CA_ID WHERE MaleSide.CA_ID = ?", $id);
            $partnerData = $sql->ExecuteQuery("SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION
                SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $id, $id);
            $singleChildData = $sql->ExecuteQuery("SELECT * FROM (
                SELECT *, COUNT(*) AS ParentCount FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND CB_ID != '00000000-0000-0000-0000-000000000000' GROUP BY CA_ID, relation_types.ParentalSubType
                ) AS Children WHERE Children.ParentCount = 1 AND CB_ID = ?", $id);
            $sql->Close();

            // Sort and insert Data
            foreach($parentData as $parents){
                $sql->Open();
                $relationType = $sql->ExecuteScalar("SELECT relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE ((CA_ID = ? AND CB_ID = ?) OR (CA_ID = ? AND CB_ID = ?)) AND relation_types.SubType = 'Partner'",
                    $parents["FatherID"], $parents["MotherID"], $parents["MotherID"], $parents["FatherID"]
                );
                $sql->Close();

                $branch = new TreeBranch($sql, $this, $parents["MotherID"], ConnectionPoint::Right, $parents["FatherID"], ConnectionPoint::Left, $relationType, $depth);
                array_push($this->parentBranches, $branch->cbranch);
            }

            $alternator = false;
            foreach($partnerData as $partner){
                if($alternator)
                    array_push($this->leftPartnerBranches, new TreeBranch($sql, null, $this, ConnectionPoint::Left, $partner["CID"], ConnectionPoint::Right, $partner["RT"], $depth));
                else
                    array_push($this->rightPartnerBranches, new TreeBranch($sql, null, $this, ConnectionPoint::Right, $partner["CID"], ConnectionPoint::Left, $partner["RT"], $depth));
                
                $alternator = !$alternator;
            }

            foreach($singleChildData as $child){
                array_push($this->childrenBranches, new TreeBranch($sql, null, $this, ConnectionPoint::Bottom, $child["CA_ID"], ConnectionPoint::Top, $child["ParentalSubType"], $depth, true));
            }

        }
    }

    public function GetSimpleGraph(){
        
        $graph = array();

        $layer0 = array();
        foreach($this->parentBranches as $parents){
            array_push($layer0, $parents->nodeA, $parents->nodeB);
        }

        $layer1 = array();
        array_push($layer1, $this);
        foreach($this->leftPartnerBranches as $p)
            array_unshift($layer1, $p->GetOtherNode($this));
        foreach($this->rightPartnerBranches as $p)
            array_push($layer1, $p->GetOtherNode($this));

        $layer2 = array();
        foreach($this->leftPartnerBranches as $p)
            foreach($p->childrenNodes as $c)
                array_push($layer2, $c->node);
        foreach($this->childrenBranches as $c)
            array_push($layer2, $c->GetOtherNode($this));
        foreach($this->rightPartnerBranches as $p)
            foreach($p->childrenNodes as $c)
                array_push($layer2, $c->node);
        
        array_push($graph, $layer0);
        array_push($graph, $layer1);
        array_push($graph, $layer2);

        return $graph;
    }
}

class TreeBranch {
    public TreeNode $nodeA;
    public TreeNode $nodeB;

    public ConnectionPoint $pointA;
    public ConnectionPoint $pointB;

    public ChildBranch $cbranch;

    public array $childrenNodes = array();

    public function __construct(WrapMySQL $sql, ?TreeNode $callingNode, $nodeA, ConnectionPoint $nodeAConnectionPoint, $nodeB, ConnectionPoint $nodeBConnectionPoint, string $relationType, int $depth, bool $childRelation = false)
    {
        if(is_object($nodeA)) $this->nodeA = $nodeA;
        else $this->nodeA = new TreeNode($sql, $nodeA, $depth - 1);

        if(is_object($nodeB)) $this->nodeB = $nodeB;
        else $this->nodeB = new TreeNode($sql, $nodeB, $depth - 1);

        $this->pointA = $nodeAConnectionPoint;
        $this->pointB = $nodeBConnectionPoint;

        TreePath::AddPath($this->nodeA , $this->nodeB, $nodeAConnectionPoint, $nodeBConnectionPoint, $relationType);

        if(!$childRelation){
            $sql->Open();
            $childrenData = $sql->ExecuteQuery("SELECT MaleSide.CA_ID AS CID, MaleSide.ParentalSubtype AS RelationType FROM 
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'M' AND CB_ID = ?)
                AS MaleSide INNER JOIN 
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'F' AND CB_ID = ?)
                AS FemaleSide ON MaleSide.ParentalSubType = FemaleSide.ParentalSubType AND MaleSide.CA_ID = FemaleSide.CA_ID",
                $this->nodeB->ID, $this->nodeA->ID);
            $sql->Close();

            foreach($childrenData as $child){
                if($callingNode != null && $child["CID"] == $callingNode->ID){
                    $cbranch = new ChildBranch($sql, $this, $callingNode, $child["RelationType"], $depth);
                    $this->cbranch = $cbranch;
                    array_push($this->childrenNodes, $cbranch);
                } 
                else array_push($this->childrenNodes, new ChildBranch($sql, $this, $child["CID"], $child["RelationType"], $depth));
            }
        }
    }

    public function GetOtherNode(TreeNode $node){
        if($this->nodeA->ID != $node->ID) return $this->nodeA;
        else return $this->nodeB;
    }
}

class ChildBranch{
    public TreeBranch $branch;
    public TreeNode $node;

    public ConnectionPoint $point;

    public function __construct(WrapMySQL $sql, TreeBranch $branch, $node, $relationType, $depth)
    {
        $this->branch = $branch;

        if(is_object($node)) $this->node = $node;
        else $this->node = new TreeNode($sql, $node, $depth - 1);

        $this->point = ConnectionPoint::Top;

        TreePath::AddPathFromBranch($branch, $this->node, ConnectionPoint::Top, $relationType);
    }
}

