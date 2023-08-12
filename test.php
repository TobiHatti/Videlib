<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV3.php");


$tree = FamilyTree::CreateTree("B997B2AE-4EB0-4F45-BA08-77EC0DC724EC", 2);


// foreach($tree->GetSimpleGraph() as $layer){
//     foreach($layer as $node)
//         echo $node->Name." ";
//     echo "<br>";
//}


?>

<pre>
  <?= var_dump(TreePath::GetDefinition()) ?> 
</pre> 

