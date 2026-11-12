<?php
require '../includes/config.php';
require '../includes/cloudinary.php';
admin_only();
$adminTitle = 'Blog Posts';

// Local dir kept for old images
$uploadDir = dirname(__DIR__).'/assets/uploads/blog/';
if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

try {
  $col = $pdo->query("SHOW COLUMNS FROM posts LIKE 'image'")->fetch();
  if(!$col) $pdo->exec("ALTER TABLE posts ADD COLUMN image VARCHAR(500) NULL AFTER excerpt");
  $pdo->exec("ALTER TABLE posts MODIFY COLUMN image VARCHAR(500) NULL");
} catch(Exception $e){}

function post_img_url(string $val): string {
  if(!$val) return '';
  if(is_cloudinary_url($val)) return $val;
  return UPLOADS_URL.'blog/'.$val;
}

// ── Handle POST ──────────────────────────────────────────────────────────────
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();

  if($_POST['action']==='save'){
    $id        = (int)($_POST['id']??0);
    $imageVal  = trim($_POST['existing_image']??'');
    $uploadErr = '';

    if(!empty($_FILES['post_image']['name']) && $_FILES['post_image']['error'] !== UPLOAD_ERR_NO_FILE){
      $file = $_FILES['post_image'];
      if($file['error'] !== UPLOAD_ERR_OK){
        $uploadErr = 'Upload error code: '.$file['error'];
      } else {
        try {
          $result   = cloudinary_upload($file['tmp_name'], 'lapaire/blog');
          $imageVal = $result['url'];
        } catch(RuntimeException $ex){
          $uploadErr = $ex->getMessage();
        }
      }
    }

    $v = [trim($_POST['title']), trim($_POST['excerpt']), $imageVal, trim($_POST['body']), isset($_POST['published'])?1:0];
    if($id){
      $v[]=$id;
      $pdo->prepare('UPDATE posts SET title=?,excerpt=?,image=?,body=?,published=? WHERE id=?')->execute($v);
    } else {
      $pdo->prepare('INSERT INTO posts(title,excerpt,image,body,published) VALUES(?,?,?,?,?)')->execute($v);
    }

  } else {
    $pid = (int)$_POST['id'];
    $row = $pdo->prepare('SELECT image FROM posts WHERE id=?');
    $row->execute([$pid]); $row = $row->fetch();
    if($row && $row['image'] && !is_cloudinary_url($row['image']) && file_exists($uploadDir.$row['image']))
      @unlink($uploadDir.$row['image']);
    $pdo->prepare('DELETE FROM posts WHERE id=?')->execute([$pid]);
  }

  header('Location:posts.php');exit;
}

// ── Load data ────────────────────────────────────────────────────────────────
$rows = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();
$edit = null;
if(isset($_GET['edit'])){
  $s=$pdo->prepare('SELECT * FROM posts WHERE id=?');
  $s->execute([(int)$_GET['edit']]);
  $edit=$s->fetch();
}

include '_top.php';
?>

<?php if(!empty($uploadErr)):?>
<div class="alert alert-danger mb-3"><b>Image Error:</b> <?=e($uploadErr)?></div>
<?php endif;?>

