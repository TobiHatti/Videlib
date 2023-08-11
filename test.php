<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV2.php");


$tree = FamilyTree::CreateTree("8CF31BD6-0301-4267-94C4-D4DC7C8D566E", 2);
$graph = $tree->GetStructuralGraph();

echo $tree->partnerNodes[0]->leftNode->partnerNodes[0]->leftNode->entity->name;
echo "|";
echo $tree->partnerNodes[0]->leftNode->partnerNodes[0]->rightNode->entity->name;

echo "---|";
echo $tree->partnerNodes[0]->leftNode->entity->name;
echo $tree->partnerNodes[0]->rightNode->entity->name;
echo "|";
echo $tree->entity->name;
echo "|";
echo $tree->partnerNodes[1]->leftNode->entity->name;
echo $tree->partnerNodes[1]->rightNode->entity->name;
echo "|---";


echo $tree->partnerNodes[1]->leftNode->partnerNodes[1]->leftNode->entity->name;
echo "|";
echo $tree->partnerNodes[1]->leftNode->partnerNodes[1]->rightNode->entity->name;


?>

<pre>
    <!-- <?= var_dump($tree) ?> -->
</pre>

