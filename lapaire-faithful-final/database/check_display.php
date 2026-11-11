<?php
require dirname(__DIR__).'/includes/config.php';
$rows = $pdo->query('SELECT id,name,image FROM products WHERE active=1 ORDER BY id DESC')->fetchAll();
echo '<style>body{font-family:Arial;padding:20px} img{max-width:200px;border:2px solid #ccc;border-radius:8px;display:block;margin-top:8px}</style>';
echo '<h2>Image Display Test</h2>';
foreach($rows as $r){
  $url = 'http://'.$_SERVER['HTTP_HOST'].'/lapaire-faithful-final/lapaire-faithful-final/lapaire-faithful-final/assets/uploads/frames/'.htmlspecialchars($r['image']);
  echo '<div style="margin-bottom:20px;padding:12px;border:1px solid #ddd;border-radius:8px">';
  echo '<b>'.$r['name'].'</b> (ID '.$r['id'].')<br>';
  echo 'DB image value: <code>'.htmlspecialchars($r['image']).'</code><br>';
  echo 'Full URL: <a href="'.$url.'" target="_blank">'.$url.'</a><br>';
  if($r['image']){
    echo '<img src="'.$url.'" onerror="this.style.border=\'2px solid red\';this.after(document.createTextNode(\' ← BROKEN\'))">';
  } else {
    echo '<span style="color:red">No image in DB</span>';
  }
  echo '</div>';
}
?>
