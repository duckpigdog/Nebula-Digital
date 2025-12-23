<?php
session_start();
require __DIR__.'/lib/config.php';
$msg='';
if(isset($_GET['enc'])){
  $raw=dec($_GET['enc']);
  $data=json_decode($raw,true);
  if($data&&isset($data['op'])){
    if(!isset($_SESSION['cart']))$_SESSION['cart']=[];
    $op=$data['op'];
    $pid=isset($data['pid'])?intval($data['pid']):0;
    if($op==='inc'&&$pid>0){if(!isset($_SESSION['cart'][$pid]))$_SESSION['cart'][$pid]=0;$_SESSION['cart'][$pid]+=1;}
    if($op==='dec'&&$pid>0){if(isset($_SESSION['cart'][$pid])){$_SESSION['cart'][$pid]-=1;if($_SESSION['cart'][$pid]<=0)unset($_SESSION['cart'][$pid]);}}
    if($op==='del'&&$pid>0){if(isset($_SESSION['cart'][$pid]))unset($_SESSION['cart'][$pid]);}
    if($op==='clear'){$_SESSION['cart']=[];}
    if($op==='checkout'){
      $cart=isset($_SESSION['cart'])?$_SESSION['cart']:[];
      $ids=array_keys($cart);
      $total=0;
      if(count($ids)){
        $in=implode(',',array_map('intval',$ids));
        $res=$mysqli->query("SELECT id,price FROM products WHERE id IN ($in)");
        while($row=$res->fetch_assoc()){$qty=$cart[$row['id']];$total+=floatval($row['price'])*$qty;}
      }
      $uid=isset($_SESSION['admin_id'])?intval($_SESSION['admin_id']):null;
      $uidsql=is_null($uid)?'NULL':intval($uid);
      $mysqli->query("INSERT INTO orders(user_id,total) VALUES($uidsql,".floatval($total).")");
      $oid=$mysqli->insert_id;
      $_SESSION['cart']=[];
      header('Location: /cart.php?order='.$oid);
      exit;
    }
    header('Location: /cart.php');
    exit;
  }
}
$cart=isset($_SESSION['cart'])?$_SESSION['cart']:[];
$ids=array_keys($cart);
$list=[];
$total=0;
if(count($ids)){
  $in=implode(',',array_map('intval',$ids));
  $res=$mysqli->query("SELECT id,title,price,cover FROM products WHERE id IN ($in)");
  while($row=$res->fetch_assoc()){
    $qty=$cart[$row['id']];
    $list[]=['id'=>$row['id'],'title'=>$row['title'],'price'=>$row['price'],'qty'=>$qty,'cover'=>$row['cover']];
    $total+=floatval($row['price'])*$qty;
  }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>购物车</title>
<link rel="stylesheet" href="/assets/style.css">
<script src="/assets/crypto.js"></script>
</head>
<body>
<div class="cart">
  <h1>购物车</h1>
  <?php if(isset($_GET['order'])){ ?><div style="padding:8px 0;color:#0a0">已下单，订单号 <?php echo intval($_GET['order']); ?></div><?php } ?>
  <div class="cart-box">
    <?php foreach($list as $it){ ?>
      <div class="cart-item">
        <div class="meta">
          <img src="<?php echo htmlspecialchars($it['cover']); ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:10px">
          <div>
            <div class="title"><?php echo htmlspecialchars($it['title']); ?></div>
            <div class="sub">单价 ¥<?php echo number_format($it['price'],2); ?></div>
            <div class="qty">
              <button onclick="dec(<?php echo intval($it['id']); ?>)">-</button>
              <span><?php echo intval($it['qty']); ?></span>
              <button onclick="inc(<?php echo intval($it['id']); ?>)">+</button>
              <button class="btn btn-danger" style="margin-left:8px" onclick="delItem(<?php echo intval($it['id']); ?>)">移除</button>
            </div>
          </div>
        </div>
        <div style="font-weight:700;color:#d33">¥<?php echo number_format($it['price']*$it['qty'],2); ?></div>
      </div>
    <?php } ?>
  </div>
  <div class="cart-summary">
    <div>合计</div>
    <div style="font-size:18px;font-weight:700;color:#111">¥<?php echo number_format($total,2); ?></div>
  </div>
  <div class="cart-actions">
    <a class="btn" href="/index.php">继续购物</a>
    <button class="btn" onclick="clearCart()">清空</button>
    <button class="btn btn-primary" onclick="checkout()">结算</button>
  </div>
</div>
<script>
function inc(pid){location.href='/cart.php?enc='+encodeURIComponent(e(JSON.stringify({op:'inc',pid:pid,time:Date.now()})))}
function dec(pid){location.href='/cart.php?enc='+encodeURIComponent(e(JSON.stringify({op:'dec',pid:pid,time:Date.now()})))}
function delItem(pid){location.href='/cart.php?enc='+encodeURIComponent(e(JSON.stringify({op:'del',pid:pid,time:Date.now()})))}
function clearCart(){location.href='/cart.php?enc='+encodeURIComponent(e(JSON.stringify({op:'clear',time:Date.now()})))}
function checkout(){location.href='/cart.php?enc='+encodeURIComponent(e(JSON.stringify({op:'checkout',time:Date.now()})))}
</script>
</body>
</html>
