<?php
require dirname(__DIR__).'/includes/config.php';
$steps=[];
try{
  $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('Pending','Active','Suspended') DEFAULT 'Pending' AFTER phone");
  $steps[]='✔ Added status column to users';
}catch(PDOException $e){$steps[]='ℹ '.$e->getMessage();}
// Set existing users to Active so they aren't locked out
try{
  $pdo->exec("UPDATE users SET status='Active' WHERE status='Pending'");
  $steps[]='✔ Existing users set to Active';
}catch(PDOException $e){$steps[]='ℹ '.$e->getMessage();}
?>
<!doctype html><html><head><meta charset="utf-8"><title>DB Update</title>
<style>body{font-family:Arial;max-width:500px;margin:60px auto;padding:20px}
.ok{color:green}.info{color:orange}
a{display:inline-block;margin-top:16px;background:#0787f5;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none}
</style></head><body>
<h2>User Status — DB Update</h2>
<?php foreach($steps as $s):?>
<p class="<?=str_starts_with($s,'✔')?'ok':'info'?>"><?=$s?></p>
<?php endforeach;?>
<a href="../admin/wishlists.php">Go to Users Panel</a>
</body></html>
