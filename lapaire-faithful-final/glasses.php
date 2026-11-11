<?php
require 'includes/config.php';
require 'includes/frame_image.php';
$pageTitle='Glasses';$active='Glasses';
$q=trim($_GET['q']??'');
$cat=trim($_GET['category']??'');

$sql='SELECT * FROM products WHERE active=1';$args=[];
if($q!==''){$sql.=' AND (name LIKE ? OR shape LIKE ? OR material LIKE ?)';$args=array_merge($args,array_fill(0,3,'%'.$q.'%'));}
if($cat!==''){$sql.=' AND category=?';$args[]=$cat;}
$sql.=' ORDER BY id DESC';
$st=$pdo->prepare($sql);$st->execute($args);$products=$st->fetchAll();

// total count per category
$cats=['All'=>0,'Prescription'=>0,'Sunglasses'=>0,'Blue Light'=>0,'Kids'=>0,'New Arrivals'=>0];
$allProducts=$pdo->query('SELECT category,created_at FROM products WHERE active=1')->fetchAll();
foreach($allProducts as $p){
  $cats['All']++;
  if(isset($cats[$p['category']])) $cats[$p['category']]++;
  // consider last 14 days as new
  if(strtotime($p['created_at'])>strtotime('-14 days')) $cats['New Arrivals']++;
}

include 'includes/header.php';

// Load user's current wishlist for highlighting
$userWishlist = [];
if(user_logged_in()){
  $ws = $pdo->prepare('SELECT product_id FROM wishlist WHERE user_id=?');
  $ws->execute([user_id()]);
  $userWishlist = array_column($ws->fetchAll(),'product_id');
}
?>

<!-- Page Hero — image banner -->
<div class="services-cta-banner">
  <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=1400&q=85" alt="Glasses collection" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">EYEWEAR COLLECTION</span>
    <h1 class="fw-bold display-4 text-white mt-2">Our Glasses<br><span class="sl-accent">Collection</span></h1>
    <p class="text-white opacity-75 fs-5 mt-3">Stylish frames for every style, every age,<br>every moment.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
      <a class="sl-btn sl-btn--primary" href="appointment.php">Book Eye Test &rarr;</a>
      <a class="sl-btn sl-btn--outline" href="#frames">Browse Frames</a>
    </div>
  </div>
