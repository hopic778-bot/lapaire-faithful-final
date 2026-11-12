<?php
require '../includes/config.php';
require '../includes/frame_image.php';
admin_only();
$adminTitle = 'Frames & Products';
$uploadDir  = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'frames' . DIRECTORY_SEPARATOR;
$uploadUrl  = UPLOADS_URL.'frames/';

// ── Ensure upload directory and image column exist ───────────────────────────
if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
try {
  $col = $pdo->query("SHOW COLUMNS FROM products LIKE 'image'")->fetch();
  if(!$col) $pdo->exec("ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL AFTER color");
} catch(Exception $e){ /* ignore */ }

// ── Handle POST ──────────────────────────────────────────────────────────────
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $action = $_POST['action']??'';

  if($action==='save'){
    $id = (int)($_POST['id']??0);

    // Ensure upload directory exists
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Handle image upload
    $imagePath = trim($_POST['existing_image']??'');
    $debugLog  = []; // collect debug info

    if(!empty($_FILES['frame_image']['name'])){
      $file = $_FILES['frame_image'];
      $debugLog[] = 'File received: '.$file['name'].' | size: '.$file['size'].' | error: '.$file['error'];

      if($file['error'] !== UPLOAD_ERR_OK){
        $errLabels = [1=>'INI_SIZE',2=>'FORM_SIZE',3=>'PARTIAL',4=>'NO_FILE',6=>'NO_TMP_DIR',7=>'CANT_WRITE',8=>'EXTENSION'];
        $uploadErr = 'PHP upload error '.($errLabels[$file['error']]??$file['error']).'. upload_max_filesize='.ini_get('upload_max_filesize');
        $debugLog[] = 'ERROR: '.$uploadErr;
      } else {
        // Detect extension from MIME type
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $debugLog[] = 'Real MIME: '.$realMime;

        $mimeToExt = ['image/jpeg'=>'jpg','image/jpg'=>'jpg','image/pjpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif','image/bmp'=>'jpg','image/tiff'=>'jpg','image/x-ms-bmp'=>'jpg','image/svg+xml'=>'svg','image/avif'=>'jpg','image/heic'=>'jpg','image/heif'=>'jpg'];
        $origExt   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap    = ['jfif'=>'jpg','jpe'=>'jpg','jpeg'=>'jpg','jpg'=>'jpg','png'=>'png','webp'=>'webp','gif'=>'gif','bmp'=>'jpg','tif'=>'jpg','tiff'=>'jpg','avif'=>'jpg','heic'=>'jpg','heif'=>'jpg'];
        $saveExt   = $mimeToExt[$realMime] ?? $extMap[$origExt] ?? 'jpg';
        $debugLog[] = 'Save ext: '.$saveExt;

        $newName = 'frame_'.time().'_'.bin2hex(random_bytes(4)).'.'.$saveExt;
        $dest    = $uploadDir.$newName;
        $debugLog[] = 'Destination: '.$dest;

        if(move_uploaded_file($file['tmp_name'], $dest)){
          if($imagePath && file_exists($uploadDir.$imagePath)) @unlink($uploadDir.$imagePath);
          $imagePath = $newName;
          $debugLog[] = 'SUCCESS: saved as '.$newName;
        } else {
          // Fallback: try copy() — sometimes needed on WAMP/Windows
          if(copy($file['tmp_name'], $dest)){
            if($imagePath && file_exists($uploadDir.$imagePath)) @unlink($uploadDir.$imagePath);
            $imagePath = $newName;
            $debugLog[] = 'SUCCESS (via copy): saved as '.$newName;
          } else {
            $uploadErr = 'Could not save image. Dir: '.$uploadDir.' | Writable: '.(is_writable($uploadDir)?'YES':'NO').' | PHP tmp: '.$file['tmp_name'];
            $debugLog[] = 'FAILED: '.$uploadErr;
          }
        }
      }
    } else {
      $debugLog[] = 'No file in $_FILES[frame_image] — keeping existing: '.($imagePath?:' (none)');
    }

    // Always save to DB (even if no image uploaded)
    $v = [
      trim($_POST['name']),
      $_POST['category'],
      (float)$_POST['price'],
      trim($_POST['shape']),
      trim($_POST['material']),
      trim($_POST['color']),
      $imagePath,   // will be filename if uploaded, or existing, or empty
      trim($_POST['description']),
      isset($_POST['active'])?1:0,
    ];
    $debugLog[] = 'imagePath saved to DB: '.($imagePath?:' (empty)');

    if($id){
      $v[]=$id;
      $pdo->prepare('UPDATE products SET name=?,category=?,price=?,shape=?,material=?,color=?,image=?,description=?,active=? WHERE id=?')->execute($v);
      $debugLog[] = 'Updated product ID '.$id;
    } else {
      $pdo->prepare('INSERT INTO products(name,category,price,shape,material,color,image,description,active) VALUES(?,?,?,?,?,?,?,?,?)')->execute($v);
      $debugLog[] = 'Inserted new product, last ID: '.$pdo->lastInsertId();
    }

    // Store debug in session so we can show it after redirect
    $_SESSION['upload_debug'] = $debugLog;
    $_SESSION['upload_err']   = $uploadErr ?? '';

    // If upload failed, stay on page to show error clearly — don't redirect
    if(!empty($uploadErr)){
      $showDebug = true;
      $showDebugErr = $uploadErr;
    } else {
      header('Location:products.php?saved=1');exit;
    }
  }

  if($action==='delete'){
    $pid = (int)$_POST['id'];
    // Delete uploaded image too
    $row = $pdo->prepare('SELECT image FROM products WHERE id=?');
    $row->execute([$pid]);$row=$row->fetch();
    if($row && $row['image'] && file_exists($uploadDir.$row['image']))
      @unlink($uploadDir.$row['image']);
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$pid]);
    header('Location:products.php');exit;
  }
}

