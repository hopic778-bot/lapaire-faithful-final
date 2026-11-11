<?php
/**
 * Upload test page — visit, pick an image, submit, and see exactly what happens.
 * http://localhost/lapaire-faithful-final/lapaire-faithful-final/lapaire-faithful-final/database/upload_test.php
 */
$uploadDir = dirname(__DIR__).'/assets/uploads/frames/';

if($_SERVER['REQUEST_METHOD']==='POST'){
    echo '<style>body{font-family:Arial;padding:20px} .ok{color:green} .fail{color:red} pre{background:#f4f4f4;padding:10px;border-radius:4px}</style>';
    echo '<h2>Upload Test Results</h2>';

    // 1. Directory
    echo '<h3>1. Upload Directory</h3>';
    echo '<p>Path: <code>'.$uploadDir.'</code></p>';
    echo is_dir($uploadDir)
        ? '<p class="ok">✔ Directory exists</p>'
        : '<p class="fail">✘ Directory missing</p>';
    echo is_writable($uploadDir)
        ? '<p class="ok">✔ Directory is writable</p>'
        : '<p class="fail">✘ Directory is NOT writable</p>';

    // 2. Raw $_FILES dump
    echo '<h3>2. Raw $_FILES Data</h3>';
    echo '<pre>'.print_r($_FILES, true).'</pre>';

    // 3. Attempt move
    echo '<h3>3. Upload Attempt</h3>';
    if(empty($_FILES['testfile']['name'])){
        echo '<p class="fail">No file received by PHP at all.</p>';
    } elseif($_FILES['testfile']['error'] !== 0){
        $errors = [
            1=>'UPLOAD_ERR_INI_SIZE — file exceeds upload_max_filesize in php.ini ('.(ini_get('upload_max_filesize')).')',
            2=>'UPLOAD_ERR_FORM_SIZE — file exceeds MAX_FILE_SIZE in form',
            3=>'UPLOAD_ERR_PARTIAL — file only partially uploaded',
            4=>'UPLOAD_ERR_NO_FILE — no file uploaded',
            6=>'UPLOAD_ERR_NO_TMP_DIR — missing temp folder',
            7=>'UPLOAD_ERR_CANT_WRITE — failed to write to disk',
            8=>'UPLOAD_ERR_EXTENSION — extension stopped upload',
        ];
        $code = $_FILES['testfile']['error'];
        echo '<p class="fail">✘ PHP Upload Error '.$code.': '.($errors[$code]??'Unknown').'</p>';
    } else {
        $tmp  = $_FILES['testfile']['tmp_name'];
        $name = 'test_'.time().'.'.strtolower(pathinfo($_FILES['testfile']['name'], PATHINFO_EXTENSION));
        $dest = $uploadDir.$name;
        if(move_uploaded_file($tmp, $dest)){
            echo '<p class="ok">✔ File uploaded successfully!</p>';
            echo '<p>Saved as: <code>'.$dest.'</code></p>';
            echo '<p>File size: '.round(filesize($dest)/1024,1).' KB</p>';
            echo '<p>Preview:</p>';
            echo '<img src="../assets/uploads/frames/'.htmlspecialchars($name).'" style="max-width:300px;border:1px solid #ccc;border-radius:8px">';
            echo '<p style="margin-top:10px"><a href="../assets/uploads/frames/'.htmlspecialchars($name).'" target="_blank">Open image directly</a></p>';
        } else {
            echo '<p class="fail">✘ move_uploaded_file() FAILED</p>';
            echo '<p>tmp_name exists: '.(file_exists($tmp)?'yes':'no').'</p>';
            echo '<p>Destination: <code>'.$dest.'</code></p>';
        }
    }

    // 4. PHP settings
    echo '<h3>4. PHP Upload Settings</h3>';
    echo '<pre>';
    echo 'upload_max_filesize : '.ini_get('upload_max_filesize')."\n";
    echo 'post_max_size       : '.ini_get('post_max_size')."\n";
    echo 'file_uploads        : '.ini_get('file_uploads')."\n";
    echo 'upload_tmp_dir      : '.(ini_get('upload_tmp_dir')?:sys_get_temp_dir())."\n";
    echo '</pre>';

    echo '<hr><a href="upload_test.php">← Test again</a>';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Upload Test</title>
<style>body{font-family:Arial;padding:40px;max-width:500px} input{margin:10px 0;display:block} button{padding:10px 20px;background:#0d6efd;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:16px}</style>
</head>
<body>
<h2>🧪 Image Upload Test</h2>
<p>Pick any image from your computer and click Upload Test.</p>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="testfile" accept="image/*" required>
    <button type="submit">Upload Test</button>
</form>
</body>
</html>
