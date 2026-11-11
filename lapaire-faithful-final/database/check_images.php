<?php
/**
 * Diagnostic: checks image column exists, lists products and whether
 * their image files are actually on disk.
 * Visit: http://localhost/lapaire-faithful-final/database/check_images.php
 */
require dirname(__DIR__).'/includes/config.php';

$uploadDir = dirname(__DIR__).'/assets/uploads/frames/';

echo '<style>body{font-family:Arial,sans-serif;padding:20px;max-width:900px}
table{border-collapse:collapse;width:100%}
th,td{border:1px solid #ccc;padding:8px 12px;text-align:left}
th{background:#f4f4f4}
.ok{color:green;font-weight:bold}
.fail{color:red;font-weight:bold}
.warn{color:orange;font-weight:bold}
img{max-height:60px;max-width:80px;object-fit:cover;border-radius:4px}
</style>';

echo '<h2>🔍 Image Upload Diagnostics</h2>';

// 1. Check upload directory
echo '<h3>1. Upload Directory</h3>';
echo '<p>Path: <code>'.$uploadDir.'</code></p>';
if(is_dir($uploadDir)){
    echo '<p class="ok">✔ Directory exists</p>';
    $files = glob($uploadDir.'*');
    echo '<p>Files in directory: <b>'.count($files).'</b></p>';
    if($files){
        echo '<ul>';
        foreach($files as $f) echo '<li>'.basename($f).' ('.round(filesize($f)/1024,1).' KB)</li>';
        echo '</ul>';
    }
} else {
    echo '<p class="fail">✘ Directory does NOT exist — creating it now...</p>';
    if(mkdir($uploadDir, 0755, true)){
        echo '<p class="ok">✔ Directory created successfully</p>';
    } else {
        echo '<p class="fail">✘ Failed to create directory. Check folder permissions.</p>';
    }
}

// 2. Check image column exists
echo '<h3>2. Database Column Check</h3>';
try {
    $cols = $pdo->query("SHOW COLUMNS FROM products LIKE 'image'")->fetchAll();
    if($cols){
        echo '<p class="ok">✔ <code>image</code> column exists in products table</p>';
    } else {
        echo '<p class="fail">✘ <code>image</code> column is MISSING — adding it now...</p>';
        $pdo->exec("ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL AFTER color");
        echo '<p class="ok">✔ Column added successfully</p>';
    }
} catch(Exception $e){
    echo '<p class="fail">Error: '.htmlspecialchars($e->getMessage()).'</p>';
}

// 3. List all products and their image status
echo '<h3>3. Products & Image Status</h3>';
$rows = $pdo->query('SELECT id,name,category,color,shape,image FROM products ORDER BY id DESC')->fetchAll();
if(!$rows){
    echo '<p class="warn">No products in database yet.</p>';
} else {
    echo '<table><tr><th>ID</th><th>Name</th><th>Category</th><th>image (DB value)</th><th>File on disk?</th><th>Preview</th></tr>';
    foreach($rows as $r){
        $img  = $r['image'];
        $path = $uploadDir.$img;
        $exists = $img && file_exists($path);
        $webPath = '../assets/uploads/frames/'.htmlspecialchars($img??'');
        echo '<tr>';
        echo '<td>'.$r['id'].'</td>';
        echo '<td>'.htmlspecialchars($r['name']).'</td>';
        echo '<td>'.htmlspecialchars($r['category']).'</td>';
        echo '<td><code>'.htmlspecialchars($img??'(empty)').'</code></td>';
        if(!$img)
            echo '<td class="warn">— No image uploaded</td><td>—</td>';
        elseif($exists)
            echo '<td class="ok">✔ Found</td><td><img src="'.$webPath.'" alt="preview"></td>';
        else
            echo '<td class="fail">✘ File missing on disk</td><td>—</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// 4. PHP upload settings
echo '<h3>4. PHP Upload Settings</h3>';
echo '<table>';
echo '<tr><th>Setting</th><th>Value</th><th>Notes</th></tr>';
$maxFile = ini_get('upload_max_filesize');
$maxPost = ini_get('post_max_size');
$tmpDir  = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
$fileUploads = ini_get('file_uploads');
echo '<tr><td>file_uploads</td><td>'.($fileUploads?'<span class="ok">On</span>':'<span class="fail">Off</span>').'</td><td>Must be On</td></tr>';
echo '<tr><td>upload_max_filesize</td><td>'.$maxFile.'</td><td>Your max image size</td></tr>';
echo '<tr><td>post_max_size</td><td>'.$maxPost.'</td><td>Must be >= upload_max_filesize</td></tr>';
echo '<tr><td>upload_tmp_dir</td><td>'.$tmpDir.'</td><td>'.( is_writable($tmpDir)?'<span class="ok">Writable</span>':'<span class="fail">NOT Writable</span>' ).'</td></tr>';
echo '</table>';

echo '<hr><p><a href="../admin/products.php">← Go to Admin Products</a></p>';
