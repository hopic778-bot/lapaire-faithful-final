<?php
require dirname(__DIR__).'/includes/config.php';
$rows = $pdo->query("SELECT id, name, image, active, category FROM products ORDER BY id DESC")->fetchAll();
echo "<style>body{font-family:Arial;padding:20px} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:8px} .ok{color:green;font-weight:bold} .bad{color:red;font-weight:bold}</style>";
echo "<h2>All Products in DB</h2>";
echo "<table><tr><th>ID</th><th>Name</th><th>Category</th><th>active</th><th>image value in DB</th><th>Image Preview</th></tr>";
foreach($rows as $r){
  $imgVal = $r['image'] ?? '';
  $imgUrl = $imgVal ? '../assets/uploads/frames/'.htmlspecialchars($imgVal) : '';
  echo "<tr>";
  echo "<td>{$r['id']}</td>";
  echo "<td>".htmlspecialchars($r['name'])."</td>";
  echo "<td>".htmlspecialchars($r['category'])."</td>";
  echo "<td class='".($r['active']?'ok':'bad')."'>".($r['active']?'✔ YES (visible)':'✘ NO (hidden)')."</td>";
  echo "<td>".($imgVal?htmlspecialchars($imgVal):'<span class="bad">EMPTY — will show stock image</span>')."</td>";
  echo "<td>";
  if($imgUrl) echo "<img src='$imgUrl' style='height:60px;border-radius:4px' onerror=\"this.replaceWith('❌ file missing on disk')\">";
  else echo "<span class='bad'>no image in DB</span>";
  echo "</td>";
  echo "</tr>";
}
echo "</table>";
echo "<br><a href='../admin/products.php' style='background:#0d6efd;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>→ Go to Admin Products</a>";
?>
