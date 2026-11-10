<?php
require dirname(__DIR__).'/includes/config.php';

$username = 'admin';
$password = 'admin123';  // simple password, no special chars

// 1. Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS admins(
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(100) NOT NULL
)");

// 2. Delete any existing admin and re-insert fresh
$pdo->prepare('DELETE FROM admins WHERE username=?')->execute([$username]);
$hash = password_hash($password, PASSWORD_BCRYPT);
$pdo->prepare('INSERT INTO admins(username,password_hash,display_name) VALUES(?,?,?)')->execute([$username,$hash,'Admin']);

// 3. Immediately verify it works
$row = $pdo->prepare('SELECT * FROM admins WHERE username=?');
$row->execute([$username]);
$row = $row->fetch();
$verified = $row && password_verify($password, $row['password_hash']);
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Fix Admin</title>
<style>
body{font-family:Arial,sans-serif;max-width:520px;margin:60px auto;padding:24px;background:#f4f7fb}
.box{background:#fff;border-radius:14px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,.08)}
.ok{color:#166534;background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:16px;margin-bottom:16px}
.err{color:#991b1b;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:16px;margin-bottom:16px}
.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:.95rem}
a.btn{display:inline-block;margin-top:18px;background:#0787f5;color:#fff;
  padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:1rem}
</style>
</head><body>
<div class="box">
  <h2 style="color:#082d67;margin-top:0">Admin Account Fix</h2>

  <?php if($verified): ?>
  <div class="ok">✔ Admin account created and verified successfully!</div>
  <div class="row"><span>Username</span><b><?=htmlspecialchars($username)?></b></div>
  <div class="row"><span>Password</span><b><?=htmlspecialchars($password)?></b></div>
  <div class="row"><span>Hash check</span><b style="color:green">PASS ✓</b></div>
  <a class="btn" href="../admin/login.php">Go to Admin Login →</a>
  <?php else: ?>
  <div class="err">✘ Verification failed — something is wrong with password_hash on this server.</div>
  <p>PHP version: <?=PHP_VERSION?></p>
  <p>password_hash available: <?=function_exists('password_hash')?'YES':'NO'?></p>
  <?php endif; ?>
</div>
</body></html>
