USE sir-database;
CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(64) UNIQUE,password VARCHAR(128),role ENUM('user','admin') DEFAULT 'user');
CREATE TABLE IF NOT EXISTS products(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(128),price DECIMAL(10,2),stock INT DEFAULT 0,cover VARCHAR(256));
CREATE TABLE IF NOT EXISTS orders(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,total DECIMAL(10,2),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS bf_ip(ip VARCHAR(64) PRIMARY KEY,fail INT DEFAULT 0,lock_until INT DEFAULT 0);
INSERT INTO users(username,password,role) VALUES('admin','password','admin') ON DUPLICATE KEY UPDATE password='password';
INSERT INTO products(title,price,stock,cover) VALUES
('智能手机 X',3999.00,50,'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop'),
('轻薄笔记本 Pro',6999.00,30,'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'),
('降噪耳机',899.00,120,'https://images.unsplash.com/photo-1511367461989-f85a21fda167?w=800&auto=format&fit=crop'),
('智能手表',1299.00,80,'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&auto=format&fit=crop')
ON DUPLICATE KEY UPDATE stock=VALUES(stock);
