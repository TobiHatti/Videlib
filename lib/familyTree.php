<?php

class FamilyTree{
    public static function CreateTree(string $rootCharacterID, int $depth = 1)
    {
        $sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
        $sql->Open();
        return new FamilyNode(null, $rootCharacterID, $sql, $depth, "Root", RelationCallType::Root);
        $sql->Close();
    }
}


enum RelationCallType{
    case Root;
    case Parent;
    case Child;
    case Partner;
}

enum ConnectionPoint{
    case Unknown;
    case Left;
    case Right;
}

class FamilyNode{
    public FamilyNode $parentNide;

    public array $parents;
    public array $partners;
    public array $children;

    public string $characterID;
    public string $relationType;
    public array $cdata;
    public string $color;
    public ConnectionPoint $connectOn = ConnectionPoint::Unknown;

    public static array $colors = array("#C7B446","#D84B20","#A18594","#924E7D","#BDECB6","#6C4675","#922B3E","#497E76","#EC7C26","#CF3476","#C51D34","#3E3B32","#2A6478","#D6AE01","#317F43","#3E5F8A","#BDECB6","#A12312","#008F39","#CC0605","#FFA420");
    public static int $colorCounter = 0;



    public function __construct(?FamilyNode $parentNode, string $characterID, WrapMySQL $sql, $depth, $relationType, RelationCallType $callType)
    {
        if($callType == RelationCallType::Root) FamilyNode::$colorCounter = 0;
 
        $this->characterID = $characterID;
        $this->relationType = $relationType;
        $this->color = FamilyNode::$colors[FamilyNode::$colorCounter];

        FamilyNode::$colorCounter++;
        if(FamilyNode::$colorCounter >= count(FamilyNode::$colors)) FamilyNode::$colorCounter = 0;

        $this->parents = array();
        $this->partners = array();
        $this->children = array();


        if($parentNode != null){
            switch($callType){
                case RelationCallType::Parent:
                    array_push($this->children, $parentNode);
                    break;
                case RelationCallType::Partner:
                    array_push($this->partners, $parentNode);
                    break;
                case RelationCallType::Child:
                    array_push($this->parents, $parentNode);
                    break;
            }
        }

        $this->cdata = $sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID LEFT JOIN character_img ON characters.ID = character_img.CharacterID WHERE characters.ID = ? ORDER BY MainImg DESC LIMIT 1", $characterID)[0];
       
        if($depth > 0 || ($depth == 0 && $callType == RelationCallType::Child)) 
            foreach($sql->ExecuteQuery("SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Relative' ORDER BY Weight ASC", $characterID) as $parent) 
                array_push($this->parents, new FamilyNode($this, $parent["CID"], $sql, $depth - 1, $parent["RT"], RelationCallType::Parent));    

        if($depth > 0 || ($depth == 0 && $callType == RelationCallType::Parent)) 
            foreach($sql->ExecuteQuery("SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Relative' ORDER BY Weight ASC", $characterID) as $children) 
                array_push($this->children, new FamilyNode($this, $children["CID"], $sql, $depth - 1, $children["RT"], RelationCallType::Child));
        if($depth > 0)
        foreach($sql->ExecuteQuery(" SELECT CA_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CB_ID = ? AND SubType = 'Partner' UNION
            SELECT CB_ID AS CID, relations.RelationType AS RT FROM relations INNER JOIN relation_types ON relations.RelationType = relation_types.Type WHERE CA_ID = ? AND SubType = 'Partner'", $characterID, $characterID) as $partner) 
            array_push($this->partners, new FamilyNode($this, $partner["CID"], $sql, $depth - 1, $partner["RT"], RelationCallType::Partner)); 
        
        // Insert this node in the middle of all partners
        array_splice($this->partners, floor(count($this->partners)/2), 0, array($this));

        // Set connection points for graph of parents
        $insertLeft = true;
        foreach($this->partners as $p){
            if($p->characterID == $this->characterID) $insertLeft = false; 
            if($insertLeft) $p->connectOn = ConnectionPoint::Right;
            else $p->connectOn = ConnectionPoint::Left;
        }
    }

    public function GetSvgRelationPaths(){
        $paths = "";
        if(isset($this->parents) && count($this->parents) != 0) {
            $paths .= FamilyNode::RelP($this->parents[0]->color);
        } 

        
        if(isset($this->partners) && count($this->partners) > 1) {  // 1 element is self
                $insertLeft = true;
                $insertCount = 0;
                foreach($this->partners as $p){
                    if($p->characterID == $this->characterID) {
                        $insertLeft = false; 
                        $insertCount = 0;
                        continue;
                    }

                    if($insertLeft){
                        switch($insertCount){
                            case 0: $paths .= FamilyNode::RelML("#FFFFFF", null); break;
                            case 1: $paths .= FamilyNode::RelMLE1("#FFFFFF", null); break;
                            case 2: $paths .= FamilyNode::RelMLE2("#FFFFFF", null); break;
                        }
                    }
                    else{
                        switch($insertCount){
                            case 0: $paths .= FamilyNode::RelMR("#FFFFFF", null); break;
                            case 1: $paths .= FamilyNode::RelMRE1("#FFFFFF", null); break;
                            case 2: $paths .= FamilyNode::RelMRE2("#FFFFFF", null); break;
                        }
                    }
                    $insertCount++; 
            }
        } 

        // if($this->connectOn == ConnectionPoint::Left) $paths .= FamilyNode::RelML("#FFFFFF", null); 
        // if($this->connectOn == ConnectionPoint::Right) $paths .= FamilyNode::RelMR("#FFFFFF", null); 
        return $paths;
    }

    private static function RelMRC($color, $relationStatus) { return '<path d="M50 0 H70 V90  H60 M50 90 H40 M35 90 H30" stroke="'.$color.'" stroke-width="3" fill="none"></path><!-- MRC -->'; }
    private static function RelMLC($color, $relationStatus) { return '<path d="M-50 0 H-70 V90 H-60 M-50 90 H-40 M-35 90 H-30" stroke="'.$color.'" stroke-width="3" fill="none"></path><!-- MLC -->'; }
    private static function RelMRE1C($color, $relationStatus) { return '<path d="M50 -10 H70 V-70 H210 V90   H200 M190 90 H180 M175 90 H170" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MRE1C -->'; }
    private static function RelMRE2C($color, $relationStatus) { return '<path d="M50 -20 H60 V-80 H350 V90  H340 M330 90 H320 M315 90 H310" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MRE2C -->'; }
    private static function RelMLE1C($color, $relationStatus) { return '<path d="M-50 -10 H-70 V-70 H-210 V90  H-200 M-190 90 H-180 M-175 90 H-170" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MLE1C -->'; }
    private static function RelMLE2C($color, $relationStatus) { return '<path d="M-50 -20 H-60 V-80 H-350 V90  H-340 M-330 90 H-320 M-315 90 H-310" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MLE2C -->'; }
    private static function RelMR($color, $relationStatus) { return '<path d="M50 0 H70" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MR -->'; }
    private static function RelML($color, $relationStatus) { return '<path d="M-50 0 H-70" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- ML -->'; }
    private static function RelMRE1($color, $relationStatus) { return '<path d="M50 -10 H70 V-70 H210 V0" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MRE1 -->'; }
    private static function RelMRE2($color, $relationStatus) { return '<path d="M50 -20 H60 V-80 H350 V0" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MRE2 -->'; }
    private static function RelMLE1($color, $relationStatus) { return '<path d="M-50 -10 H-70 V-70 H-210 V0" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MLE1 -->'; }
    private static function RelMLE2($color, $relationStatus) { return '<path d="M-50 -20 H-60 V-80 H-350 V0" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- MLE2 -->'; }
    private static function RelP($color) { return '<path d="M0 -60 V-90 M-40 -90 H-35 M -30 -90 H-20 M -10 -90 H10 M20 -90 H30 M35 -90 H40" stroke="'.$color.'" stroke-width="3" fill="none"></path> <!-- P -->'; }






    
    
    
    
    

    
    
    
    
    
    

    

}

?>