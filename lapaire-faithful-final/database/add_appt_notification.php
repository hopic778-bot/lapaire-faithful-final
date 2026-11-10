<?php
require dirname(__DIR__).'/includes/config.php';
$steps=[];
foreach([
  "ALTER TABLE appointments ADD COLUMN user_id INT NULL AFTER id",
  "ALTER TABLE appointments ADD COLUMN appt_notif_seen TINYINT(1) DEFAULT 0 AFTER status",
  "ALTER TABLE appointments ADD COLUMN admin_note VARCHAR(255) NULL AFTER appt_notif_seen",
] as $sql){
  try{ $pdo->exec($sql); $steps[]='✔ '.explode('ADD COLUMN',$sql)[1]; }
  catch(PDOException $e){ $steps[]='ℹ '.$e->getMessage(); }
}
?><!doctype html><html><head><meta charset="utf-8"><title>DB</title>
<style>body{font-family:Arial;max-width:500px;margin:60px auto;padding:20px}.ok{color:green}.info{color:orange}
a{display:inline-block;margin-top:16px;background:#0787f5;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none}</style></head><body>
<h2>Appointment Notification — DB Update</h2>
<?php foreach($steps as $s): ?><p class="<?=str_starts_with($s,'✔')?'ok':'info'?>"><?=$s?></p><?php endforeach; ?>
<a href="../admin/appointments.php">Go to Appointments</a>
</body></html>
