<?php
require 'includes/config.php';
if(!user_logged_in()){ echo json_encode(['ok'=>false]); exit; }
try{
  $pdo->prepare('UPDATE appointments SET appt_notif_seen=1 WHERE user_id=?')->execute([user_id()]);
}catch(Exception $e){}
echo json_encode(['ok'=>true]);
