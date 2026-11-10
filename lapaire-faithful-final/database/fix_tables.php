<?php
require dirname(__DIR__).'/includes/config.php';
$errors = [];
$done = [];

$tables = [
  'products' => "CREATE TABLE IF NOT EXISTS products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(60) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    shape VARCHAR(60),
    material VARCHAR(60),
    color VARCHAR(60),
    description TEXT,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",
  'branches' => "CREATE TABLE IF NOT EXISTS branches(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    city VARCHAR(80) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    email VARCHAR(140) NOT NULL,
    hours VARCHAR(160) NOT NULL,
    active TINYINT(1) DEFAULT 1
  )",
  'appointments' => "CREATE TABLE IF NOT EXISTS appointments(
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(140) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    branch_id INT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",
  'enquiries' => "CREATE TABLE IF NOT EXISTS enquiries(
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(140) NOT NULL,
    phone VARCHAR(40),
    subject VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('New','Read','Closed') DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",
  'posts' => "CREATE TABLE IF NOT EXISTS posts(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    excerpt VARCHAR(300) NOT NULL,
    body TEXT NOT NULL,
    published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",
  'admins' => "CREATE TABLE IF NOT EXISTS admins(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL
  )",
];

foreach($tables as $name => $sql){
  try { $pdo->exec($sql); $done[] = "✔ Table <b>$name</b> — OK"; }
  catch(PDOException $e){ $errors[] = "✘ $name: ".$e->getMessage(); }
}

// Seed products if empty
$count = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
if($count == 0){
  $seed = [
    ['Kebili-C1',   'Prescription', 105000, 'Round',     'Metal',   'Black', 'Modern round metal frame'],
    ['Carnot-C2',   'Prescription', 105000, 'Round',     'Mixed',   'Brown', 'Everyday mixed-material frame'],
    ['Cape Town-C1','Prescription', 105000, 'Rectangle', 'TR90',    'Black', 'Light rectangular frame'],
    ['Kenitra-C5',  'Prescription', 105000, 'Square',    'Plastic', 'Blue',  'Bold square frame'],
    ['Jijel-C3',    'Prescription', 105000, 'Cat-eye',   'Metal',   'Gold',  'Metal cat-eye frame'],
    ['Pate-C2',     'Prescription',  50000, 'Rectangle', 'Plastic', 'Black', 'Frame-only starter option'],
    ['Nairobi-S1',  'Sunglasses',    85000, 'Wraparound','Plastic', 'Black', 'Classic wraparound sunglass'],
    ['Lagos-S2',    'Sunglasses',    95000, 'Oval',      'Metal',   'Gold',  'Oval metal sunglass'],
    ['Accra-B1',    'Blue Light',    75000, 'Rectangle', 'TR90',    'Clear', 'Blue light screen frame'],
    ['Kampala-K1',  'Kids',          55000, 'Round',     'Rubber',  'Red',   'Durable kids frame'],
  ];
  $st = $pdo->prepare('INSERT INTO products(name,category,price,shape,material,color,description) VALUES(?,?,?,?,?,?,?)');
  foreach($seed as $r){ $st->execute($r); }
  $done[] = "✔ Seeded <b>".count($seed)." products</b>";
}

// Seed branches if empty
$bc = $pdo->query('SELECT COUNT(*) FROM branches')->fetchColumn();
if($bc == 0){
  $br = [
    ['Kampala Road','Kampala','I&M Bank Building, Opposite FoodHub, Kampala Road','+256770748938','kampalaroad@lapaire.ug','Mon-Sat 8:30am-7pm'],
    ['Ntinda','Kampala','Stanbic Bank Building, Kiwatule Road','+256776886393','ntinda@lapaire.ug','Mon-Sat 8:30am-7pm'],
    ['Entebbe','Entebbe','Entebbe town center','+256700000001','entebbe@lapaire.ug','Mon-Sat'],
    ['Jinja','Jinja','Jinja Main Street','+256700000002','jinja@lapaire.ug','Mon-Sat'],
    ['Masaka','Masaka','Masaka town center','+256700000003','masaka@lapaire.ug','Mon-Sat'],
    ['Mbale','Mbale','Mbale town center','+256700000004','mbale@lapaire.ug','Mon-Sat'],
    ['Mbarara','Mbarara','Mbarara town center','+256700000005','mbarara@lapaire.ug','Mon-Sat'],
  ];
  $st = $pdo->prepare('INSERT INTO branches(name,city,address,phone,email,hours) VALUES(?,?,?,?,?,?)');
  foreach($br as $r){ $st->execute($r); }
  $done[] = "✔ Seeded <b>".count($br)." branches</b>";
}

// Seed posts if empty
$pc = $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
if($pc == 0){
  $posts = [
    ['5 Tips to Keep Your Eyes Healthy','Simple habits that support everyday eye care.','Full article coming soon.'],
    ['How to Choose the Right Frame','Consider shape, width, material and daily use.','Full article coming soon.'],
    ['The Importance of Regular Eye Tests','Eye tests can help identify vision correction needs.','Full article coming soon.'],
  ];
  $st = $pdo->prepare('INSERT INTO posts(title,excerpt,body) VALUES(?,?,?)');
  foreach($posts as $r){ $st->execute($r); }
  $done[] = "✔ Seeded <b>".count($posts)." posts</b>";
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Lapaire DB Setup</title>
<style>body{font-family:Arial,sans-serif;max-width:640px;margin:60px auto;padding:20px}
h2{color:#082d67}.ok{color:#1a7a2e}.err{color:#c00}
a{display:inline-block;margin-top:20px;background:#0787f5;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none}
</style></head><body>
<h2>✔ Lapaire DB Setup Complete</h2>
<?php foreach($done as $d): echo "<p class='ok'>$d</p>"; endforeach; ?>
<?php foreach($errors as $e): echo "<p class='err'>$e</p>"; endforeach; ?>
<a href="../index.php">Go to Site</a>
<a href="../glasses.php" style="background:#0a5">Test Glasses Page</a>
</body></html>
