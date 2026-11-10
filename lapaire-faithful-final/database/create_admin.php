<?php
require dirname(__DIR__).'/includes/config.php';

// Create admins table if missing
$pdo->exec("CREATE TABLE IF NOT EXISTS admins(
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(100) NOT NULL
)");

$username     = 'admin';
$password     = 'Lapaire2024!';
$display_name = 'Admin';
$hash         = password_hash($password, PASSWORD_DEFAULT);

$pdo->prepare('INSERT INTO admins(username,password_hash,display_name)
  VALUES(?,?,?)
  ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), display_name=VALUES(display_name)')
  ->execute([$username, $hash, $display_name]);

// Verify
$row = $pdo->prepare('SELECT * FROM admins WHERE username=?');
$row->execute([$username]);
$row = $row->fetch();
$ok  = $row && password_verify($password, $row['password_hash']);
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Admin Setup</title>
<style>
  body{font-family:Arial,sans-serif;max-width:500px;margin:80px auto;padding:20px}
  .ok{background:#e6f9ee;border:1px solid #2a9d5c;border-radius:10px;padding:24px}
  .err{background:#fde;border:1px solid #c00;border-radius:10px;padding:24px}
  a{display:inline-block;margin-top:16px;background:#0787f5;color:#fff;
    padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:bold}
  p{margin:6px 0}
</style></head><body>
<?php if($ok): ?>
<div class="ok">
  <h2>✔ Admin account ready</h2>
  <p><b>Username:</b> <?=htmlspecialchars($username)?></p>
  <p><b>Password:</b> <?=htmlspecialchars($password)?></p>
  <p style="color:#c00;margin-top:12px;font-size:.9rem">⚠ Delete this file after logging in.</p>
  <a href="../admin/login.php">Go to Admin Login →</a>
</div>
<?php else: ?>
<div class="err">
  <h2>✘ Something went wrong</h2>
  <p>Could not create or verify the admin account.</p>
  <p>Check your database connection in <code>includes/config.php</code>.</p>
</div>
<?php endif; ?>
</body></html>
