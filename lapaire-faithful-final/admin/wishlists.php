<?php
require '../includes/config.php';
admin_only();
$adminTitle = 'Users & Wishlist';

// ── Handle actions ─────────────────────────────────────────────────────────
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $act = $_POST['action']??'';
  $uid = (int)($_POST['user_id']??0);

  if($act==='approve'){
    $pdo->prepare('UPDATE users SET status="Active" WHERE id=?')->execute([$uid]);
  } elseif($act==='suspend'){
    $pdo->prepare('UPDATE users SET status="Suspended" WHERE id=?')->execute([$uid]);
  } elseif($act==='delete'){
    $pdo->prepare('DELETE FROM wishlist WHERE user_id=?')->execute([$uid]);
    $pdo->prepare('UPDATE enquiries SET user_id=NULL WHERE user_id=?')->execute([$uid]);
    $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$uid]);
  }
  header('Location:wishlists.php');exit;
}

// ── Filter ─────────────────────────────────────────────────────────────────
$filter = $_GET['status'] ?? 'all';
$sql  = 'SELECT u.*, COUNT(w.id) wish_count FROM users u LEFT JOIN wishlist w ON w.user_id=u.id';
$args = [];
if($filter !== 'all'){ $sql .= ' WHERE u.status=?'; $args[] = $filter; }
$sql .= ' GROUP BY u.id ORDER BY u.created_at DESC';
$st=$pdo->prepare($sql);$st->execute($args);
$users=$st->fetchAll();

// Counts
$pending    = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='Pending'")->fetchColumn();
$active     = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='Active'")->fetchColumn();
$suspended  = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='Suspended'")->fetchColumn();
$total      = $pending+$active+$suspended;

// Most wishlisted
$topProducts = $pdo->query('SELECT p.name,p.category,COUNT(w.id) wish_count FROM wishlist w JOIN products p ON p.id=w.product_id GROUP BY p.id ORDER BY wish_count DESC LIMIT 8')->fetchAll();
$totalWishes = (int)$pdo->query('SELECT COUNT(*) FROM wishlist')->fetchColumn();

include '_top.php';
?>

<!-- KPI row -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-blue">
      <div class="kpi-icon">👥</div>
      <div class="kpi-num"><?=$total?></div>
      <div class="kpi-label">Total Users</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card" style="background:linear-gradient(135deg,#fff7e0,#ffe9a0)">
      <div class="kpi-icon">⏳</div>
      <div class="kpi-num"><?=$pending?></div>
      <div class="kpi-label">Pending Approval</div>
      <?php if($pending):?>
      <div class="kpi-badge" style="background:rgba(220,100,0,.15);color:#c05000">Needs action</div>
      <?php endif;?>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-green">
      <div class="kpi-icon">✅</div>
      <div class="kpi-num"><?=$active?></div>
      <div class="kpi-label">Active Users</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-purple">
      <div class="kpi-icon">♥</div>
      <div class="kpi-num"><?=$totalWishes?></div>
      <div class="kpi-label">Wishlist Saves</div>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- ── Users table ── -->
  <div class="col-xl-8">
    <div class="admin-card">

      <!-- Filter tabs -->
      <div class="d-flex flex-wrap gap-2 mb-4 align-items-center justify-content-between">
        <div class="d-flex gap-2 flex-wrap">
          <?php foreach(['all'=>"All ($total)",'Pending'=>"Pending ($pending)",'Active'=>"Active ($active)",'Suspended'=>"Suspended ($suspended)"] as $val=>$label):
            $cls = $filter===$val?'btn-primary':'btn-outline-secondary';
          ?>
          <a href="?status=<?=$val?>" class="btn btn-sm <?=$cls?> rounded-pill px-3"><?=$label?></a>
          <?php endforeach;?>
        </div>
      </div>

      <?php if($users):?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Name</th><th>Email / Phone</th><th>Saved</th><th>Joined</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php foreach($users as $u):
            $statusBadge = match($u['status']??'Pending'){
              'Active'    => ['success','✅ Active'],
              'Suspended' => ['secondary','🚫 Suspended'],
              default     => ['warning','⏳ Pending'],
            };
          ?>
          <tr>
            <td><b><?=e($u['full_name'])?></b></td>
            <td class="small">
              <?=e($u['email'])?>
              <?php if($u['phone']):?><div class="text-muted"><?=e($u['phone'])?></div><?php endif;?>
            </td>
            <td><span class="badge bg-primary"><?=$u['wish_count']?></span></td>
            <td class="small text-muted"><?=date('d M Y',strtotime($u['created_at']))?></td>
            <td><span class="badge bg-<?=$statusBadge[0]?>"><?=$statusBadge[1]?></span></td>
            <td>
              <div class="d-flex gap-1 flex-wrap">
                <?php if(($u['status']??'Pending')==='Pending'):?>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf"    value="<?=csrf()?>">
                  <input type="hidden" name="action"  value="approve">
                  <input type="hidden" name="user_id" value="<?=$u['id']?>">
                  <button class="btn btn-sm btn-success rounded-pill px-2">✅ Approve</button>
                </form>
                <?php endif;?>
                <?php if(($u['status']??'')==='Active'):?>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf"    value="<?=csrf()?>">
                  <input type="hidden" name="action"  value="suspend">
                  <input type="hidden" name="user_id" value="<?=$u['id']?>">
                  <button class="btn btn-sm btn-outline-warning rounded-pill px-2">🚫 Suspend</button>
                </form>
                <?php endif;?>
                <?php if(($u['status']??'')==='Suspended'):?>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf"    value="<?=csrf()?>">
                  <input type="hidden" name="action"  value="approve">
                  <input type="hidden" name="user_id" value="<?=$u['id']?>">
                  <button class="btn btn-sm btn-outline-success rounded-pill px-2">↩ Reactivate</button>
                </form>
                <?php endif;?>
                <form method="post" class="d-inline"
                      onsubmit="return confirm('Delete <?=e($u['full_name'])?>? This cannot be undone.')">
                  <input type="hidden" name="csrf"    value="<?=csrf()?>">
                  <input type="hidden" name="action"  value="delete">
                  <input type="hidden" name="user_id" value="<?=$u['id']?>">
                  <button class="btn btn-sm btn-outline-danger rounded-pill px-2">🗑 Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </div>
      <?php else:?>
      <p class="text-muted text-center py-4">No users found<?=$filter!=='all'?' with status "'.$filter.'"':''?>.</p>
      <?php endif;?>
    </div>
  </div>

  <!-- ── Most wishlisted ── -->
  <div class="col-xl-4">
    <div class="admin-card">
      <h6 class="admin-card-title mb-3">Most Wishlisted Frames</h6>
      <?php if($topProducts):?>
      <?php foreach($topProducts as $i=>$p):?>
      <div class="d-flex align-items-center gap-3 py-2 <?=$i<count($topProducts)-1?'border-bottom':''?>">
        <div class="fw-bold text-muted" style="width:22px;font-size:.85rem"><?=$i+1?></div>
        <div class="flex-grow-1">
          <div class="fw-semibold small"><?=e($p['name'])?></div>
          <div class="text-muted" style="font-size:.75rem"><?=e($p['category'])?></div>
        </div>
        <span class="badge bg-primary"><?=$p['wish_count']?></span>
      </div>
      <?php endforeach;?>
      <?php else:?>
      <p class="text-muted text-center py-4">No wishlist data yet.</p>
      <?php endif;?>
    </div>
  </div>

</div>

<?php include '_bottom.php';?>
