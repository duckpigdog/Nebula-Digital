<?php
session_start();
require __DIR__.'/../lib/config.php';
if(isset($_POST['enc'])){
  $ip=isset($_SERVER['HTTP_X_FORWARDED_FOR'])?trim(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR'])[0]):$_SERVER['REMOTE_ADDR'];
  if($ip===''){$ip='0.0.0.0';}
  $now=time();
  $stmt0=$mysqli->prepare("SELECT fail,lock_until FROM bf_ip WHERE ip=?");
  $stmt0->bind_param('s',$ip);
  $stmt0->execute();
  $rs0=$stmt0->get_result();
  $row0=$rs0?$rs0->fetch_assoc():null;
  if($row0&&intval($row0['lock_until'])>$now){
    $err='当前IP已锁定，请稍后再试';
  } else {
    $raw=dec($_POST['enc']);
    $data=json_decode($raw,true);
    $u=isset($data['u'])?trim($data['u']):'';
    $p=isset($data['p'])?trim($data['p']):'';
    $stmt=$mysqli->prepare("SELECT id FROM users WHERE username=? AND password=? AND role='admin' LIMIT 1");
    $stmt->bind_param('ss',$u,$p);
    $stmt->execute();
    $res=$stmt->get_result();
    if($row=$res->fetch_assoc()){
      $_SESSION['admin_id']=$row['id'];
      $stmt1=$mysqli->prepare("REPLACE INTO bf_ip(ip,fail,lock_until) VALUES(?,0,0)");
      $stmt1->bind_param('s',$ip);
      $stmt1->execute();
      header('Location: /admin/dashboard.php');
      exit;
    } else {
      $fail=$row0?intval($row0['fail'])+1:1;
      $lock=0;
      if($fail>=5){$lock=$now+60;$fail=0;}
      $stmt2=$mysqli->prepare("REPLACE INTO bf_ip(ip,fail,lock_until) VALUES(?,?,?)");
      $stmt2->bind_param('sii',$ip,$fail,$lock);
      $stmt2->execute();
      $err='账号或密码错误';
    }
  }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>后台登录</title>
<link rel="stylesheet" href="/assets/style.css">
<script src="/assets/crypto.js"></script>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-brand">
      <div class="login-title">登录</div>
    </div>
    <div class="login-sub">请输入账号与密码完成登录</div>
    <?php if(isset($err)){ ?><div style="color:#d33;margin-bottom:10px"><?php echo htmlspecialchars($err); ?></div><?php } ?>
    <form method="post" onsubmit="return encLogin(this)">
      <div class="form-row">
        <input class="form-input" type="text" name="username" placeholder="用户名">
      </div>
      <div class="form-row">
        <input class="form-input" type="password" name="password" placeholder="密码">
      </div>
      <input type="hidden" name="enc" value="">
      <div class="login-actions">
        <button class="btn btn-primary" type="submit">登录</button>
      </div>
    </form>
  </div>
</div>
<script>
function encLogin(f){var u=f.username.value;var p=f.password.value;f.enc.value=e(JSON.stringify({u:u,p:p,time:Date.now()}));f.username.disabled=true;f.password.disabled=true;return true}
</script>
</body>
</html>
