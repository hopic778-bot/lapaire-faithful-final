<?php
require '../includes/config.php';
admin_only();
$adminTitle = 'Dashboard';

// KPI counts
$counts = [];
foreach(['appointments','products','branches','enquiries'] as $t)
  $counts[$t] = (int)$pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();

$pendingAppts = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();
$newEnqs      = (int)$pdo->query("SELECT COUNT(*) FROM enquiries WHERE status='New'")->fetchColumn();

// Recent appointments
$recent = $pdo->query('SELECT a.*,b.name branch_name FROM appointments a
  LEFT JOIN branches b ON b.id=a.branch_id
  ORDER BY a.created_at DESC LIMIT 8')->fetchAll();

// Recent enquiries
$enqs = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5')->fetchAll();

include '_top.php';
?>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-blue">
      <div class="kpi-icon">📅</div>
      <div class="kpi-num"><?=$counts['appointments']?></div>
      <div class="kpi-label">Appointments</div>
      <?php if($pendingAppts):?>
      <div class="kpi-badge"><?=$pendingAppts?> pending</div>
      <?php endif;?>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-green">
      <div class="kpi-icon">👓</div>
      <div class="kpi-num"><?=$counts['products']?></div>
      <div class="kpi-label">Frames & Products</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-purple">
      <div class="kpi-icon">📍</div>
      <div class="kpi-num"><?=$counts['branches']?></div>
      <div class="kpi-label">Branches</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-orange">
      <div class="kpi-icon">✉️</div>
      <div class="kpi-num"><?=$counts['enquiries']?></div>
      <div class="kpi-label">Enquiries</div>
      <?php if($newEnqs):?>
      <div class="kpi-badge"><?=$newEnqs?> new</div>
      <?php endif;?>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="admin-card">
      <h6 class="admin-card-title">Quick Actions</h6>
      <div class="d-flex flex-wrap gap-2">
        <a href="products.php" class="btn btn-primary rounded-pill px-4">+ Add Frame</a>
        <a href="branches.php" class="btn btn-outline-primary rounded-pill px-4">+ Add Branch</a>
        <a href="posts.php" class="btn btn-outline-primary rounded-pill px-4">+ New Post</a>
        <a href="appointments.php" class="btn btn-outline-secondary rounded-pill px-4">View Appointments</a>
        <a href="enquiries.php" class="btn btn-outline-secondary rounded-pill px-4">View Enquiries</a>
        <a href="../index.php" target="_blank" class="btn btn-outline-success rounded-pill px-4">🌐 View Site</a>
      </div>
    </div>
  </div>
</div>

<!-- Recent Appointments + Enquiries -->
<div class="row g-3">

  <div class="col-xl-8">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="admin-card-title mb-0">Recent Appointments</h6>
        <a href="appointments.php" class="text-primary small">View All &rarr;</a>
      </div>
      <?php if($recent):?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Name</th><th>Branch</th><th>Date</th><th>Time</th><th>Status</th></tr>
          </thead>
          <tbody>
          <?php foreach($recent as $r):?>
          <tr>
            <td><b><?=e($r['full_name'])?></b><div class="small text-muted"><?=e($r['phone'])?></div></td>
            <td><?=e($r['branch_name']??'—')?></td>
            <td><?=date('d M Y',strtotime($r['appointment_date']))?></td>
            <td><?=date('g:i A',strtotime($r['appointment_time']))?></td>
            <td>
              <?php
              $cls=['Pending'=>'warning','Confirmed'=>'primary','Completed'=>'success','Cancelled'=>'secondary'];
              $c=$cls[$r['status']]??'secondary';
              ?>
              <span class="badge bg-<?=$c?>"><?=e($r['status'])?></span>
            </td>
          </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </div>
      <?php else:?>
      <p class="text-muted text-center py-4">No appointments yet.</p>
      <?php endif;?>
    </div>
  </div>

  <div class="col-xl-4">
    <div class="admin-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="admin-card-title mb-0">Latest Enquiries</h6>
        <a href="enquiries.php" class="text-primary small">View All &rarr;</a>
      </div>
      <?php if($enqs):?>
      <?php foreach($enqs as $q):?>
      <div class="enq-item">
        <div class="d-flex justify-content-between">
          <b class="small"><?=e($q['full_name'])?></b>
          <span class="badge bg-<?=$q['status']==='New'?'danger':($q['status']==='Read'?'warning':'secondary')?> badge-sm"><?=e($q['status'])?></span>
        </div>
        <div class="small text-primary"><?=e($q['subject'])?></div>
        <div class="small text-muted"><?=e($q['email'])?></div>
      </div>
      <?php endforeach;?>
      <?php else:?>
      <p class="text-muted text-center py-4">No enquiries yet.</p>
      <?php endif;?>
    </div>
  </div>

</div>

<?php include '_bottom.php';?>
