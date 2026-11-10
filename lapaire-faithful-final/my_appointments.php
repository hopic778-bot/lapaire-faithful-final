<?php
require 'includes/config.php';
if(!user_logged_in()){header('Location:user_auth.php?action=login&redirect=my_appointments.php');exit;}
$pageTitle='My Appointments';$active='';

// Mark all as seen
try{
  $pdo->prepare('UPDATE appointments SET appt_notif_seen=1 WHERE user_id=?')->execute([user_id()]);
}catch(Exception $e){}

$items=$pdo->prepare('
  SELECT a.*,b.name branch_name,b.address branch_address,b.phone branch_phone
  FROM appointments a
  LEFT JOIN branches b ON b.id=a.branch_id
  WHERE a.user_id=?
  ORDER BY a.appointment_date DESC,a.appointment_time DESC');
$items->execute([user_id()]);
$items=$items->fetchAll();

include 'includes/header.php';
?>

<div class="services-cta-banner" style="min-height:240px">
  <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400&q=85" alt="My Appointments" class="services-cta-bg">
  <div class="services-cta-overlay"></div>
  <div class="services-cta-content">
    <span class="sl-label">YOUR BOOKINGS</span>
    <h1 class="fw-bold display-5 text-white mt-2">My Appointments</h1>
    <p class="text-white opacity-75 mt-2">Track your eye test bookings and their status.</p>
  </div>
</div>

<section class="section soft">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="fw-bold mb-0">Your Bookings (<?=count($items)?>)</h5>
      <a href="appointment.php" class="sl-btn sl-btn--primary">+ Book New Eye Test</a>
    </div>

    <?php if($items):?>
    <div class="row g-3">
      <?php foreach($items as $a):
        $icon  = match($a['status']){ 'Confirmed'=>'✅','Cancelled'=>'❌','Completed'=>'🎉',default=>'⏳' };
        $badge = match($a['status']){ 'Confirmed'=>'primary','Cancelled'=>'danger','Completed'=>'success',default=>'warning' };
        $isPast= strtotime($a['appointment_date']) < strtotime('today');
      ?>
      <div class="col-12">
        <div class="cardx p-0 overflow-hidden">
          <div class="row g-0">
            <div class="col-auto">
              <div style="width:6px;min-height:100%;border-radius:0;background:var(--bs-<?=$badge?>)"></div>
            </div>
            <div class="col p-4">
              <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                  <h5 class="fw-bold mb-1"><?=$icon?> Eye Test — <?=e($a['branch_name']??'Lapaire')?></h5>
                  <div class="d-flex flex-wrap gap-3 text-muted small">
                    <span>📅 <?=date('d M Y',strtotime($a['appointment_date']))?></span>
                    <span>🕐 <?=date('g:i A',strtotime($a['appointment_time']))?></span>
                    <?php if($a['branch_address']):?>
                    <span>📍 <?=e($a['branch_address'])?></span>
                    <?php endif;?>
                    <?php if($a['branch_phone']):?>
                    <span>📞 <?=e($a['branch_phone'])?></span>
                    <?php endif;?>
                  </div>
                </div>
                <span class="badge bg-<?=$badge?> px-3 py-2 fs-6"><?=$icon?> <?=e($a['status'])?></span>
              </div>

              <?php if($a['status']==='Confirmed' && !$isPast):?>
              <div class="mt-3 p-3 rounded-3" style="background:#dcfce7;border:1px solid #86efac">
                <b class="text-success">✅ Your appointment is confirmed!</b>
                <div class="text-muted small mt-1">Please arrive 5 minutes early. Bring this confirmation if possible.</div>
              </div>
              <?php elseif($a['status']==='Pending'):?>
              <div class="mt-3 p-3 rounded-3" style="background:#fff8e1;border:1px solid #ffe082">
                <b>⏳ Awaiting confirmation</b>
                <div class="text-muted small mt-1">The branch will confirm your slot shortly. You'll get a notification here.</div>
              </div>
              <?php elseif($a['status']==='Cancelled'):?>
              <div class="mt-3 p-3 rounded-3" style="background:#fee2e2;border:1px solid #fca5a5">
                <b class="text-danger">❌ This appointment was cancelled.</b>
                <div class="text-muted small mt-1"><a href="appointment.php" class="text-primary">Book a new appointment &rarr;</a></div>
              </div>
              <?php endif;?>

              <?php if($a['admin_note']):?>
              <div class="mt-2 p-2 rounded-3 small" style="background:#f0f8ff;border:1px solid #cce3f7;color:#0d3b6e">
                📝 <b>Note from branch:</b> <?=e($a['admin_note'])?>
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
      <div class="fs-1 mb-3">📅</div>
      <h5 class="fw-bold">No appointments yet</h5>
      <p class="text-muted">Book a free eye test at any Lapaire branch near you.</p>
      <a href="appointment.php" class="sl-btn sl-btn--primary d-inline-block mt-3">Book a Free Eye Test &rarr;</a>
    </div>
    <?php endif;?>

  </div>
</section>

<?php include 'includes/footer.php';?>
