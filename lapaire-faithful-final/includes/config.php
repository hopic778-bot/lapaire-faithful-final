<?php
session_start();

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'lapaire_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Import database/lapaire.sql then check includes/config.php.');
}

// Absolute web root path for assets - auto-adjusts for Render vs local WAMP
$uploads_path = getenv('DB_HOST') ? '/assets/uploads' : '/lapaire-faithful-final/lapaire-faithful-final/assets/uploads';
define('UPLOADS_URL', $uploads_path);

function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function csrf(){if(empty($_SESSION['csrf'])){$_SESSION['csrf']=bin2hex(random_bytes(32));}return $_SESSION['csrf'];}
function verify_csrf(){if(!isset($_POST['csrf'])||!hash_equals($_SESSION['csrf']??'',$_POST['csrf'])){die('Invalid CSRF');}}
function admin_only(){if(empty($_SESSION['admin_id'])){header('Location: login.php');exit;}}
function user_id(){return $_SESSION['user_id']??null;}
function user_logged_in(){return !empty($_SESSION['user_id']);}
