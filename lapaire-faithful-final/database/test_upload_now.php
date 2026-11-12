<?php
// Quick upload + DB test — visit this page, upload an image, check result
require dirname(__DIR__).'/includes/config.php';

$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'frames' . DIRECTORY_SEPARATOR;
$result = [];

// ── Handle POST ──────────────────────────────────────────────────────────────
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_FILES['img']['name'])){
  $file = $_FILES['img'];
  $result['1_file_received']   = $file['name'].' ('.$file['size'].' bytes, error='.$file['error'].')';
  $result['2_tmp_exists']      = file_exists($file['tmp_name']) ? 'YES' : 'NO';
  $result['3_upload_dir']      = $uploadDir;
  $result['4_dir_exists']      = is_dir($uploadDir) ? 'YES' : (mkdir($uploadDir,0777,true)?'CREATED':'FAILED TO CREATE');
  $result['5_dir_writable']    = is_writable($uploadDir) ? 'YES' : 'NO';

  if($file['error'] === UPLOAD_ERR_OK){
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newName = 'test_upload_'.time().'.'.$ext;
    $dest    = $uploadDir.$newName;

    if(move_uploaded_file($file['tmp_name'], $dest)){
      $result['6_move_result'] = 'SUCCESS — file saved as '.$newName;
      $result['7_file_on_disk']= file_exists($dest) ? 'YES ('.$dest.')' : 'NO';
      $savedFile = $newName;
    } elseif(copy($file['tmp_name'], $dest)){
      $result['6_move_result'] = 'SUCCESS via copy() — saved as '.$newName;
      $result['7_file_on_disk']= file_exists($dest) ? 'YES ('.$dest.')' : 'NO';
      $savedFile = $newName;
    } else {
      $result['6_move_result'] = 'FAILED — both move_uploaded_file and copy() failed';
      $result['7_error']       = error_get_last()['message'] ?? 'no error info';
    }

    // Try saving to DB
    if(!empty($savedFile)){
      try {
        // Ensure image column exists
        $col = $pdo->query("SHOW COLUMNS FROM products LIKE 'image'")->fetch();
        if(!$col) $pdo->exec("ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL");

        $pdo->prepare("INSERT INTO products(name,category,price,shape,material,color,image,description,active) VALUES(?,?,?,?,?,?,?,?,?)")
            ->execute(['TEST UPLOAD PRODUCT','Prescription',0,'Round','Metal','Black',$savedFile,'Auto-created by upload test',0]);
        $newId = $pdo->lastInsertId();
        $result['8_db_save']  = 'SUCCESS — inserted as product ID '.$newId.' (active=0, hidden from site)';
        $result['9_view_url'] = 'http://localhost/lapaire-faithful-final/lapaire-faithful-final/lapaire-faithful-final/assets/uploads/frames/'.$savedFile;
      } catch(Exception $e){
        $result['8_db_save'] = 'DB ERROR: '.$e->getMessage();
      }
    }
  } else {
    $result['6_php_upload_error'] = 'PHP upload error code: '.$file['error'].' | upload_max_filesize='.ini_get('upload_max_filesize').' | post_max_size='.ini_get('post_max_size');
  }
}

// ── Check existing DB rows ───────────────────────────────────────────────────
try {
  $rows = $pdo->query("SELECT id, name, image FROM products ORDER BY id DESC LIMIT 10")->fetchAll();
} catch(Exception $e){ $rows = []; $dbError = $e->getMessage(); }
?>
<!doctype html>
<html><head><meta charset="utf-8">
<title>Upload Test</title>
<style>
body{font-family:Arial;max-width:700px;margin:40px auto;padding:20px}
.ok{color:green;font-weight:bold} .fail{color:red;font-weight:bold}
table{width:100%;border-collapse:collapse;margin-top:16px}
td,th{border:1px solid #ddd;padding:8px 12px;text-align:left}
th{background:#f5f5f5}
.result-box{background:#f0f8ff;border:1px solid #b0d4f1;border-radius:8px;padding:16px;margin-bottom:20px}
form{background:#fafafa;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:24px}
</style>
</head>
<body>

<h2>🔍 Image Upload Test</h2>

<form method="post" enctype="multipart/form-data">
  <label><b>Pick any image from your computer:</b></label><br><br>
  <input type="file" name="img" accept="image/*" required style="margin-bottom:12px;display:block">
  <button type="submit" style="background:#0d6efd;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:15px">
    Upload &amp; Test →
  </button>
</form>

<?php if($result): ?>
<div class="result-box">
  <h3>Test Results:</h3>
  <table>
    <?php foreach($result as $k=>$v): ?>
    <tr>
      <td><b><?=htmlspecialchars($k)?></b></td>
      <td class="<?=str_contains(strtolower($v),'success')||$v==='YES'?'ok':(str_contains(strtolower($v),'fail')||$v==='NO'?'fail':'')?>">
        <?php if(str_starts_with($v,'http')): ?>
        <?=htmlspecialchars($v)?><br>
        <img src="<?=htmlspecialchars($v)?>" style="max-width:200px;max-height:150px;margin-top:8px;border:1px solid #ddd;border-radius:4px" onerror="this.after(' ❌ image not accessible at this URL')">
        <?php else: ?>
        <?=htmlspecialchars($v)?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<h3>Current products in DB (latest 10):</h3>
<?php if(!empty($dbError)): ?>
<p style="color:red">DB Error: <?=htmlspecialchars($dbError)?></p>
<?php elseif($rows): ?>
<table>
  <tr><th>ID</th><th>Name</th><th>image column value</th><th>Image preview</th></tr>
  <?php foreach($rows as $r): ?>
  <tr>
    <td><?=$r['id']?></td>
    <td><?=htmlspecialchars($r['name'])?></td>
    <td><?=htmlspecialchars($r['image']??'(null/empty)')?></td>
    <td>
      <?php if($r['image']): ?>
      <img src="../assets/uploads/frames/<?=htmlspecialchars($r['image'])?>"
           style="height:60px;border-radius:4px"
           onerror="this.replaceWith('❌ file not found on disk')">
      <?php else: ?>
      <span style="color:red">❌ no image saved in DB</span>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<p style="color:orange">No products in database yet. <a href="setup_admin.php">Import the SQL first</a>.</p>
<?php endif; ?>

</body></html>
