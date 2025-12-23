<?php
session_start();
require __DIR__.'/lib/config.php';
if(isset($_GET['enc'])){
  $raw=dec($_GET['enc']);
  $data=json_decode($raw,true);
  if($data&&isset($data['pid'])&&isset($data['qty'])){
    if(!isset($_SESSION['cart']))$_SESSION['cart']=[];
    $pid=intval($data['pid']);
    $qty=intval($data['qty']);
    if(!isset($_SESSION['cart'][$pid]))$_SESSION['cart'][$pid]=0;
    $_SESSION['cart'][$pid]+=$qty;
    header('Location: /cart.php');
    exit;
  }
}
$res=$mysqli->query("SELECT id,title,price,cover FROM products ORDER BY id ASC");
$items=[];
while($row=$res->fetch_assoc()){$items[]=$row;}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nebula Digital</title>
<link rel="stylesheet" href="/assets/style.css">
<script src="/assets/crypto.js"></script>
<script src="/assets/app.js"></script>
</head>
<body>
<header class="top">
  <div class="brand">Nebula Digital</div>
  <nav class="nav">
    <a href="/cart.php">购物车</a>
    <button class="deal" onclick="flash()">限时抢购</button>
  </nav>
</header>
<section class="hero">
  <div class="hero-inner">
    <h1>年终热卖</h1>
    <p>数码好物直降，限时抢购</p>
    <form class="search" onsubmit="return doSearch(this)">
      <input type="text" name="q" placeholder="搜一搜手机、电脑、耳机..." />
      <button type="submit">搜索</button>
    </form>
  </div>
</section>
<main class="grid" id="grid">
<?php foreach($items as $p){ ?>
  <div class="card">
    <img class="cover" src="<?php echo htmlspecialchars($p['cover']); ?>" alt="">
    <div class="title"><?php echo htmlspecialchars($p['title']); ?></div>
    <div class="price">¥<?php echo number_format($p['price'],2); ?></div>
    <button onclick="addToCart(<?php echo intval($p['id']); ?>)">加入购物车</button>
  </div>
<?php } ?>
</main>
<script>
function doSearch(f){var q=f.q.value.trim();if(!q){return false}fetch('/api/products.php?enc='+encodeURIComponent(e(JSON.stringify({q:q,time:Date.now()})))).then(r=>r.json()).then(function(resp){if(!resp||!resp.list){return}renderList(resp.list)});return false}
function renderList(list){var g=document.getElementById('grid');var html='';for(var i=0;i<list.length;i++){var p=list[i];html+=`<div class="card"><img class="cover" src="${p.cover}" alt=""><div class="title">${p.title}</div><div class="price">¥${Number(p.price).toFixed(2)}</div><button onclick="addToCart(${p.id})">加入购物车</button></div>`}g.innerHTML=html}
</script>
</body>
</html>