</div>
<!-- Frames section -->
<section class="section soft" id="frames">
  <div class="container">

    <!-- Search + filter row -->
    <form class="row g-2 align-items-center mb-4" method="get">
      <div class="col-lg-7">
        <input name="q" value="<?=e($q)?>" class="form-control rounded-pill" placeholder="Search for frames...">
      </div>
      <div class="col-lg-3">
        <select name="category" class="form-select rounded-pill">
          <option value="">All Categories</option>
          <?php foreach(['Prescription','Sunglasses','Blue Light','Kids','New Arrivals'] as $v):?>
          <option <?=$cat===$v?'selected':''?>><?=$v?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="col-lg-2">
        <button class="btn btn-primary rounded-pill w-100">Search</button>
      </div>
    </form>

    <!-- Category tabs -->
    <div class="shop-filters d-flex flex-wrap gap-2 mb-4">
      <?php foreach($cats as $label=>$cnt):
        $href=$label==='All'?'glasses.php':'glasses.php?category='.urlencode($label);
        $isActive=($cat===''&&$label==='All')||($cat===$label);
      ?>
      <a href="<?=$href?>" class="btn btn-sm <?=$isActive?'btn-primary':'btn-outline-secondary'?> rounded-pill">
        <?=$label?> <?php if($cnt>0):?><span class="badge bg-white text-primary ms-1"><?=$cnt?></span><?php endif;?>
      </a>
      <?php endforeach;?>
    </div>

    <!-- Product grid -->
    <?php if($products):?>
    <div class="row g-4">
      <?php foreach($products as $p):
        $isNew    = strtotime($p['created_at']) > strtotime('-14 days');
        $wished   = in_array($p['id'], $userWishlist);
        $frameImg = !empty($p['image'])
          ? 'assets/uploads/frames/'.e($p['image'])
          : frame_image($p['color']??'', $p['shape']??'', $p['category']??'');
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="cardx product-card h-100">
          <div class="frame-photo position-relative" style="height:190px;overflow:hidden;background:#eff8ff;">
            <?php if(!empty($p['image'])):?>
            <img src="assets/uploads/frames/<?=e($p['image'])?>" 
                 alt="<?=e($p['name'])?>"
                 style="width:100%;height:190px;object-fit:cover;display:block;"
                 onerror="this.src='<?=frame_image($p['color']??'',$p['shape']??'',$p['category']??'')?>'">
            <?php else:?>
            <img src="<?=$frameImg?>" 
                 alt="<?=e($p['name'])?>"
                 style="width:100%;height:190px;object-fit:cover;display:block;">
            <?php endif;?>
            <?php if($isNew):?>
            <span class="position-absolute top-0 start-0 m-2 badge bg-primary">New</span>
            <?php endif;?>
            <button class="wishlist-btn position-absolute top-0 end-0 m-2 <?=$wished?'wishlisted':''?>"
              data-id="<?=$p['id']?>"
              aria-label="<?=$wished?'Remove from':'Add to'?> wishlist"
              title="<?=$wished?'Saved':'Save frame'?>">
              <?=$wished?'♥':'♡'?>
            </button>
          </div>
          <div class="p-4">
            <span class="badge badge-soft"><?=e($p['category'])?></span>
            <h5 class="mt-2 fw-bold"><?=e($p['name'])?></h5>
            <small class="text-muted"><?=e($p['shape'])?> · <?=e($p['material'])?></small>
            <p class="mt-2 mb-3"><b class="text-primary">UGX <?=number_format($p['price'])?></b></p>
            <a class="btn btn-outline-primary btn-sm rounded-pill" href="appointment.php">Try in branch</a>
          </div>
        </div>
      </div>
      <?php endforeach;?>
    </div>
    <?php else:?>
    <div class="text-center py-5">
      <div class="fs-1 mb-3">👓</div>
      <h4>No frames found</h4>
      <p class="text-muted">Try a different search or category.</p>
      <a href="glasses.php" class="btn btn-outline-primary rounded-pill">View All Frames</a>
    </div>
    <?php endif;?>

    <!-- CTA banner -->
    <div class="feature-blue mt-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h3 class="fw-bold mb-1">New styles every week</h3>
        <p class="mb-0 opacity-75">Stay on trend with our latest arrivals.</p>
      </div>
      <a href="glasses.php" class="btn btn-light text-primary rounded-pill px-4">View All Frames</a>
    </div>

  </div>
</section>

<!-- Auth required modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 overflow-hidden">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="authModalLabel">Save this frame</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 pb-4">
        <p class="text-muted">Sign in or create a free account to save frames to your wishlist.</p>
        <div class="d-grid gap-2 mt-3">
          <a id="authModalLogin" href="user_auth.php?action=login&redirect=glasses.php"
             class="sl-btn sl-btn--primary text-center">Sign In &rarr;</a>
          <a id="authModalRegister" href="user_auth.php?action=register&redirect=glasses.php"
             class="btn btn-outline-primary rounded-pill py-2">Create Free Account</a>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="csrf_token" value="<?=csrf()?>">
<input type="hidden" id="user_logged_in" value="<?=user_logged_in()?'1':'0'?>">

<script>
document.addEventListener('DOMContentLoaded',()=>{
  const isLoggedIn = document.getElementById('user_logged_in').value === '1';
  const csrf = document.getElementById('csrf_token').value;
  const currentUrl = encodeURIComponent(window.location.href);

  document.querySelectorAll('.wishlist-btn[data-id]').forEach(btn=>{
    btn.addEventListener('click', function(e){
      e.preventDefault();

      if(!isLoggedIn){
        // Set redirect back to this page
        document.getElementById('authModalLogin').href = 'user_auth.php?action=login&redirect=glasses.php';
        document.getElementById('authModalRegister').href = 'user_auth.php?action=register&redirect=glasses.php';
        new bootstrap.Modal(document.getElementById('authModal')).show();
        return;
      }

      const pid = this.dataset.id;
      const fd = new FormData();
      fd.append('csrf', csrf);
      fd.append('product_id', pid);

      fetch('wishlist_action.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(d=>{
          if(d.status==='added'){
            this.textContent='♥';
            this.classList.add('wishlisted');
            this.title='Saved';
          } else if(d.status==='removed'){
            this.textContent='♡';
            this.classList.remove('wishlisted');
            this.title='Save frame';
          }
        });
    });
  });
});
</script>

<?php include 'includes/footer.php';?>
