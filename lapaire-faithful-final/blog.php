<?php
require 'includes/config.php';
$pageTitle='Blog';$active='Blog';
$posts=$pdo->query("SELECT * FROM posts WHERE published=1 ORDER BY created_at DESC")->fetchAll();

// All eye care / optical specific images
$blogImgs=[
  'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&q=80', // optician exam
  'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80', // glasses display
  'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=600&q=80', // prescription glasses
  'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&q=80', // eye test chart
  'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=600&q=80',    // optician with patient
  'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600&q=80', // sunglasses
];
include 'includes/header.php';
?>

<!-- Page Hero -->
<div class="services-cta-banner">
  <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400&q=85" alt="Eye care tips" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">EYE CARE TIPS &amp; NEWS</span>
    <h1 class="fw-bold display-4 text-white mt-2">Latest News &amp;<br>Eye Care Tips</h1>
    <p class="text-white opacity-75 fs-5 mt-3">Expert advice, frame guides and updates<br>straight from the Lapaire team.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
      <a class="sl-btn sl-btn--primary" href="appointment.php">Book Eye Test &rarr;</a>
    </div>
  </div>
</div>

<section class="section soft">
  <div class="container">

    <?php if($posts):?>
    <h4 class="fw-bold mb-4">All Articles</h4>
    <div class="row g-4">
      <?php foreach($posts as $i=>$p):
        $img=$blogImgs[$i%count($blogImgs)];
      ?>
      <div class="col-md-6 col-lg-4">
        <article class="cardx h-100 overflow-hidden d-flex flex-column blog-card">
          <div class="blog-photo" style="background-image:url('<?=$img?>')">
            <span class="blog-cat-tag">Eye Care</span>
          </div>
          <div class="p-4 flex-grow-1 d-flex flex-column">
            <h5 class="fw-bold"><?=e($p['title'])?></h5>
            <p class="text-muted small flex-grow-1 mt-1"><?=e($p['excerpt'])?></p>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
              <small class="text-muted"><?=date('d M Y',strtotime($p['created_at']))?></small>
              <a href="#" class="blog-read-more">Read More &rarr;</a>
            </div>
          </div>
        </article>
      </div>
      <?php endforeach;?>
    </div>

    <!-- Tips section — all eye care images -->
    <div class="blog-tips-strip mt-5">
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <span class="section-tag">Quick Tips</span>
          <h3 class="fw-bold mt-3">5 Daily Habits for Healthy Eyes</h3>
          <div class="mt-3">
            <?php foreach([
              ['https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=80&q=70', 'Get regular eye tests',     'Catch problems early with a free Lapaire eye test.'],
              ['https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=80&q=70','Choose the right frames',   'Good fitting frames protect your eyes and reduce strain.'],
              ['https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=80&q=70','Wear UV protection',        'Sunglasses shield your eyes from harmful UV rays.'],
              ['https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=80&q=70','Use blue light glasses',    'Reduce digital eye strain from screens.'],
              ['https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=80&q=70','Follow the 20-20-20 rule', 'Every 20 min, look 20 feet away for 20 seconds.'],
            ] as $t):?>
            <div class="d-flex gap-3 mb-3 align-items-center">
              <div class="tip-ico flex-shrink-0" style="background-image:url('<?=$t[0]?>');background-size:cover;background-position:center;border-radius:12px;border:none"></div>
              <div><b><?=$t[1]?></b><div class="text-muted small"><?=$t[2]?></div></div>
            </div>
            <?php endforeach;?>
          </div>
        </div>
        <div class="col-lg-6">
          <!-- optician performing eye exam -->
          <div class="blog-tips-img" style="background-image:url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=700&q=85')"></div>
        </div>
      </div>
    </div>

    <?php else:?>
    <div class="text-center py-5">
      <div class="mb-3">
        <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=200&q=80"
             style="width:120px;height:80px;object-fit:cover;border-radius:12px" alt="Glasses">
      </div>
      <h4>No articles yet</h4>
      <p class="text-muted">Check back soon for eye care tips and news from Lapaire.</p>
      <a href="appointment.php" class="sl-btn sl-btn--primary d-inline-block mt-3">Book a Free Eye Test</a>
    </div>
    <?php endif;?>

  </div>
</section>

<?php include 'includes/footer.php';?>
