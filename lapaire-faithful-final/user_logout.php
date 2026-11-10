<?php
require 'includes/config.php';
unset($_SESSION['user_id'],$_SESSION['user_name']);
header('Location: glasses.php');exit;
