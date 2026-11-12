<?php
session_start();
$dsn='mysql:host=localhost;dbname=lapaire_db;charset=utf8mb4';
try{
  $pdo=new PDO($dsn,'root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
}catch(PDOException $e){
  die('Database connection failed. Import database/lapaire.sql then check includes/config.php.');
}
// Absolute web root path for assets — adjust if you move the project
define('UPLOADS_URL', '/lapaire-faithful-final/lapaire-faithful-final/lapaire-faithful-final/assets/uploads/');
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verify_csrf(){if(!isset($_POST['csrf'])||!hash_equals($_SESSION['csrf']??'',$_POST['csrf']))die('Invalid request token.');}
function admin_only(){if(empty($_SESSION['admin_id'])){header('Location: login.php');exit;}}
function user_id(){return $_SESSION['user_id']??null;}
function user_logged_in(){return !empty($_SESSION['user_id']);}
