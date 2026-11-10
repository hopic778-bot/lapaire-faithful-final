<?php
require '../includes/config.php';
admin_only();
$adminTitle = 'Inquiries';

if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $action = $_POST['action']??'';

  if($action==='reply'){
    $id    = (int)$_POST['id'];
    $reply = trim($_POST['admin_reply']);
    if($reply){
      $pdo->prepare('UPDATE enquiries SET admin_reply=?,replied_at=NOW(),status="Read",reply_seen=0 WHERE id=?')
          ->execute([$reply,$id]);
    }
  }
  elseif($action==='status'){
    $pdo->prepare('UPDATE enquiries SET status=? WHERE id=?')
        ->execute([$_POST['status'],(int)$_POST['id']]);
  }
  elseif($action==='delete'){
    $pdo->prepare('DELETE FROM enquiries WHERE id=?')->execute([(int)$_POST['id']]);
  }
  header('Location:enquiries.php');exit;
}

$filter = $_GET['status']??'all';
$sql    = 'SELECT * FROM enquiries';
$args   = [];
if($filter !== 'all'){ $sql .= ' WHERE status=?'; $args[] = $filter; }
$sql .= ' ORDER BY created_at DESC';
$st = $pdo->prepare($sql); $st->execute($args);
$rows = $st->fetchAll();

$counts = ['all'=>0];
foreach(['New','Read','Closed'] as $s){
  $counts[$s] = (int)$pdo->query("SELECT COUNT(*) FROM enquiries WHERE status='$s'")->fetchColumn();
  $counts['all'] += $counts[$s];
}

include '_top.php';
?>

<!-- Filter tabs -->
<div class="d-flex flex-wrap gap-2 mb-4 align-items-center justify-content-between">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach(['all'=>'All','New'=>'New','Read'=>'Read','Closed'=>'Closed'] as $val=>$label):
      $cls = $filter===$val ? 'btn-primary' : 'btn-outline-secondary';
    ?>
    <a href="?status=<?=$val?>" class="btn btn-sm <?=$cls?> rounded-pill px-3">
      <?=$label?> <span class="badge bg-white text-dark ms-1"><?=$counts[$val]?></span>
    </a>
    <?php endforeach;?>
  </div>
  <span class="text-muted small"><?=count($rows)?> inquiry/inquiries</span>
</div>

<?php if($rows):?>
<div class="row g-3">
<?php foreach($rows as $r):
  $badgeClass = match($r['status']){
    'New'    => 'danger',
    'Read'   => 'warning',
    'Closed' => 'secondary',
    default  => 'secondary'
  };
  $hasReply = !empty($r['admin_reply']);
?>
<div class="col-12">
  <div class="admin-card inq-card">
    <div class="row g-0">
      <div class="col-auto">
        <div class="inq-stripe bg-<?=$badgeClass?>"></div>
      </div>
      <div class="col p-4">

        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
          <div>
            <h5 class="fw-bold mb-1"><?=e($r['subject'])?></h5>
            <div class="d-flex flex-wrap gap-3 text-muted small">
              <span>👤 <b><?=e($r['full_name'])?></b></span>
              <span>✉️ <?=e($r['email'])?></span>
              <?php if($r['phone']):?><span>📞 <?=e($r['phone'])?></span><?php endif;?>
              <span>🕐 <?=date('d M Y, g:i A',strtotime($r['created_at']))?></span>
              <?php if($r['user_id']):?><span class="text-primary">🔗 Registered user</span><?php endif;?>
            </div>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <?php if($hasReply):?><span class="badge bg-success">Replied ✓</span><?php endif;?>
            <span class="badge bg-<?=$badgeClass?> px-3 py-2"><?=e($r['status'])?></span>
          </div>
        </div>

        <!-- Original message -->
        <div class="inq-message mb-3">
          <div class="small text-muted fw-semibold mb-1">📨 Customer message:</div>
          <?=nl2br(e($r['message']))?>
        </div>

        <!-- Existing reply -->
        <?php if($hasReply):?>
        <div class="inq-reply-box mb-3">
          <div class="small text-muted fw-semibold mb-1">✅ Your reply
            <span class="text-muted fw-normal">(sent <?=date('d M Y, g:i A',strtotime($r['replied_at']))?>)</span>:
          </div>
          <div><?=nl2br(e($r['admin_reply']))?></div>
        </div>
        <?php endif;?>

        <!-- Reply form -->
        <details <?=$hasReply?'':'open'?> class="mb-3">
          <summary class="btn btn-sm btn-outline-primary rounded-pill px-3 mb-2" style="list-style:none;cursor:pointer">
            <?=$hasReply?'✏️ Edit Reply':'💬 Write Reply'?>
          </summary>
          <form method="post" class="mt-2">
            <input type="hidden" name="csrf"   value="<?=csrf()?>">
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="id"     value="<?=$r['id']?>">
            <textarea name="admin_reply" class="form-control mb-2" rows="4"
              placeholder="Type your reply to <?=e($r['full_name'])?>..."><?=e($r['admin_reply']??'')?></textarea>
            <button class="btn btn-primary rounded-pill px-4">
              <?=$hasReply?'Update Reply':'Send Reply'?> &rarr;
            </button>
            <?php if(!$r['user_id']):?>
            <small class="text-muted d-block mt-2">
              ℹ️ This user was not logged in — they'll see the reply next time they sign in
              with the same email, or you can also reply directly at
              <a href="mailto:<?=e($r['email'])?>"><?=e($r['email'])?></a>
            </small>
            <?php endif;?>
          </form>
        </details>

        <!-- Status + delete actions -->
        <div class="d-flex flex-wrap gap-2">
          <form method="post" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="csrf"   value="<?=csrf()?>">
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id"     value="<?=$r['id']?>">
            <select name="status" class="form-select form-select-sm rounded-pill" style="max-width:150px">
              <?php foreach(['New','Read','Closed'] as $s):?>
              <option <?=$r['status']===$s?'selected':''?>><?=$s?></option>
              <?php endforeach;?>
            </select>
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">Update</button>
          </form>
          <form method="post" onsubmit="return confirm('Delete this inquiry?')">
            <input type="hidden" name="csrf"   value="<?=csrf()?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id"     value="<?=$r['id']?>">
            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
          </form>
          <a href="mailto:<?=e($r['email'])?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Email Directly
          </a>
        </div>

      </div>
    </div>
  </div>
</div>
<?php endforeach;?>
</div>
<?php else:?>
<div class="admin-card text-center py-5">
  <div class="fs-1 mb-3">✉️</div>
  <h5 class="fw-bold">No inquiries<?=$filter!=='all'?' with status "'.$filter.'"':''?></h5>
  <p class="text-muted">When customers send a message from the contact page, it will appear here.</p>
  <?php if($filter!=='all'):?>
  <a href="enquiries.php" class="btn btn-outline-primary rounded-pill mt-2">View All</a>
  <?php endif;?>
</div>
<?php endif;?>

<?php include '_bottom.php';?>
