<?php
require 'includes/config.php';
$pageTitle='Contact';$active='Contact';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $uid = user_logged_in() ? user_id() : null;
  $st=$pdo->prepare('INSERT INTO enquiries(user_id,full_name,email,phone,subject,message,status) VALUES(?,?,?,?,?,?,?)');
  $st->execute([$uid,trim($_POST['full_name']),trim($_POST['email']),trim($_POST['phone']),trim($_POST['subject']),trim($_POST['message']),'New']);
  $msg='success';
}
include 'includes/header.php';
?>

<!-- Page Hero — image banner -->
<div class="services-cta-banner">
  <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1400&q=85" alt="Lapaire team ready to help" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">WE'RE HERE FOR YOU</span>
    <h1 class="fw-bold display-4 text-white mt-2">Get in Touch</h1>
    <p class="text-white opacity-75 fs-5 mt-3">Our team is ready to help you find the<br>right frames and eye care for your needs.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
      <a class="sl-btn sl-btn--primary" href="appointment.php">Book Eye Test &rarr;</a>
      <a class="sl-btn sl-btn--outline" href="branches.php">Find a Branch</a>
    </div>
  </div>
</div>

<section class="section soft">
  <div class="container">
    <div class="row g-4">

      <!-- Contact Info -->
      <div class="col-lg-4">
        <div class="cardx p-4 h-100">
          <div class="contact-top-img mb-3" style="background-image:url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&q=80')"></div>
          <h4 class="fw-bold mb-4">Contact Info</h4>
          <?php foreach([
            ['https://images.unsplash.com/photo-1534536281715-e28d76689b4d?w=80&q=70','Phone','+256 775 488 056'],
            ['https://images.unsplash.com/photo-1596526131083-e8c633c948d2?w=80&q=70','Email','hello@lapaire.ug'],
            ['https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=80&q=70','WhatsApp','+256 775 488 056'],
            ['https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=80&q=70','Working Hours','Mon–Fri: 8AM–6PM · Sat: 9AM–5PM'],
            ['https://images.unsplash.com/photo-1582560475093-ba66accbc424?w=80&q=70','Head Office','I&amp;M Bank Building, Kampala Road, Kampala'],
          ] as $ci):?>
          <div class="contact-info-item">
            <div class="contact-icon-img" style="background-image:url('<?=$ci[0]?>')"></div>
            <div>
              <b><?=$ci[1]?></b>
              <div class="text-muted small"><?=$ci[2]?></div>
            </div>
          </div>
          <?php endforeach;?>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-lg-8">
        <div class="cardx p-4 p-lg-5">

          <?php if($msg==='success'):?>
          <!-- SUCCESS STATE -->
          <div class="text-center py-4">
            <div class="inq-success-ico">✅</div>
            <h3 class="fw-bold mt-3">Message Sent!</h3>
            <p class="text-muted fs-5 mt-2">
              Thank you for reaching out.<br>
              Our team will get back to you within <b>24 hours</b>.
            </p>
            <div class="mt-4 p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
              <div class="text-muted small">In the meantime you can also reach us at:</div>
              <div class="fw-bold mt-1">📞 +256 775 488 056 &nbsp;|&nbsp; ✉️ hello@lapaire.ug</div>
            </div>
            <a href="contact.php" class="sl-btn sl-btn--primary d-inline-block mt-4">Send Another Message</a>
            <a href="index.php" class="btn btn-outline-primary rounded-pill px-4 ms-2">Go Home</a>
          </div>

          <?php else:?>
          <!-- FORM -->
          <h4 class="fw-bold mb-1">Send Us an Inquiry</h4>
          <p class="text-muted mb-4">Have a question about glasses, branches or eye care? Fill in the form — we'll reply within 24 hours.</p>

          <!-- Common inquiry topics -->
          <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="inq-topic-tag" data-subject="Frame inquiry">👓 Frame inquiry</span>
            <span class="inq-topic-tag" data-subject="Eye test question">👁 Eye test question</span>
            <span class="inq-topic-tag" data-subject="Branch location">📍 Branch location</span>
            <span class="inq-topic-tag" data-subject="Price inquiry">💰 Price inquiry</span>
            <span class="inq-topic-tag" data-subject="Other">💬 Other</span>
          </div>

          <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Your Name</label>
              <input required name="full_name" class="form-control" placeholder="John Doe">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Email Address</label>
              <input required type="email" name="email" class="form-control" placeholder="you@example.com">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Phone Number</label>
              <input name="phone" class="form-control" placeholder="+256 ...">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Subject</label>
              <input required name="subject" id="subjectField" class="form-control" placeholder="How can we help?">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Your Message</label>
              <textarea required name="message" class="form-control" rows="5"
                placeholder="Tell us more — e.g. which frame you liked, which branch you visited, or any question you have..."></textarea>
            </div>
            <div class="col-12">
              <button class="sl-btn sl-btn--primary">Send Inquiry &rarr;</button>
            </div>
          </form>
          <?php endif;?>

        </div>
      </div>

    </div>
  </div>
</section>


<script>
// Click a topic tag to pre-fill the subject field
document.querySelectorAll('.inq-topic-tag').forEach(tag=>{
  tag.addEventListener('click',function(){
    const field = document.getElementById('subjectField');
    if(field){ field.value = this.dataset.subject; field.focus(); }
    document.querySelectorAll('.inq-topic-tag').forEach(t=>t.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>

<?php include 'includes/footer.php';?>
