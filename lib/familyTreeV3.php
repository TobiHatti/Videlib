<?php


enum ConnectionPoint{
    case Top;
    case Left;
    case Right;
    case Bottom;
}


class TreeNode {
    private static $nodeCtr = 0;

    public string $ID;
    public string $shortID;
    public string $Symbol;
    public string $Name;
    public string $Img;
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
        $this->Img = $data["Filename"];
        $this->Gender = $data["Gender"];

        
        if($depth > 0){
            // Fetch Data
            $sql->Open();
            $parentData = $sql->ExecuteQuery("SELECT MaleSide.CB_ID AS FatherID, FemaleSide.CB_ID, MaleSide.ParentalSubType AS MotherID FROM 
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'M') 
                AS MaleSide INNER JOIN
                (SELECT * FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND relation_types.GenderSubType = 'F') 
                AS FemaleSide ON MaleSide.ParentalSubType = FemaleSide.ParentalSubType AND MaleSide.CA_ID = FemaleSide.CA_ID WHERE MaleSide.CA_ID = ?", $id);
            $partnerData = $sql->ExecuteQuery("SELECT CA_ID AS CID, relations.RelationType AS RT, RelationColor FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION
                SELECT CB_ID AS CID, relations.RelationType AS RT, RelationColor FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $id, $id);
            $singleChildData = $sql->ExecuteQuery("SELECT * FROM (
                SELECT *, COUNT(*) AS ParentCount FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.`Type` WHERE relation_types.SubType = 'Relative' AND CB_ID != '00000000-0000-0000-0000-000000000000' GROUP BY CA_ID, relation_types.ParentalSubType
                ) AS Children WHERE Children.ParentCount = 1 AND CB_ID = ?", $id);
            $sql->Close();

            // Sort and insert Data
            foreach($parentData as $parents)
                array_push($this->parentBranches, new TreeBranch($sql, $this, $parents["MotherID"], ConnectionPoint::Right, $parents["FatherID"], ConnectionPoint::Left, $parents["ParentalSubType"]));
          

        }
    }

}

class TreeBranch {
    public TreeNode $nodeA;
    public TreeNode $nodeB;

    public ConnectionPoint $pointA;
    public ConnectionPoint $pointB;

    public string $lineColor;
    public string $lineType;
    public string $lineSize;

    public array $childrenNodes;

    public function __construct(WrapMySQL $sql, TreeNode $callingNode, $mother, ConnectionPoint $motherConnectionPoint, $father, ConnectionPoint $fatherConnectionPoint, string $relationType)
    {
        
    }
}

