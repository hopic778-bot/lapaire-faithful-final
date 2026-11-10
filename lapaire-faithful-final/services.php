<?php require 'includes/config.php';$pageTitle='Services';$active='Services';include 'includes/header.php';?>

<!-- Page Hero — full image banner at top -->
<div class="services-cta-banner">
  <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400&q=85" alt="Eye care professional" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">PROFESSIONAL EYE CARE</span>
    <h1 class="fw-bold display-4 text-white mt-2">Our Services</h1>
    <p class="text-white opacity-75 fs-5 mt-3">Professional eye care, for a clearer and brighter you.<br>Free eye test · Quality frames · Trusted experts.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
      <a class="sl-btn sl-btn--primary" href="appointment.php">Book a Free Eye Test &rarr;</a>
      <a class="sl-btn sl-btn--outline" href="branches.php">Find a Branch</a>
    </div>
  </div>
</div>

<!-- Service cards with images -->
<section class="section">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">What We Offer</span>
      <h2 class="title mt-2">Everything Your Eyes Need</h2>
      <p class="text-muted">From free eye tests to stylish frames — all under one roof.</p>
    </div>

    <!-- Free Eye Test -->
    <div class="row g-4 align-items-center mb-5">
      <div class="col-lg-6">
        <div class="service-img-card" style="background-image:url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=85')"></div>
      </div>
      <div class="col-lg-6">
        <span class="section-tag">100% Free</span>
        <h2 class="fw-bold mt-3">Free Eye Test</h2>
        <p class="text-muted mt-3 fs-5">Our qualified opticians carry out a comprehensive eye examination at no cost. No hidden fees, no commitment — just a clear picture of your vision health.</p>
        <ul class="service-list mt-3">
          <li>Complete vision assessment in just 20 minutes</li>
          <li>Carried out by certified opticians</li>
          <li>Available at all 18 branches across Uganda</li>
          <li>Walk in or book in advance</li>
        </ul>
        <a href="appointment.php" class="sl-btn sl-btn--primary d-inline-block mt-4">Book Your Free Eye Test &rarr;</a>
      </div>
    </div>

    <!-- Prescription Glasses -->
    <div class="row g-4 align-items-center mb-5 flex-lg-row-reverse">
      <div class="col-lg-6">
        <div class="service-img-card" style="background-image:url('https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=800&q=85')"></div>
      </div>
      <div class="col-lg-6">
        <span class="section-tag">Corrective Lenses</span>
        <h2 class="fw-bold mt-3">Prescription Glasses</h2>
        <p class="text-muted mt-3 fs-5">Choose from a wide range of stylish frames and get corrective lenses fitted by our professionals. Modern frames starting from UGX 50,000.</p>
        <ul class="service-list mt-3">
          <li>Hundreds of frame styles and shapes</li>
          <li>Single vision, bifocal and progressive lenses</li>
          <li>Ready within 24–72 hours</li>
          <li>Affordable prices for every budget</li>
        </ul>
        <a href="glasses.php?category=Prescription" class="sl-btn sl-btn--primary d-inline-block mt-4">Browse Prescription Frames &rarr;</a>
      </div>
    </div>

    <!-- Sunglasses -->
    <div class="row g-4 align-items-center mb-5">
      <div class="col-lg-6">
        <div class="service-img-card" style="background-image:url('https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&q=85')"></div>
      </div>
      <div class="col-lg-6">
        <span class="section-tag">UV Protection</span>
        <h2 class="fw-bold mt-3">Sunglasses</h2>
        <p class="text-muted mt-3 fs-5">Protect your eyes from harmful UV rays while staying on trend. Our sunglasses collection covers every style from classic to modern.</p>
        <ul class="service-list mt-3">
          <li>100% UV400 protection options</li>
          <li>Polarised lenses available</li>
          <li>Styles for men, women and kids</li>
          <li>Can be fitted with prescription lenses</li>
        </ul>
        <a href="glasses.php?category=Sunglasses" class="sl-btn sl-btn--primary d-inline-block mt-4">Browse Sunglasses &rarr;</a>
      </div>
    </div>

    <!-- Blue Light Glasses -->
    <div class="row g-4 align-items-center flex-lg-row-reverse">
      <div class="col-lg-6">
        <div class="service-img-card" style="background-image:url('https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=800&q=85')"></div>
      </div>
      <div class="col-lg-6">
        <span class="section-tag">Screen Protection</span>
        <h2 class="fw-bold mt-3">Blue Light Glasses</h2>
        <p class="text-muted mt-3 fs-5">Reduce digital eye strain from screens and improve your sleep. Ideal for office workers, students and anyone spending long hours on devices.</p>
        <ul class="service-list mt-3">
          <li>Filters harmful blue light from screens</li>
          <li>Reduces headaches and eye fatigue</li>
          <li>Available with or without prescription</li>
          <li>Lightweight, comfortable frames</li>
        </ul>
        <a href="glasses.php?category=Blue+Light" class="sl-btn sl-btn--primary d-inline-block mt-4">Browse Blue Light Frames &rarr;</a>
      </div>
    </div>

  </div>
</section>

<!-- Bottom CTA strip -->
<section class="section soft">
  <div class="container text-center">
    <h2 class="title">Ready to see clearer?</h2>
    <p class="text-muted fs-5 mt-2">Book your free eye test at any Lapaire branch today.</p>
    <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
      <a href="appointment.php" class="sl-btn sl-btn--primary">Book a Free Eye Test &rarr;</a>
      <a href="branches.php" class="btn btn-outline-primary rounded-pill px-4">Find Your Nearest Branch</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php';?>
