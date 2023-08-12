<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV3.php");


$tree = FamilyTree::CreateTree("8CF31BD6-0301-4267-94C4-D4DC7C8D566E", 2);


// foreach($tree->GetSimpleGraph() as $layer){
//     foreach($layer as $node)
//         echo $node->Name." ";
//     echo "<br>";
//}


?>

<pre>
  <?= var_dump(TreePath::GetDefinition()) ?> 
</pre> 

