<?php
require 'includes/config.php';
if(!user_logged_in()){header('Location:user_auth.php?action=login&redirect=my_inquiries.php');exit;}
$pageTitle='My Inquiries'; $active='';

// Mark all as seen
$pdo->prepare('UPDATE enquiries SET reply_seen=1 WHERE user_id=?')->execute([user_id()]);

$items = $pdo->prepare('SELECT * FROM enquiries WHERE user_id=? ORDER BY created_at DESC');
$items->execute([user_id()]);
$items = $items->fetchAll();

include 'includes/header.php';
?>

<div class="services-cta-banner" style="min-height:240px">
  <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1400&q=85" alt="My Inquiries" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">YOUR MESSAGES</span>
    <h1 class="fw-bold display-5 text-white mt-2">My Inquiries</h1>
    <p class="text-white opacity-75 mt-2">Track your messages and replies from the Lapaire team.</p>
  </div>
</div>

<section class="section soft">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="fw-bold mb-0">Your Inquiries (<?=count($items)?>)</h5>
      <a href="contact.php" class="sl-btn sl-btn--primary">+ New Inquiry</a>
    </div>

    <?php if($items):?>
    <div class="row g-3">
      <?php foreach($items as $r):
        $hasReply = !empty($r['admin_reply']);
        $statusColor = match($r['status']){
          'New'    => 'danger',
          'Read'   => 'warning',
          'Closed' => 'success',
          default  => 'secondary'
        };
      ?>
      <div class="col-12">
        <div class="cardx p-0 overflow-hidden">
          <div class="row g-0">
            <!-- Color stripe -->
            <div class="col-auto">
              <div style="width:6px;background:var(--bs-<?=$statusColor?>);min-height:100%"></div>
            </div>
            <div class="col p-4">

              <!-- Header -->
              <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                <div>
                  <h5 class="fw-bold mb-1"><?=e($r['subject'])?></h5>
                  <small class="text-muted">Sent <?=date('d M Y, g:i A',strtotime($r['created_at']))?></small>
                </div>
                <div class="d-flex gap-2">
                  <?php if($hasReply):?>
                  <span class="badge bg-success">Replied ✓</span>
                  <?php else:?>
                  <span class="badge bg-warning text-dark">Awaiting reply</span>
                  <?php endif;?>
                  <span class="badge bg-<?=$statusColor?>"><?=e($r['status'])?></span>
                </div>
              </div>

              <!-- Your message -->
              <div class="inq-message mb-3">
                <div class="small text-muted fw-semibold mb-1">📨 Your message:</div>
                <?=nl2br(e($r['message']))?>
              </div>

              <!-- Admin reply -->
              <?php if($hasReply):?>
              <div class="inq-reply-box">
                <div class="small fw-semibold mb-1" style="color:#0570df">
                  💬 Reply from Lapaire
                  <span class="text-muted fw-normal">(<?=date('d M Y, g:i A',strtotime($r['replied_at']))?>)</span>
                </div>
                <div><?=nl2br(e($r['admin_reply']))?></div>
              </div>
              <?php else:?>
              <div class="p-3 rounded-3 text-muted small" style="background:#f8faff;border:1px dashed #cce3f7">
                ⏳ No reply yet. The Lapaire team will respond within 24 hours.
                You'll see a notification here and when you log in.
              </div>
              <?php endif;?>

            </div>
          </div>
        </div>
      </div>
      <?php endforeach;?>
    </div>

    <?php else:?>
    <div class="cardx p-5 text-center">
      <div class="fs-1 mb-3">✉️</div>
      <h5 class="fw-bold">No inquiries yet</h5>
      <p class="text-muted">Have a question about glasses, prices or branches? Send us an inquiry.</p>
      <a href="contact.php" class="sl-btn sl-btn--primary d-inline-block mt-3">Send an Inquiry &rarr;</a>
    </div>
    <?php endif;?>

  </div>
</section>

<?php include 'includes/footer.php';?>
