<?php
$DB_HOST='127.0.0.1:3316';
$DB_USER='policeman';
$DB_PASS='policeman';
$DB_NAME='sir-database';
$k='k9B!1x@Z';
function enc($s){global $k;$o='';$l=strlen($k);$n=strlen($s);for($i=0;$i<$n;$i++){ $o.=chr((ord($s[$i])^ord($k[$i%$l]))+3);}return base64_encode($o);}
function dec($b){global $k;$a=base64_decode($b);$o='';$l=strlen($k);$n=strlen($a);for($i=0;$i<$n;$i++){ $o.=chr(((ord($a[$i])-3)^ord($k[$i%$l])));}return $o;}
function j($arr){header('Content-Type: application/json; charset=utf-8');echo json_encode($arr,JSON_UNESCAPED_UNICODE);exit;}
$mysqli=@new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($mysqli->connect_errno){
  $tmp=new mysqli($DB_HOST,$DB_USER,$DB_PASS);
  $tmp->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
  $tmp->close();
  $mysqli=new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
}
$mysqli->set_charset('utf8mb4');
$mysqli->query("CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(64) UNIQUE,password VARCHAR(128),role ENUM('user','admin') DEFAULT 'user')");
$mysqli->query("CREATE TABLE IF NOT EXISTS products(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(128),price DECIMAL(10,2),stock INT DEFAULT 0,cover VARCHAR(256))");
$mysqli->query("CREATE TABLE IF NOT EXISTS orders(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,total DECIMAL(10,2),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$mysqli->query("CREATE TABLE IF NOT EXISTS bf_ip(ip VARCHAR(64) PRIMARY KEY,fail INT DEFAULT 0,lock_until INT DEFAULT 0)");
$rc=$mysqli->query("SELECT COUNT(*) c FROM users");
$c=$rc?$rc->fetch_assoc()['c']:0;
if($c==0){
  $mysqli->query("INSERT INTO users(username,password,role) VALUES('admin01','jd_admin_123','admin'),('ops02','supply_chain','admin'),('testuser','123456','user')");
}
$mysqli->query("UPDATE users SET password='password' WHERE username='admin' AND role='admin'");
$mysqli->query("INSERT INTO users(username,password,role) SELECT 'admin','password','admin' WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='admin' AND role='admin')");
$rp=$mysqli->query("SELECT COUNT(*) c FROM products");
$cp=$rp?$rp->fetch_assoc()['c']:0;
if($cp==0){
  $mysqli->query("INSERT INTO products(title,price,stock,cover) VALUES
    ('智能手机 X',3999.00,50,'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop'),
    ('轻薄笔记本 Pro',6999.00,30,'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'),
    ('降噪耳机',899.00,120,'https://images.unsplash.com/photo-1511367461989-f85a21fda167?w=800&auto=format&fit=crop'),
    ('智能手表',1299.00,80,'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&auto=format&fit=crop')
  ");
}
$mysqli->query("UPDATE products SET cover='https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop' WHERE title='智能手机 X' AND cover LIKE '/assets/img/%'");
$mysqli->query("UPDATE products SET cover='https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop' WHERE title='轻薄笔记本 Pro' AND cover LIKE '/assets/img/%'");
$mysqli->query("UPDATE products SET cover='https://images.unsplash.com/photo-1511367461989-f85a21fda167?w=800&auto=format&fit=crop' WHERE title='降噪耳机'");
$mysqli->query("UPDATE products SET cover='https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&auto=format&fit=crop' WHERE title='智能手表'");
$mysqli->query("INSERT INTO products(title,price,stock,cover)
  SELECT '蓝牙音箱',499.00,200,'https://images.unsplash.com/photo-1519677100203-a0e668c92439?w=800&auto=format&fit=crop'
  WHERE NOT EXISTS (SELECT 1 FROM products WHERE title='蓝牙音箱')");
$mysqli->query("INSERT INTO products(title,price,stock,cover)
  SELECT '机械键盘',699.00,150,'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'
  WHERE NOT EXISTS (SELECT 1 FROM products WHERE title='机械键盘')");
$mysqli->query("INSERT INTO products(title,price,stock,cover)
  SELECT '游戏鼠标',399.00,180,'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'
  WHERE NOT EXISTS (SELECT 1 FROM products WHERE title='游戏鼠标')");
$mysqli->query("INSERT INTO products(title,price,stock,cover)
  SELECT '显示器 27\"',1699.00,60,'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'
  WHERE NOT EXISTS (SELECT 1 FROM products WHERE title='显示器 27\"')");
