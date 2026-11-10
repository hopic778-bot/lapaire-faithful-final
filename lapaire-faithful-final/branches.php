<?php
require 'includes/config.php';
$pageTitle='Branches';$active='Branches';
$branches=$pdo->query('SELECT * FROM branches WHERE active=1 ORDER BY city,name')->fetchAll();
// group by city for counts
$byCityCount=[];
foreach($branches as $b){ $byCityCount[$b['city']]=($byCityCount[$b['city']]??0)+1; }
include 'includes/header.php';
?>

<!-- Page Hero — image banner -->
<div class="services-cta-banner">
  <img src="https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=1400&q=85" alt="Lapaire store branch" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">FIND US NEAR YOU</span>
    <h1 class="fw-bold display-4 text-white mt-2">Our Branches<br>in Uganda</h1>
    <p class="text-white opacity-75 fs-5 mt-3">18 branches across 6 cities.<br>Walk in for a free eye test — no appointment needed.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
      <a class="sl-btn sl-btn--primary" href="appointment.php">Book Eye Test &rarr;</a>
      <a class="sl-btn sl-btn--outline" href="#branchList">Find a Branch</a>
    </div>
  </div>
</div>

<section class="section soft">
  <div class="container">
    <div class="row g-4">

      <!-- Branch list -->
      <div class="col-lg-5">
        <div class="cardx p-4">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <input id="branchSearch" class="form-control" placeholder="Search city or branch...">
            </div>
            <div class="col-4">
              <select id="cityFilter" class="form-select">
                <option value="">All Cities</option>
                <?php foreach(array_keys($byCityCount) as $city):?>
                <option><?=e($city)?></option>
                <?php endforeach;?>
              </select>
            </div>
          </div>

          <div id="branchList">
            <?php foreach($branches as $b):?>
            <div class="branch-item py-3 border-bottom" data-city="<?=e($b['city'])?>">
              <div class="d-flex align-items-start gap-2">
                <span class="text-primary mt-1">📍</span>
                <div class="flex-grow-1">
                  <b><?=e($b['name'])?></b>
                  <div class="small text-muted"><?=e($b['city'])?></div>
                  <?php if($b['address']):?>
                  <div class="small"><?=e($b['address'])?></div>
                  <?php endif;?>
                </div>
                <a href="appointment.php?branch=<?=$b['id']?>" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0">Book</a>
              </div>
            </div>
            <?php endforeach;?>
            <?php if(!$branches):?>
            <p class="text-muted py-3">No branches found.</p>
            <?php endif;?>
          </div>
        </div>
      </div>

      <!-- Map placeholder -->
      <div class="col-lg-7">
        <div class="branch-map">
          <div class="text-center">
            <div class="map-shape">🗺️</div>
            <h2 class="title mt-2">18 Branches</h2>
            <p class="text-muted">6 cities across Uganda</p>
            <?php foreach($byCityCount as $city=>$cnt):?>
            <span class="badge badge-soft me-1 mb-1"><?=e($city)?> (<?=$cnt?>)</span>
            <?php endforeach;?>
          </div>
        </div>
      </div>

    </div>

    <!-- Featured store card -->
    <div class="row g-4 mt-4">
      <div class="col-lg-5">
        <div class="store-card-img" style="background-image:url('assets/images/storefront.png')"></div>
      </div>
      <div class="col-lg-7">
        <div class="cardx p-4 h-100 d-flex flex-column justify-content-center">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="text-primary fs-5">◉</span>
            <span class="fw-semibold text-muted small text-uppercase">Featured Branch</span>
          </div>
          <h3 class="fw-bold">Kampala Road</h3>
          <p class="text-muted">I&amp;M Bank Building, Opposite FoodHub,<br>Kampala Road, Kampala</p>
          <p><span class="text-primary">☎</span> +256 775 488 056</p>
          <div class="d-flex gap-3 mt-2">
            <a class="btn btn-outline-primary rounded-pill px-4" href="https://maps.google.com" target="_blank">Get Directions</a>
            <a class="btn btn-primary rounded-pill px-4" href="appointment.php">Book Here</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded',()=>{
  const q=document.querySelector('#branchSearch');
  const cf=document.querySelector('#cityFilter');
  function filter(){
    const v=q.value.toLowerCase();
    const city=(cf.value||'').toLowerCase();
    document.querySelectorAll('.branch-item').forEach(x=>{
      const txt=x.innerText.toLowerCase();
      const xCity=(x.dataset.city||'').toLowerCase();
      x.hidden=!(txt.includes(v)&&(!city||xCity===city));
    });
  }
  q.oninput=filter;
  cf.onchange=filter;
});
</script>

<?php include 'includes/footer.php';?>
