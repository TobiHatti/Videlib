<pre>
<?php
require("lib/connect.php");
require("lib/wrapsql.php");
require("lib/util.php");
require("lib/familyTreeV3.php");


//$tree = FamilyTree::CreateTree("B997B2AE-4EB0-4F45-BA08-77EC0DC724EC", 2);


// foreach($tree->GetSimpleGraph() as $layer){
//     foreach($layer as $node)
//         echo $node->Name." ";
//     echo "<br>";
//}


$arr = array(
  "A" => 3,
  "B" => 2,
  "C" => 20,
);

echo var_dump($arr);

foreach($arr as &$node) $node = $node - 1;
unset($node);
foreach (array_keys($arr, 0, true) as $key) unset($arr[$key]);

echo var_dump($arr);

foreach($arr as &$node) $node = $node - 1;
unset($node);
foreach (array_keys($arr, 0, true) as $key) unset($arr[$key]);

echo var_dump($arr);

foreach($arr as &$node) $node = $node - 1;
unset($node);
foreach (array_keys($arr, 0, true) as $key) unset($arr[$key]);

echo var_dump($arr);

foreach($arr as &$node) $node = $node - 1;
unset($node);
foreach (array_keys($arr, 0, true) as $key) unset($arr[$key]);

echo var_dump($arr);




// public function SkipAdjacentNode($id){
//   if(isset($this->adjacentNodes[$id]) && $this->adjacentNodes[$id] > 0) return true;
//   return false;
// }

// public function AdjustAdjacentNodes(){
  

//   foreach (array_keys($this->adjacentNodes, 0, true) as $key) {
//       unset($this->adjacentNodes[$key]);
//   }
// }

?>

</pre> 

