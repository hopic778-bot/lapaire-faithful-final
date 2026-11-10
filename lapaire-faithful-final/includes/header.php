<?php
$pageTitle=$pageTitle??'Lapaire';$active=$active??'';

// Check for unseen admin replies for logged-in users
$unseenReplies = [];
$unseenAppts   = [];
if(user_logged_in()){
  try{
    $rq = $pdo->prepare('SELECT id,subject,admin_reply,replied_at FROM enquiries WHERE user_id=? AND admin_reply IS NOT NULL AND reply_seen=0 ORDER BY replied_at DESC');
    $rq->execute([user_id()]);
    $unseenReplies = $rq->fetchAll();
  }catch(Exception $e){ $unseenReplies=[]; }

  try{
    $aq = $pdo->prepare('
      SELECT a.*, b.name branch_name
      FROM appointments a
      LEFT JOIN branches b ON b.id=a.branch_id
      WHERE a.user_id=? AND a.appt_notif_seen=0
      AND a.status IN ("Confirmed","Cancelled","Completed")
      ORDER BY a.created_at DESC');
    $aq->execute([user_id()]);
    $unseenAppts = $aq->fetchAll();
  }catch(Exception $e){ $unseenAppts=[]; }
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=e($pageTitle)?> | Lapaire</title>
  <meta name="description" content="Eyewear, eye care, branches and free eye test booking in Uganda.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg lapaire-nav sticky-top">
  <div class="container">

    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="assets/images/Designer (4).png" alt="Lapaire" class="nav-logo">
    </a>

    <!-- Mobile toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-1">
        <?php foreach(['index.php'=>'Home','about.php'=>'About','glasses.php'=>'Glasses','services.php'=>'Services','branches.php'=>'Branches','blog.php'=>'Blog','contact.php'=>'Contact'] as $u=>$n):?>
        <li class="nav-item">
          <a class="nav-link <?=$active===$n?'nav-link--active':''?>" href="<?=$u?>"><?=$n?></a>
        </li>
        <?php endforeach;?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if(user_logged_in()):?>
          <a href="wishlist.php" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="My Wishlist">♡ Wishlist</a>
          <div class="dropdown">
            <button class="btn btn-sm btn-light rounded-pill px-3 dropdown-toggle position-relative" data-bs-toggle="dropdown">
              👤 <?=e($_SESSION['user_name'])?>
              <?php $totalNotifs=count($unseenReplies)+count($unseenAppts); if($totalNotifs):?>
              <span class="notif-dot"><?=$totalNotifs?></span>
              <?php endif;?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="wishlist.php">♡ My Wishlist</a></li>
              <li><a class="dropdown-item" href="my_inquiries.php">✉️ My Inquiries
                <?php if($unseenReplies):?><span class="badge bg-danger ms-1"><?=count($unseenReplies)?></span><?php endif;?>
              </a></li>
              <li><a class="dropdown-item" href="my_appointments.php">📅 My Appointments
                <?php if($unseenAppts):?><span class="badge bg-danger ms-1"><?=count($unseenAppts)?></span><?php endif;?>
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="user_logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else:?>
          <a href="user_auth.php?action=login" class="btn btn-sm btn-outline-primary rounded-pill px-3">Sign In</a>
        <?php endif;?>
        <a class="btn btn-nav-cta rounded-pill px-4" href="appointment.php">Book Eye Test</a>
      </div>
    </div>

  </div>
</nav>

<main>

<?php if($unseenReplies): ?>
<!-- Admin reply notification popup -->
<div id="replyNotifOverlay" class="reply-notif-overlay">
  <div class="reply-notif-modal">
    <div class="reply-notif-header">
      <div class="reply-notif-icon">💬</div>
      <div>
        <h5 class="fw-bold mb-0">You have <?=count($unseenReplies)?> new reply<?=count($unseenReplies)>1?'s':''?></h5>
        <small class="text-muted">The Lapaire team replied to your inquiry</small>
      </div>
      <button class="reply-notif-close" id="closeReplyNotif" aria-label="Close">&times;</button>
    </div>
    <div class="reply-notif-body">
      <?php foreach($unseenReplies as $i=>$nr): ?>
      <div class="reply-notif-item <?=$i>0?'mt-3 pt-3 border-top':''?>">
        <div class="reply-notif-subject">Re: <?=e($nr['subject'])?></div>
        <div class="reply-notif-date"><?=date('d M Y, g:i A',strtotime($nr['replied_at']))?></div>
        <div class="reply-notif-message"><?=nl2br(e($nr['admin_reply']))?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="reply-notif-footer">
      <a href="my_inquiries.php" class="sl-btn sl-btn--primary">View All My Inquiries</a>
      <button class="btn btn-outline-secondary rounded-pill px-4" id="dismissReplyNotif">Dismiss</button>
    </div>
  </div>
</div>
<script>
(function(){
  function closeNotif(){
    document.getElementById('replyNotifOverlay').style.display='none';
    fetch('mark_reply_seen.php');
  }
  document.getElementById('closeReplyNotif').onclick   = closeNotif;
  document.getElementById('dismissReplyNotif').onclick = closeNotif;
  document.getElementById('replyNotifOverlay').addEventListener('click',function(e){
    if(e.target===this) closeNotif();
  });
})();
</script>
<?php endif; ?>

<?php if($unseenAppts): ?>
<!-- Appointment status notification popup -->
<div id="apptNotifOverlay" class="reply-notif-overlay">
  <div class="reply-notif-modal">
    <div class="reply-notif-header">
      <div class="reply-notif-icon">📅</div>
      <div>
        <h5 class="fw-bold mb-0">Appointment Update<?=count($unseenAppts)>1?'s':''?></h5>
        <small class="text-muted">Your eye test booking status has changed</small>
      </div>
      <button class="reply-notif-close" id="closeApptNotif" aria-label="Close">&times;</button>
    </div>
    <div class="reply-notif-body">
      <?php foreach($unseenAppts as $i=>$ap):
        $icon = match($ap['status']){
          'Confirmed' => '✅',
          'Cancelled' => '❌',
          'Completed' => '🎉',
          default     => '📅'
        };
        $color = match($ap['status']){
          'Confirmed' => '#dcfce7',
          'Cancelled' => '#fee2e2',
          'Completed' => '#eff6ff',
          default     => '#f8faff'
        };
      ?>
      <div class="reply-notif-item <?=$i>0?'mt-3 pt-3 border-top':''?>">
        <div class="reply-notif-subject">
          <?=$icon?> Appointment <?=e($ap['status'])?>
        </div>
        <div class="reply-notif-date">
          📍 <?=e($ap['branch_name']??'Lapaire Branch')?> &nbsp;·&nbsp;
          <?=date('d M Y',strtotime($ap['appointment_date']))?> at
          <?=date('g:i A',strtotime($ap['appointment_time']))?>
        </div>
        <div class="reply-notif-message" style="background:<?=$color?>">
          <?php if($ap['status']==='Confirmed'):?>
          Your eye test appointment has been <b>confirmed</b>! Please arrive 5 minutes early.
          <?php elseif($ap['status']==='Cancelled'):?>
          Your appointment has been <b>cancelled</b>. Please contact us or book a new slot.
          <?php elseif($ap['status']==='Completed'):?>
          Your eye test is marked as <b>completed</b>. Thank you for visiting Lapaire!
          <?php endif;?>
          <?php if($ap['admin_note']):?>
          <div class="mt-2 pt-2 border-top"><b>Note from branch:</b> <?=e($ap['admin_note'])?></div>
          <?php endif;?>
        </div>
      </div>
      <?php endforeach;?>
    </div>
    <div class="reply-notif-footer">
      <a href="my_appointments.php" class="sl-btn sl-btn--primary">View My Appointments</a>
      <button class="btn btn-outline-secondary rounded-pill px-4" id="dismissApptNotif">Dismiss</button>
    </div>
  </div>
</div>
<script>
(function(){
  function closeAppt(){
    document.getElementById('apptNotifOverlay').style.display='none';
    fetch('mark_appt_seen.php');
  }
  document.getElementById('closeApptNotif').onclick   = closeAppt;
  document.getElementById('dismissApptNotif').onclick = closeAppt;
  document.getElementById('apptNotifOverlay').addEventListener('click',function(e){
    if(e.target===this) closeAppt();
  });
})();
</script>
<?php endif; ?>