<div class="row g-4">

  <!-- ── Form ── -->
  <div class="col-xl-5">
    <div class="admin-card">
      <h6 class="admin-card-title mb-3"><?=$edit?'✏️ Edit Post':'➕ New Post'?></h6>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf"           value="<?=csrf()?>">
        <input type="hidden" name="action"         value="save">
        <input type="hidden" name="id"             value="<?=$edit['id']??''?>">
        <input type="hidden" name="existing_image" value="<?=e($edit['image']??'')?>">

        <!-- Image upload -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Cover Image <small class="text-muted fw-normal">(any image type)</small></label>

          <?php if($edit && $edit['image']):?>
          <div class="mb-2">
            <img src="<?=e(post_img_url($edit['image']))?>" alt="Current"
                 style="width:100%;height:160px;object-fit:cover;border-radius:10px;border:1px solid #dee2e6">
            <div class="small text-muted mt-1">Current image — upload a new one to replace it</div>
          </div>
          <?php endif;?>

          <div id="blogUploadArea" style="border:2px dashed #cbd5e0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;background:#f8fafc;position:relative;">
            <input type="file" name="post_image" id="blogImageInput" accept="image/*"
                   style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%">
            <div id="blogUploadPlaceholder">
              <div style="font-size:2rem">🖼️</div>
              <div class="fw-semibold">Click to upload cover image</div>
              <div class="small text-muted">JPG, PNG, WEBP, GIF — any size</div>
            </div>
          </div>
          <div id="blogImageInfo" class="small text-success mt-1" style="display:none"></div>
        </div>

        <!-- Title -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Title</label>
          <input required name="title" value="<?=e($edit['title']??'')?>"
                 class="form-control" placeholder="Post title">
        </div>

        <!-- Excerpt -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Excerpt <small class="text-muted fw-normal">(short summary)</small></label>
          <textarea required name="excerpt" class="form-control" rows="2"
                    placeholder="Short summary shown on blog listing..."><?=e($edit['excerpt']??'')?></textarea>
        </div>

        <!-- Body -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Article Body</label>
          <textarea required name="body" rows="8" class="form-control"
                    placeholder="Full article content..."><?=e($edit['body']??'')?></textarea>
        </div>

        <!-- Published -->
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" name="published" id="chk_published"
                 <?=!isset($edit['published'])||$edit['published']?'checked':''?>>
          <label class="form-check-label fw-semibold" for="chk_published">Published (visible on site)</label>
        </div>

        <button class="btn btn-primary w-100 rounded-pill py-2">
          <?=$edit?'Update Post':'Add Post'?> &rarr;
        </button>
        <?php if($edit):?>
        <a href="posts.php" class="btn btn-outline-secondary w-100 rounded-pill py-2 mt-2">Cancel</a>
        <?php endif;?>
      </form>
    </div>
  </div>

  <!-- ── Posts list ── -->
  <div class="col-xl-7">
    <div class="admin-card">
      <h6 class="admin-card-title mb-3">All Posts (<?=count($rows)?>)</h6>
      <?php foreach($rows as $r):?>
      <div class="d-flex gap-3 align-items-start p-3 mb-2 rounded-3 border bg-white">

        <!-- thumbnail -->
        <?php if($r['image']):?>
        <img src="<?=e(post_img_url($r['image']))?>" alt=""
             style="width:80px;height:60px;object-fit:cover;border-radius:8px;flex-shrink:0">
        <?php else:?>
        <div style="width:80px;height:60px;background:#eff8ff;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.5rem">📰</div>
        <?php endif;?>

        <div class="flex-grow-1 min-width-0">
          <b><?=e($r['title'])?></b>
          <div class="small text-muted mt-1"><?=e(substr($r['excerpt'],0,80)).(strlen($r['excerpt'])>80?'…':'')?></div>
          <div class="d-flex gap-2 mt-2 align-items-center">
            <span class="badge bg-<?=$r['published']?'success':'secondary'?>"><?=$r['published']?'Published':'Draft'?></span>
            <small class="text-muted"><?=date('d M Y',strtotime($r['created_at']))?></small>
            <a href="?edit=<?=$r['id']?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 ms-auto">Edit</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this post?')">
              <input type="hidden" name="csrf" value="<?=csrf()?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=$r['id']?>">
              <button class="btn btn-sm btn-outline-danger rounded-pill px-2">Del</button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach;?>
      <?php if(!$rows):?>
      <p class="text-muted text-center py-4">No posts yet. Add your first post →</p>
      <?php endif;?>
    </div>
  </div>

</div>

<script>
const blogInput = document.getElementById('blogImageInput');
const blogArea  = document.getElementById('blogUploadArea');
const blogInfo  = document.getElementById('blogImageInfo');
const blogPlaceholder = document.getElementById('blogUploadPlaceholder');

blogInput.addEventListener('change', function(){
  if(!this.files[0]) return;
  const file = this.files[0];
  const url  = URL.createObjectURL(file);
  blogPlaceholder.innerHTML = `<img src="${url}" style="width:100%;height:150px;object-fit:cover;border-radius:8px">`;
  blogInfo.style.display = 'block';
  blogInfo.textContent   = '✔ ' + file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)';
});

// Drag & drop
blogArea.addEventListener('dragover',  e=>{e.preventDefault();blogArea.style.borderColor='#0d6efd'});
blogArea.addEventListener('dragleave', ()=>blogArea.style.borderColor='#cbd5e0');
blogArea.addEventListener('drop', e=>{
  e.preventDefault(); blogArea.style.borderColor='#cbd5e0';
  if(e.dataTransfer.files[0]){
    blogInput.files = e.dataTransfer.files;
    blogInput.dispatchEvent(new Event('change'));
  }
});
</script>

<?php include '_bottom.php';?>
