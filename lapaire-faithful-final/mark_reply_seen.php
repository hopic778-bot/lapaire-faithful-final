<?php
require 'includes/config.php';
if(!user_logged_in()){ echo json_encode(['ok'=>false]); exit; }
$pdo->prepare('UPDATE enquiries SET reply_seen=1 WHERE user_id=?')->execute([user_id()]);
echo json_encode(['ok'=>true]);
