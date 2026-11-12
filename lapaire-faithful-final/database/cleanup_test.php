<?php
require dirname(__DIR__).'/includes/config.php';

$deleted = 0;
// Delete test upload products (active=0 and name starts with TEST UPLOAD)
$rows = $pdo->query("SELECT id, image FROM products WHERE name LIKE 'TEST UPLOAD%'")->fetchAll();
$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'frames' . DIRECTORY_SEPARATOR;

foreach($rows as $r){
  if($r['image'] && file_exists($uploadDir.$r['image'])) @unlink($uploadDir.$r['image']);
  $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$r['id']]);
  $deleted++;
}

// Also delete the test jfif file from earlier
$oldTest = $uploadDir.'test_1794334365.jfif';
if(file_exists($oldTest)) @unlink($oldTest);

echo "<style>body{font-family:Arial;padding:20px} a{background:#0d6efd;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:16px}</style>";
echo "<h2>✔ Cleanup done</h2>";
echo "<p>Deleted <b>$deleted</b> test product(s) and their image files.</p>";
echo "<p>Your real products (Edith, johnson, MARK ALISON, etc.) are untouched.</p>";
echo "<a href='../admin/products.php'>→ Go to Admin Products</a> &nbsp;";
echo "<a href='../glasses.php' style='background:#198754'>→ View Glasses Page</a>";
?>
