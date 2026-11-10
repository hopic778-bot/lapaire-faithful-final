<?php
require dirname(__DIR__).'/includes/config.php';
$steps = [];
try{
  $pdo->exec("ALTER TABLE enquiries ADD COLUMN user_id INT NULL AFTER id");
  $steps[] = '✔ Added user_id column';
}catch(PDOException $e){ $steps[] = 'ℹ user_id: '.$e->getMessage(); }
try{
  $pdo->exec("ALTER TABLE enquiries ADD COLUMN admin_reply TEXT NULL AFTER message");
  $steps[] = '✔ Added admin_reply column';
}catch(PDOException $e){ $steps[] = 'ℹ admin_reply: '.$e->getMessage(); }
try{
  $pdo->exec("ALTER TABLE enquiries ADD COLUMN replied_at DATETIME NULL AFTER admin_reply");
  $steps[] = '✔ Added replied_at column';
}catch(PDOException $e){ $steps[] = 'ℹ replied_at: '.$e->getMessage(); }
try{
  $pdo->exec("ALTER TABLE enquiries ADD COLUMN reply_seen TINYINT(1) DEFAULT 0 AFTER replied_at");
  $steps[] = '✔ Added reply_seen column';
}catch(PDOException $e){ $steps[] = 'ℹ reply_seen: '.$e->getMessage(); }
?>
<!doctype html><html><head><meta charset="utf-8"><title>DB Update</title>
<style>body{font-family:Arial;max-width:520px;margin:60px auto;padding:20px}
.ok{color:green}.info{color:orange}
a{display:inline-block;margin-top:16px;background:#0787f5;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none}
</style></head><body>
<h2>Inquiry Reply — DB Update</h2>
<?php foreach($steps as $s): ?>
<p class="<?=str_starts_with($s,'✔')?'ok':'info'?>"><?=$s?></p>
<?php endforeach; ?>
<a href="../admin/enquiries.php">Go to Inquiries</a>
</body></html>
