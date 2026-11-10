<?php require 'includes/config.php';$pageTitle='Home';$active='Home';include 'includes/header.php';?>

<!-- Hero Slider -->
<section class="hero-slider">
  <div class="hero-slide active">
    <img src="assets/images/Designer (1).png" alt="Clearer Vision Brighter Tomorrow" class="sl-bg">
    <div class="sl-overlay"></div>
    <div class="sl-inner">
      <div class="sl-copy">
        <span class="sl-label">YOUR VISION OUR PRIORITY</span>
        <h1 class="sl-title">Clearer Vision<br><span class="sl-accent">Brighter</span> Tomorrow</h1>
        <p class="sl-sub">Stylish, affordable eyewear for a better you.<br>Free eye test · Quality frames · Trusted experts.</p>
        <div class="sl-btns">
          <a href="appointment.php" class="sl-btn sl-btn--primary">Book a Free Eye Test &nbsp;&#8594;</a>
          <a href="glasses.php" class="sl-btn sl-btn--outline">Browse Frames</a>
        </div>
        <div class="sl-badges">
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=80&q=60')"></div><span>100% Free<br>eye test</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=80&q=60')"></div><span>18<br>Branches</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1577401132921-cb39bb0adcff?w=80&q=60')"></div><span>6<br>Cities</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="hero-slide">
    <img src="assets/images/Designer (2).png" alt="Find Your Perfect Frame" class="sl-bg">
    <div class="sl-overlay"></div>
    <div class="sl-inner">
      <div class="sl-copy">
        <span class="sl-label">STYLISH FRAMES FOR EVERY YOU</span>
        <h1 class="sl-title">Find Your<br><span class="sl-accent">Perfect</span> Frame</h1>
        <p class="sl-sub">From classic to modern, discover styles<br>that match your personality.</p>
        <div class="sl-btns">
          <a href="glasses.php" class="sl-btn sl-btn--primary">Browse Frames &nbsp;&#8594;</a>
        </div>
        <div class="sl-badges">
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=80&q=60')"></div><span>Prescription<br>Glasses</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=80&q=60')"></div><span>Sunglasses</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=80&q=60')"></div><span>Blue Light<br>Glasses</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=80&q=60')"></div><span>Kids<br>Glasses</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="hero-slide">
    <img src="assets/images/Designer (3).png" alt="Your Eye Care Starts Here" class="sl-bg">
    <div class="sl-overlay"></div>
    <div class="sl-inner">
      <div class="sl-copy">
        <span class="sl-label">PROFESSIONAL EYE CARE</span>
        <h1 class="sl-title">Your Eye Care<br><span class="sl-accent">Starts Here</span></h1>
        <p class="sl-sub">Book a free eye test at your nearest branch<br>and take the first step to a clearer tomorrow.</p>
        <div class="sl-btns">
          <a href="appointment.php" class="sl-btn sl-btn--primary">Book an Appointment &nbsp;&#8594;</a>
        </div>
        <div class="sl-badges">
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=80&q=60')"></div><span>Trusted<br>Experts</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=80&q=60')"></div><span>Modern<br>Technology</span></div>
          <div class="sl-badge"><div class="sl-badge-img" style="background-image:url('https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=80&q=60')"></div><span>A Healthier<br>You</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="dots"></div>
</section>

<!-- Stats strip -->
<section class="stats-strip">
  <div class="container">
    <div class="row g-0 text-center">
      <?php foreach([
        ['https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=120&q=70','100%','Free Eye Test','No commitment, no cost'],
        ['https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=120&q=70','18','Branches','Across Uganda'],
        ['https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=120&q=70','6','Cities','And growing'],
        ['https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=120&q=70','100K+','Happy Customers','Trusted by families'],
      ] as $s):?>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-img" style="background-image:url('<?=$s[0]?>')"></div>
          <div class="stat-num"><?=$s[1]?></div>
          <div class="stat-label"><?=$s[2]?></div>
          <div class="stat-sub"><?=$s[3]?></div>
        </div>
      </div>
      <?php endforeach;?>
    </div>
  </div>
