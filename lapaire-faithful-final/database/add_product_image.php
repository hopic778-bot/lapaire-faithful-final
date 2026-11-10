<?php
require dirname(__DIR__).'/includes/config.php';
try{
  $pdo->exec("ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL AFTER color");
  echo '<p style="font-family:Arial;color:green;padding:20px">✔ Column <b>image</b> added to products table. <a href="../admin/products.php">Go to Products</a></p>';
}catch(PDOException $e){
  if(str_contains($e->getMessage(),'Duplicate column'))
    echo '<p style="font-family:Arial;color:orange;padding:20px">ℹ Column already exists. <a href="../admin/products.php">Go to Products</a></p>';
  else
    echo '<p style="font-family:Arial;color:red;padding:20px">Error: '.htmlspecialchars($e->getMessage()).'</p>';
}
