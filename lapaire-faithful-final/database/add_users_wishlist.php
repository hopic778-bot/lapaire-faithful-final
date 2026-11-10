<?php
require dirname(__DIR__).'/includes/config.php';

$tables = [
  "CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(140) UNIQUE NOT NULL,
    phone VARCHAR(40),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",
  "CREATE TABLE IF NOT EXISTS wishlist(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish(user_id,product_id),
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
  )",
];

foreach($tables as $sql){
  $pdo->exec($sql);
}
echo '<p style="font-family:Arial;color:green;padding:20px">✔ Tables <b>users</b> and <b>wishlist</b> created successfully. <a href="../glasses.php">Go to Glasses</a></p>';
?>