// ── Load data ────────────────────────────────────────────────────────────────
$rows = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
$edit = null;
if(isset($_GET['edit'])){
  $st=$pdo->prepare('SELECT * FROM products WHERE id=?');
  $st->execute([(int)$_GET['edit']]);
  $edit=$st->fetch();
}

$shapes    = ['Rectangle','Round','Oval','Square','Cat-eye','Wayfarer','Aviator','Rimless','Semi-rimless'];
$colors    = ['Black','Brown','Tortoise','Gold','Silver','Grey','Blue','Red','Pink','White','Clear','Green'];
$materials = ['Metal','Plastic','TR90','Titanium','Acetate','Mixed','Rubber'];
$categories= ['Prescription','Sunglasses','Blue Light','Kids'];

// Current preview image
$currentImg = $edit && $edit['image']
  ? $uploadUrl.$edit['image']
  : frame_image($edit['color']??'',$edit['shape']??'',$edit['category']??'');

include '_top.php';
?>

<?php
// Show success message after redirect
if(isset($_GET['saved'])):?>
<div class="alert alert-success mb-3">✔ Product saved successfully.</div>
<?php endif;?>

<?php
// Show debug/error inline if upload failed (no redirect happened)
if(!empty($showDebug)):?>
<div class="alert alert-danger mb-3" style="font-family:monospace;font-size:13px">
  <b style="font-size:15px">❌ Image upload failed — details below:</b>
  <div class="mt-2 p-2 bg-white rounded">
    <b>Error:</b> <?=e($showDebugErr)?><br><br>
    <b>Full log:</b>
    <ul class="mb-0 mt-1">
      <?php foreach($debugLog as $line):?>
      <li><?=e($line)?></li>
      <?php endforeach;?>
    </ul>
  </div>
  <div class="mt-2">The product was saved <b>without an image</b>. Fix the error above and edit the product to add the image.</div>
</div>
<?php endif;?>

