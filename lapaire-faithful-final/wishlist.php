<?php
require 'includes/config.php';
if(!user_logged_in()){header('Location:user_auth.php?action=login&redirect=wishlist.php');exit;}
$pageTitle='My Wishlist';$active='';
$items=$pdo->prepare('SELECT p.*,w.created_at wishlisted_at FROM wishlist w JOIN products p ON p.id=w.product_id WHERE w.user_id=? ORDER BY w.created_at DESC');
$items->execute([user_id()]);$items=$items->fetchAll();
require 'includes/frame_image.php';
include 'includes/header.php';
?>

<div class="services-cta-banner" style="min-height:280px">
  <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=1400&q=85" alt="My Wishlist" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">YOUR SAVED FRAMES</span>
    <h1 class="fw-bold display-5 text-white mt-2">My Wishlist</h1>
    <p class="text-white opacity-75 mt-2">Hello, <?=e($_SESSION['user_name'])?>! Here are your saved frames.</p>
  </div>
</div>

<section class="section soft">
  <div class="container">
    <?php if($items):?>
    <div class="row g-4">
      <?php foreach($items as $p):?>
      <div class="col-md-6 col-lg-4">
        <div class="cardx product-card h-100">
          <div class="frame-photo position-relative" style="background-image:url('<?=!empty($p['image'])?'assets/uploads/frames/'.e($p['image']):frame_image($p['color']??'',$p['shape']??'',$p['category']??'')?>')">
            <button class="wishlist-btn position-absolute top-0 end-0 m-2 wishlisted"
              data-id="<?=$p['id']?>" aria-label="Remove from wishlist">♥</button>
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
      <div class="fs-1 mb-3">♡</div>
      <h4>Your wishlist is empty</h4>
      <p class="text-muted">Browse our glasses collection and save your favourites.</p>
      <a href="glasses.php" class="sl-btn sl-btn--primary d-inline-block mt-3">Browse Frames &rarr;</a>
    </div>
    <?php endif;?>
  </div>
</section>

<input type="hidden" id="csrf_token" value="<?=csrf()?>">
<script>
document.querySelectorAll('.wishlist-btn[data-id]').forEach(btn=>{
  btn.addEventListener('click',function(){
    const pid=this.dataset.id;
    const fd=new FormData();
    fd.append('csrf',document.getElementById('csrf_token').value);
    fd.append('product_id',pid);
    fetch('wishlist_action.php',{method:'POST',body:fd})
      .then(r=>r.json()).then(d=>{
        if(d.status==='removed'){
          this.closest('.col-md-6').remove();
          if(!document.querySelector('.wishlist-btn')) location.reload();
        }
      });
  });
});
</script>
<?php include 'includes/footer.php';?>