</section>

<!-- Shop by Category -->
<section class="section soft">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Collections</span>
      <h2 class="title mt-2">Shop by Category</h2>
      <p class="text-muted">Find the perfect look for every moment.</p>
    </div>
    <div class="row g-4">
      <div class="col-6 col-lg-3">
        <a href="glasses.php?category=Prescription" class="cat-card text-decoration-none">
          <div class="cat-img" style="background-image:url('https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=500&q=80')"></div>
          <div class="cat-body"><h5>Prescription</h5><p>Corrective lenses</p><span class="cat-link">Explore &rarr;</span></div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <a href="glasses.php?category=Sunglasses" class="cat-card text-decoration-none">
          <div class="cat-img" style="background-image:url('https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=500&q=80')"></div>
          <div class="cat-body"><h5>Sunglasses</h5><p>Style &amp; UV protection</p><span class="cat-link">Explore &rarr;</span></div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <a href="glasses.php?category=Blue+Light" class="cat-card text-decoration-none">
          <div class="cat-img" style="background-image:url('https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=500&q=80')"></div>
          <div class="cat-body"><h5>Blue Light</h5><p>For screens &amp; work</p><span class="cat-link">Explore &rarr;</span></div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <a href="glasses.php?category=Kids" class="cat-card text-decoration-none">
          <div class="cat-img" style="background-image:url('https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=500&q=80')"></div>
          <div class="cat-body"><h5>Kids Glasses</h5><p>Comfort for children</p><span class="cat-link">Explore &rarr;</span></div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Vision banner -->
<section class="section">
  <div class="container">
    <div class="photo-banner" style="background-image:url('assets/images/vision-banner.png')">
      <div class="shade">
        <div class="hero-copy">
          <small class="text-uppercase letter-spacing-1 opacity-75">Vision for a Brighter Future</small>
          <h2 class="display-5 fw-bold mt-2">Better Vision<br>Brighter Opportunities</h2>
          <p>We believe quality eye care deserves to be accessible to everyone across Africa.</p>
          <a class="btn btn-light text-primary rounded-pill px-4 mt-3" href="about.php">Learn More &rarr;</a>
        </div>
      </div>
    </div>

    <!-- Trust badges — real images -->
    <div class="row g-3 mt-4">
      <?php foreach([
        ['https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=400&q=80','Trusted Across Africa','Serving thousands of families'],
        ['https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400&q=80','Quality Frames','Premium eyewear, expert care'],
        ['https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400&q=80','Modern Styles','New collections every week'],
        ['https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=400&q=80','Free Eye Test','100% free, no commitment'],
      ] as $v):?>
      <div class="col-6 col-lg-3">
        <div class="trust-card text-center">
          <div class="trust-img" style="background-image:url('<?=$v[0]?>')"></div>
          <b class="d-block mt-3"><?=$v[1]?></b>
          <small class="text-muted"><?=$v[2]?></small>
        </div>
      </div>
      <?php endforeach;?>
    </div>
  </div>
</section>

<!-- Inquiry CTA strip -->
<section class="section" style="background:linear-gradient(135deg,#072d67,#0b6ed4);color:#fff">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <span class="section-tag" style="background:rgba(255,255,255,.15);color:#fff">Got a Question?</span>
        <h2 class="fw-bold mt-2 text-white">Not sure which frames suit you?<br>We're here to help.</h2>
        <p class="mt-2" style="color:#cde8ff">Ask us anything — frame styles, pricing, branch locations, eye test bookings.</p>
      </div>
      <div class="col-lg-5 text-lg-end">
        <a href="contact.php" class="sl-btn sl-btn--primary me-2">Send an Inquiry &rarr;</a>
        <a href="appointment.php" class="sl-btn sl-btn--outline">Book Eye Test</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php';?>
