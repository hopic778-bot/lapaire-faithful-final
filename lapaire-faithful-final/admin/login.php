<?php
require '../includes/config.php';
if(!empty($_SESSION['admin_id'])){header('Location:index.php');exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $st=$pdo->prepare('SELECT * FROM admins WHERE username=?');
  $st->execute([trim($_POST['username'])]);
  $a=$st->fetch();
  if($a && password_verify($_POST['password'],$a['password_hash'])){
    session_regenerate_id(true);
    $_SESSION['admin_id']=$a['id'];
    $_SESSION['admin_name']=$a['display_name'];
    header('Location:index.php');exit;
  }
  $err='Invalid username or password.';
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login | Lapaire</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-login">
  <div class="admin-login-card">

    <div class="text-center mb-4">
      <img src="../assets/images/Designer (4).png" alt="Lapaire" class="admin-login-logo">
      <p class="text-muted mt-2 mb-0">Admin Panel — Sign in to continue</p>
    </div>

    <?php if($err):?>
    <div class="alert alert-danger rounded-3"><?=e($err)?></div>
    <?php endif;?>

    <form method="post">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <input required name="username" class="form-control form-control-lg" placeholder="admin" autocomplete="username">
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Password</label>
        <input required type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" autocomplete="current-password">
      </div>
      <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-6">Sign In &rarr;</button>
    </form>

    <div class="text-center mt-4">
      <a href="../index.php" class="text-muted small">← Back to website</a>
    </div>
  </div>
</body>
</html>
