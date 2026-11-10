<?php
require '../includes/config.php';
admin_only();
$adminTitle = 'Appointments';

if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $act = $_POST['action']??'';
  $id  = (int)$_POST['id'];

  if($act==='status'){
    $newStatus = $_POST['status'];
    $note      = trim($_POST['admin_note']??'');
    // When confirming — reset seen flag so user gets notified
    $resetSeen = in_array($newStatus,['Confirmed','Cancelled','Completed']) ? 0 : null;
    if($resetSeen !== null){
      $pdo->prepare('UPDATE appointments SET status=?,admin_note=?,appt_notif_seen=0 WHERE id=?')
          ->execute([$newStatus,$note,$id]);
    } else {
      $pdo->prepare('UPDATE appointments SET status=?,admin_note=? WHERE id=?')
          ->execute([$newStatus,$note,$id]);
    }
  }
  if($act==='delete'){
    $pdo->prepare('DELETE FROM appointments WHERE id=?')->execute([$id]);
  }
  header('Location:appointments.php');exit;
}

$filter = $_GET['status']??'all';
$sql    = 'SELECT a.*,b.name branch_name FROM appointments a LEFT JOIN branches b ON b.id=a.branch_id';
$args   = [];
if($filter!=='all'){ $sql.=' WHERE a.status=?'; $args[]=$filter; }
$sql   .= ' ORDER BY a.appointment_date ASC, a.appointment_time ASC';
$st=$pdo->prepare($sql);$st->execute($args);
$rows=$st->fetchAll();

// counts
$counts=[];
foreach(['Pending','Confirmed','Completed','Cancelled'] as $s)
  $counts[$s]=(int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status='$s'")->fetchColumn();
$counts['all']=array_sum($counts);

include '_top.php';
?>

<!-- Filter tabs -->
<div class="d-flex flex-wrap gap-2 mb-4 align-items-center justify-content-between">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach(['all'=>'All','Pending'=>'Pending','Confirmed'=>'Confirmed','Completed'=>'Completed','Cancelled'=>'Cancelled'] as $val=>$label):
      $cls=$filter===$val?'btn-primary':'btn-outline-secondary';
    ?>
    <a href="?status=<?=$val?>" class="btn btn-sm <?=$cls?> rounded-pill px-3">
      <?=$label?> <span class="badge bg-white text-dark ms-1"><?=$counts[$val]??0?></span>
    </a>
    <?php endforeach;?>
  </div>
  <span class="text-muted small"><?=count($rows)?> appointment(s)</span>
</div>

<?php if($rows):?>
<div class="row g-3">
<?php foreach($rows as $r):
  $badgeClass=match($r['status']){
    'Confirmed' =>'primary',
    'Completed' =>'success',
    'Cancelled' =>'danger',
    default     =>'warning'
  };
?>
<div class="col-12">
  <div class="admin-card">
    <div class="row g-0 align-items-center">

      <!-- Status stripe -->
      <div class="col-auto me-3">
        <div style="width:6px;height:100%;min-height:80px;border-radius:4px;background:var(--bs-<?=$badgeClass?>)"></div>
      </div>

      <!-- Details -->
      <div class="col">
        <div class="row g-2 align-items-center">

          <div class="col-lg-3">
            <div class="fw-bold"><?=e($r['full_name'])?></div>
            <div class="small text-muted"><?=e($r['email'])?></div>
            <div class="small text-muted"><?=e($r['phone'])?></div>
            <?php if($r['user_id']):?>
            <span class="badge bg-info text-dark mt-1" style="font-size:.7rem">🔗 Registered</span>
            <?php endif;?>
          </div>

          <div class="col-lg-2">
            <div class="small text-muted">Branch</div>
            <b><?=e($r['branch_name']??'—')?></b>
          </div>

          <div class="col-lg-2">
            <div class="small text-muted">Date &amp; Time</div>
            <b><?=date('d M Y',strtotime($r['appointment_date']))?></b>
            <div class="small"><?=date('g:i A',strtotime($r['appointment_time']))?></div>
          </div>

          <div class="col-lg-2">
            <span class="badge bg-<?=$badgeClass?> px-3 py-2"><?=e($r['status'])?></span>
            <?php if($r['appt_notif_seen']==0 && $r['status']!=='Pending'):?>
            <div class="small text-warning mt-1">⏳ Notif pending</div>
            <?php endif;?>
          </div>

          <!-- Update form -->
          <div class="col-lg-3">
            <form method="post" class="d-flex flex-column gap-2">
              <input type="hidden" name="csrf"   value="<?=csrf()?>">
              <input type="hidden" name="action" value="status">
              <input type="hidden" name="id"     value="<?=$r['id']?>">
              <div class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm rounded-pill">
                  <?php foreach(['Pending','Confirmed','Completed','Cancelled'] as $v):?>
                  <option <?=$r['status']===$v?'selected':''?>><?=$v?></option>
                  <?php endforeach;?>
                </select>
                <button class="btn btn-sm btn-primary rounded-pill px-3">Save</button>
              </div>
              <input name="admin_note" value="<?=e($r['admin_note']??'')?>"
                     class="form-control form-control-sm rounded-pill"
                     placeholder="Optional note to user...">
            </form>
          </div>

        </div>

        <!-- Admin note display -->
        <?php if($r['admin_note']):?>
        <div class="mt-2 p-2 rounded-3 small" style="background:#f0f8ff;border:1px solid #cce3f7;color:#0d3b6e">
          📝 Note to user: <?=e($r['admin_note'])?>
        </div>
        <?php endif;?>

      </div>

      <!-- Delete -->
      <div class="col-auto ms-2">
        <form method="post" onsubmit="return confirm('Delete this appointment?')">
          <input type="hidden" name="csrf"   value="<?=csrf()?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id"     value="<?=$r['id']?>">
          <button class="btn btn-sm btn-outline-danger rounded-pill px-2">🗑</button>
        </form>
      </div>

    </div>
  </div>
</div>
<?php endforeach;?>
</div>
<?php else:?>
<div class="admin-card text-center py-5">
  <div class="fs-1 mb-3">📅</div>
  <h5 class="fw-bold">No appointments<?=$filter!=='all'?' with status "'.$filter.'"':''?></h5>
  <p class="text-muted">Appointments booked from the website will appear here.</p>
  <?php if($filter!=='all'):?><a href="appointments.php" class="btn btn-outline-primary rounded-pill mt-2">View All</a><?php endif;?>
</div>
<?php endif;?>

<?php include '_bottom.php';?>