<div class="row g-4">

  <!-- ── Form ── -->
  <div class="col-xl-4">
    <div class="admin-card">
      <h6 class="admin-card-title mb-3"><?=$edit?'✏️ Edit Frame':'➕ Add New Frame'?></h6>

      <!-- Image preview -->
      <div id="framePreview" class="frame-preview-box mb-2"
           style="background-image:url('<?=$currentImg?>')"></div>

      <form method="post" enctype="multipart/form-data" id="productForm" onsubmit="return validateForm()">
        <input type="hidden" name="csrf"           value="<?=csrf()?>">
        <input type="hidden" name="action"         value="save">
        <input type="hidden" name="id"             value="<?=$edit['id']??''?>">
        <input type="hidden" name="existing_image" value="<?=e($edit['image']??'')?>">

        <!-- ── Image upload ── -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Frame Image
            <small class="text-muted fw-normal">(JPG/PNG/WEBP, max 5MB)</small>
          </label>
          <div class="upload-area" id="uploadArea">
            <input type="file" name="frame_image" id="frameImageInput"
                   accept="image/*"
                   class="upload-input">
            <div class="upload-placeholder" id="uploadPlaceholder">
              <?php if($edit && $edit['image']):?>
              <div class="upload-has-image">
                <img src="<?=$uploadUrl.e($edit['image'])?>" alt="Current" class="upload-current-img">
                <div class="small text-muted mt-1">Click to change image</div>
              </div>
              <?php else:?>
              <div class="upload-empty">
                <div class="upload-icon">📷</div>
                <div class="fw-semibold">Click to upload image</div>
                <div class="small text-muted">or drag and drop here</div>
              </div>
              <?php endif;?>
            </div>
          </div>
        </div>

        <!-- Name -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Frame Name</label>
          <input required name="name" value="<?=e($edit['name']??'')?>"
                 class="form-control" placeholder="e.g. Kebili-C1">
        </div>

        <!-- Category -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Category</label>
          <select name="category" id="sel_category" class="form-select">
            <?php foreach($categories as $v):?>
            <option <?=($edit['category']??'')===$v?'selected':''?>><?=$v?></option>
            <?php endforeach;?>
          </select>
        </div>

        <!-- Shape + Color -->
        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold">Shape</label>
            <select name="shape" id="sel_shape" class="form-select">
              <option value="">— Select —</option>
              <?php foreach($shapes as $v):?>
              <option <?=($edit['shape']??'')===$v?'selected':''?>><?=$v?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold">Color</label>
            <select name="color" id="sel_color" class="form-select">
              <option value="">— Select —</option>
              <?php foreach($colors as $v):?>
              <option <?=($edit['color']??'')===$v?'selected':''?>><?=$v?></option>
              <?php endforeach;?>
            </select>
          </div>
        </div>

        <!-- Material + Price -->
        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold">Material</label>
            <select name="material" class="form-select">
              <option value="">— Select —</option>
              <?php foreach($materials as $v):?>
              <option <?=($edit['material']??'')===$v?'selected':''?>><?=$v?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold">Price (UGX)</label>
            <input required type="number" name="price"
                   value="<?=e($edit['price']??'')?>"
                   class="form-control" placeholder="105000">
          </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Description</label>
          <textarea name="description" class="form-control" rows="3"
                    placeholder="Short description..."><?=e($edit['description']??'')?></textarea>
        </div>

        <!-- Active -->
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" name="active" id="chk_active"
                 <?=!isset($edit['active'])||$edit['active']?'checked':''?>>
          <label class="form-check-label fw-semibold" for="chk_active">Active (visible on site)</label>
        </div>

        <button class="btn btn-primary w-100 rounded-pill py-2">
          <?=$edit?'Update Frame':'Add Frame'?> &rarr;
        </button>
        <?php if($edit):?>
        <a href="products.php" class="btn btn-outline-secondary w-100 rounded-pill py-2 mt-2">Cancel</a>
        <?php endif;?>
      </form>
    </div>
  </div>

  <!-- ── Table ── -->
  <div class="col-xl-8">
    <div class="admin-card">
      <h6 class="admin-card-title mb-3">All Frames (<?=count($rows)?>)</h6>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Image</th><th>Name</th><th>Category</th><th>Shape · Color</th><th>Price</th><th>Status</th><th></th></tr>
          </thead>
          <tbody>
          <?php foreach($rows as $r):
            $img = $r['image']
              ? $uploadUrl.$r['image']
              : frame_image($r['color']??'',$r['shape']??'',$r['category']??'');
          ?>
          <tr>
            <td>
              <div class="frame-thumb" style="background-image:url('<?=$img?>')"></div>
            </td>
            <td>
              <b><?=e($r['name'])?></b>
              <div class="small text-muted"><?=e($r['material']??'')?></div>
            </td>
            <td><span class="badge badge-soft"><?=e($r['category'])?></span></td>
            <td class="small">
              <?=e($r['shape']??'—')?>
              <div class="d-flex align-items-center gap-1 mt-1">
                <div class="color-dot" style="background:<?=strtolower($r['color']??'#ccc')?>"></div>
                <span><?=e($r['color']??'—')?></span>
              </div>
            </td>
            <td class="fw-semibold">UGX <?=number_format($r['price'])?></td>
            <td>
              <span class="badge bg-<?=$r['active']?'success':'secondary'?>">
                <?=$r['active']?'Active':'Hidden'?>
              </span>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a class="btn btn-sm btn-outline-primary rounded-pill px-2" href="?edit=<?=$r['id']?>">Edit</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this frame?')">
                  <input type="hidden" name="csrf" value="<?=csrf()?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?=$r['id']?>">
                  <button class="btn btn-sm btn-outline-danger rounded-pill px-2">Del</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script>
