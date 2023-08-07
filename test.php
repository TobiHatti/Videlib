<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV2.php");



$tree = FamilyTree::CreateTree("B219C811-F11B-479A-8F05-46B09BB87792");

echo $tree->parentNodes[0]->leftNode->entity->displayName;
echo $tree->parentNodes[0]->rightNode->entity->displayName;
echo "<br>";

echo $tree->entity->displayName;
echo $tree->partnerNodes[0]->rightNode->entity->displayName;    // if self is left node: show partner on right

echo "<br>";

echo $tree->partnerNodes[0]->childrenNodes[0]->entity->displayName;
echo $tree->partnerNodes[0]->childrenNodes[1]->entity->displayName;
echo $tree->partnerNodes[0]->childrenNodes[2]->entity->displayName;
echo $tree->partnerNodes[0]->childrenNodes[3]->entity->displayName;
?>
<pre>
<?= var_dump($tree)?>
</pre>