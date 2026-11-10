<?php
require 'includes/config.php';
header('Content-Type: application/json');

if(!user_logged_in()){
  echo json_encode(['status'=>'auth_required']);exit;
}

verify_csrf();
$pid = (int)($_POST['product_id']??0);
$uid = user_id();

if(!$pid){echo json_encode(['status'=>'error']);exit;}

// Check if already wishlisted
$chk = $pdo->prepare('SELECT id FROM wishlist WHERE user_id=? AND product_id=?');
$chk->execute([$uid,$pid]);

if($chk->fetch()){
  // Remove
  $pdo->prepare('DELETE FROM wishlist WHERE user_id=? AND product_id=?')->execute([$uid,$pid]);
  echo json_encode(['status'=>'removed']);
} else {
  // Add
  $pdo->prepare('INSERT INTO wishlist(user_id,product_id) VALUES(?,?)')->execute([$uid,$pid]);
  echo json_encode(['status'=>'added']);
}
