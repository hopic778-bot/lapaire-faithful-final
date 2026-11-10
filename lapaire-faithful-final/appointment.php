<?php
require 'includes/config.php';
$pageTitle='Book Eye Test';$active='';
$branches=$pdo->query('SELECT id,name,city FROM branches WHERE active=1 ORDER BY city,name')->fetchAll();
$msg='';$pre=(int)($_GET['branch']??0);
$step=1; // default step

if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $uid = user_logged_in() ? user_id() : null;
  $st=$pdo->prepare('INSERT INTO appointments(user_id,full_name,email,phone,branch_id,appointment_date,appointment_time,status) VALUES(?,?,?,?,?,?,?,?)');
  $st->execute([
    $uid,
    trim($_POST['full_name']),trim($_POST['email']),trim($_POST['phone']),
    (int)$_POST['branch_id'],$_POST['appointment_date'],$_POST['appointment_time'],'Pending'
  ]);
  $step=4;
  $msg='Appointment request received. We will confirm your slot shortly.';
}
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero py-4">
  <div class="container">
    <h1 class="display-5 fw-bold">Book a Free Eye Test</h1>
    <p class="lead mb-0">It's quick, easy and 100% free. No commitment.</p>
  </div>
</section>

<section class="section soft">
  <div class="container">

    <!-- Steps indicator -->
    <div class="d-flex align-items-center gap-2 mb-4">
      <?php
      $steps=['Select Branch','Choose Date &amp; Time','Your Details','Confirmation'];
      foreach($steps as $i=>$label):
        $n=$i+1;
        $on=$step>=$n?'on':'';
      ?>
      <div class="step <?=$on?>"><?=$n?></div>
      <span class="small <?=$step>=$n?'text-primary fw-semibold':'text-muted'?> d-none d-sm-inline"><?=$label?></span>
      <?php if($i<count($steps)-1):?><div class="step-line flex-grow-1"></div><?php endif;?>
      <?php endforeach;?>
    </div>

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="cardx p-4">
          <?php if($msg):?>
          <div class="alert alert-success">
            <h5 class="fw-bold">✔ Booking Confirmed!</h5>
            <?=e($msg)?>
            <div class="mt-3"><a href="index.php" class="btn btn-primary rounded-pill">Back to Home</a></div>
          </div>
          <?php else:?>
          <form method="post" class="row g-3" id="bookingForm">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <!-- Step 1: Branch -->
            <div class="col-12">
              <label class="form-label fw-semibold">Select Branch</label>
              <select required name="branch_id" class="form-select">
                <option value="">Choose a branch</option>
                <?php foreach($branches as $b):?>
                <option value="<?=$b['id']?>" <?=$pre===$b['id']?'selected':''?>><?=e($b['name'].' · '.$b['city'])?></option>
                <?php endforeach;?>
              </select>
            </div>

            <!-- Step 2: Date & Time -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Select Date</label>
              <input required type="date" name="appointment_date" min="<?=date('Y-m-d')?>" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Select Time</label>
              <select required name="appointment_time" class="form-select">
                <option value="">Choose a time</option>
                <?php
                $start=strtotime('09:00');$end=strtotime('17:00');
                for($t=$start;$t<=$end;$t+=1800):
                  echo '<option value="'.date('H:i',$t).'">'.date('g:i A',$t).'</option>';
                endfor;
                ?>
              </select>
            </div>

            <!-- Step 3: Details -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name</label>
              <input required name="full_name" class="form-control" placeholder="Your full name">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone</label>
              <input required name="phone" class="form-control" placeholder="+256 ...">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Email</label>
              <input required type="email" name="email" class="form-control" placeholder="you@example.com">
            </div>

            <div class="col-12 mt-2">
              <button class="btn btn-primary rounded-pill px-5">Confirm Request</button>
            </div>
          </form>
          <?php endif;?>
        </div>
      </div>

      <!-- Side photo -->
      <div class="col-lg-5">
        <div class="appointment-photo" style="background-image:url('assets/images/eye-exam.png')">
          <div class="caption">
            <div class="fs-5 fw-bold">A clearer you</div>
            <div>in just 20 minutes</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include 'includes/footer.php';?>
