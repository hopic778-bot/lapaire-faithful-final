<?php admin_only();
$currentPage = basename($_SERVER['PHP_SELF']);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=e($adminTitle??'Admin')?> | Lapaire Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-shell">

  <!-- Sidebar -->
  <aside class="admin-side">
    <div class="admin-brand">
      <img src="../assets/images/Designer (4).png" alt="Lapaire" class="admin-logo">
      <div class="admin-brand-text">
        <span class="admin-brand-name">Lapaire</span>
        <span class="admin-brand-sub">Admin Panel</span>
      </div>
    </div>

    <div class="admin-nav-label">Main Menu</div>
    <?php
    $navItems = [
      'index.php'        => ['⊞',  'Dashboard'],
      'appointments.php' => ['📅', 'Appointments'],
      'products.php'     => ['👓', 'Frames & Products'],
      'branches.php'     => ['📍', 'Branches'],
      'enquiries.php'    => ['✉️',  'Inquiries'],
      'posts.php'        => ['📝', 'Blog / News'],
      'wishlists.php'    => ['♥',  'Wishlist & Users'],
    ];
    foreach($navItems as $file => [$icon, $label]):
      $active = $currentPage === $file ? 'active' : '';
    ?>
    <a href="<?=$file?>" class="admin-nav-link <?=$active?>">
      <span class="admin-nav-ico"><?=$icon?></span>
      <span><?=$label?></span>
    </a>
    <?php endforeach; ?>

    <div class="admin-nav-label mt-3">Settings</div>
    <a href="../index.php" class="admin-nav-link" target="_blank">
      <span class="admin-nav-ico">🌐</span><span>View Website</span>
    </a>
    <a href="logout.php" class="admin-nav-link admin-nav-logout">
      <span class="admin-nav-ico">↪</span><span>Logout</span>
    </a>
  </aside>

  <!-- Main content -->
  <div class="admin-body">

    <!-- Top bar -->
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <h5 class="admin-page-title mb-0"><?=e($adminTitle??'Dashboard')?></h5>
      </div>
      <div class="admin-topbar-right">
        <span class="admin-topbar-greeting">👋 Welcome, <?=e($_SESSION['admin_name']??'Admin')?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Logout</a>
      </div>
    </header>

    <!-- Page content starts here -->
    <div class="admin-content">
