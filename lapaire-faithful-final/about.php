<?php require 'includes/config.php';$pageTitle='About';$active='About';include 'includes/header.php';?>

<!-- Hero -->
<section class="ref-hero" style="background-image:url('assets/images/about-hero.png')">
  <div class="overlay">
    <div class="hero-copy">
      <small class="text-uppercase opacity-75 letter-spacing-1">About Lapaire</small>
      <h1 class="display-4 fw-bold mt-2">About Lapaire</h1>
      <p class="lead">More than eyewear — we are a vision partner<br>for a brighter Africa.</p>
      <a href="appointment.php" class="btn btn-primary rounded-pill px-4 mt-2">Book Eye Test</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">

    <!-- Story + Mission -->
    <div class="row g-4 align-items-stretch mb-5">
      <div class="col-lg-7">
        <h2 class="title">Our Story</h2>
        <p class="lead">Lapaire was founded with a simple mission: to make quality eye care accessible to everyone in Africa. Today, we are proud to serve thousands of customers across Uganda, with modern frames, free eye tests and a team of dedicated professionals.</p>
        <!-- optical store interior -->
        <div class="about-story-img mt-4" style="background-image:url('https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=800&q=85')"></div>
      </div>
      <div class="col-lg-5">
        <div class="cardx overflow-hidden h-100">
          <!-- optician examining patient -->
          <div class="about-mission-img" style="background-image:url('https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=600&q=85')"></div>
          <div class="p-4">
            <h3 class="fw-bold">Our Mission</h3>
            <p class="text-muted mb-0">To improve lives through accessible and affordable eye care for every African family.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Vision / Values / Impact -->
    <div class="row g-3 mt-2">
      <?php foreach([
        // eye exam chart — vision
        ['https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&q=80','Our Vision','A world where everyone sees a brighter future.'],
        // optician with patient — values
        ['https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&q=80','Our Values','People first, integrity, innovation and impact.'],
        // glasses on display — impact
        ['https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=500&q=80','Our Impact','Healthier communities and brighter opportunities.'],
      ] as $x):?>
      <div class="col-md-4">
        <div class="cardx overflow-hidden h-100">
          <div class="about-card-img" style="background-image:url('<?=$x[0]?>')"></div>
          <div class="p-4">
            <h4 class="fw-bold"><?=$x[1]?></h4>
            <p class="text-muted mb-0"><?=$x[2]?></p>
          </div>
        </div>
      </div>
      <?php endforeach;?>
    </div>

    <!-- Numbers -->
    <h2 class="title mt-5 mb-3">Lapaire in Numbers</h2>
    <div class="row g-3">
      <?php foreach([
        ['https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=400&q=80','18','Branches'],
        ['https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400&q=80','6','Cities'],
        ['https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=400&q=80','24.5K','Followers'],
        ['https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=400&q=80','100K+','Happy Customers'],
      ] as $s):?>
      <div class="col-6 col-lg-3">
        <div class="cardx overflow-hidden text-center">
          <div class="about-num-img" style="background-image:url('<?=$s[0]?>')"></div>
          <div class="p-3">
            <h2 class="fw-bold text-primary mb-0"><?=$s[1]?></h2>
            <div class="text-muted"><?=$s[2]?></div>
          </div>
        </div>
      </div>
      <?php endforeach;?>
    </div>

    <!-- Quote -->
    <div class="text-center mt-5 py-4 border-top border-bottom">
      <blockquote class="blockquote">
        <p class="fs-4 fw-semibold text-navy fst-italic">"Clear vision changes everything."</p>
        <footer class="blockquote-footer mt-2">Lapaire Team</footer>
      </blockquote>
    </div>

  </div>
</section>

<?php include 'includes/footer.php';?>