// ── Image upload preview ────────────────────────────────────────────────────
const input       = document.getElementById('frameImageInput');
const uploadArea  = document.getElementById('uploadArea');
const placeholder = document.getElementById('uploadPlaceholder');
const preview     = document.getElementById('framePreview');

input.addEventListener('change', function(){
  if(!this.files[0]) return;
  const file = this.files[0];
  const sizeMB = (file.size / 1024 / 1024).toFixed(2);

  // Remove any previous size warning
  const oldWarn = document.getElementById('sizeWarning');
  if(oldWarn) oldWarn.remove();

  // Show file info
  const info = document.createElement('div');
  info.id = 'sizeWarning';
  info.className = 'alert alert-success mt-2 mb-0 py-2';
  info.innerHTML = `✔ Image selected: <b>${file.name}</b> (${sizeMB} MB)`;
  uploadArea.after(info);

  const url = URL.createObjectURL(file);
  preview.style.backgroundImage = 'url('+url+')';
  placeholder.innerHTML = `
    <div class="upload-has-image">
      <img src="${url}" alt="Preview" class="upload-current-img">
      <div class="small text-muted mt-1">${file.name} (${sizeMB} MB)</div>
    </div>`;
});

// Drag & drop
uploadArea.addEventListener('dragover', e=>{e.preventDefault();uploadArea.classList.add('drag-over')});
uploadArea.addEventListener('dragleave',()=>uploadArea.classList.remove('drag-over'));
uploadArea.addEventListener('drop', e=>{
  e.preventDefault();uploadArea.classList.remove('drag-over');
  if(e.dataTransfer.files[0]){
    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event('change'));
  }
});

function validateForm(){
  return true; // all image types and sizes accepted
}

// ── Auto-preview from color/category dropdowns (when no image uploaded) ─────
const previewMap = {
  'sun_gold':'https://images.unsplash.com/photo-1509822929063-6b6cfc9b42f2?w=600&q=80',
  'sun_brown':'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80',
  'sun_':'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600&q=80',
  'kid_red':'https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=600&q=80',
  'kid_blue':'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=600&q=80',
  'kid_':'https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=600&q=80',
  'blue_clear':'https://images.unsplash.com/photo-1625591340248-787d6dc4a78a?w=600&q=80',
  'blue_':'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=600&q=80',
  'presc_black':'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80',
  'presc_brown':'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80',
  'presc_tortoise':'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80',
  'presc_gold':'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=600&q=80',
  'presc_silver':'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&q=80',
  'presc_grey':'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&q=80',
  'presc_blue':'https://images.unsplash.com/photo-1625591340248-787d6dc4a78a?w=600&q=80',
  'presc_red':'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=600&q=80',
  'presc_':'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80',
};
function autoPreview(){
  if(input.files && input.files[0]) return; // user already uploaded — don't override
  const cat   = (document.getElementById('sel_category').value||'').toLowerCase();
  const color = (document.getElementById('sel_color').value||'').toLowerCase();
  let prefix = cat.includes('sun')?'sun_':cat.includes('kid')?'kid_':cat.includes('blue')?'blue_':'presc_';
  let key = prefix+color;
  let img = previewMap[key] || previewMap[prefix] || 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
  preview.style.backgroundImage = 'url('+img+')';
}
document.getElementById('sel_category').addEventListener('change', autoPreview);
document.getElementById('sel_color').addEventListener('change', autoPreview);
autoPreview();
</script>

<?php include '_bottom.php';?>
