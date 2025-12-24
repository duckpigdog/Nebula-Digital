<?php
session_start();
require __DIR__.'/../lib/config.php';
if(!isset($_SESSION['admin_id'])){header('Location: /admin/login.php');exit;}
$users=get_users();
$c1=count($users);
$c2=0;foreach($users as $u){if($u['role']==='admin')$c2++;}
$c3=0;
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>控制台</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="admin-wrap">
  <div class="admin-top">
    <div class="admin-brand">控制台</div>
    <div><a class="btn" href="/admin/products.php">商品管理</a> <a class="btn" href="/index.php">返回首页</a></div>
  </div>
  <div class="admin-grid">
    <div class="admin-card"><div>用户总数</div><div class="n"><?php echo intval($c1); ?></div></div>
    <div class="admin-card"><div>管理员数</div><div class="n"><?php echo intval($c2); ?></div></div>
    <div class="admin-card"><div>订单数</div><div class="n"><?php echo intval($c3); ?></div></div>
  </div>
  <div class="admin-table">
    <table>
      <thead><tr><th>ID</th><th>账号</th><th>角色</th></tr></thead>
      <tbody>
      <?php foreach($users as $u){ ?>
        <tr><td><?php echo intval($u['id']); ?></td><td><?php echo htmlspecialchars($u['username']); ?></td><td><?php echo htmlspecialchars($u['role']); ?></td></tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
