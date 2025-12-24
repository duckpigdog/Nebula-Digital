<?php
session_start();
require __DIR__.'/../lib/config.php';
if(!isset($_SESSION['admin_id'])){header('Location: /admin/login.php');exit;}
function isImage($filename){
  $types='.jpeg|.png|.gif';
  if(file_exists($filename)){
    $info=@getimagesize($filename);
    if(!$info||!isset($info[2]))return false;
    $ext=@image_type_to_extension($info[2]);
    if(stripos($types,$ext)>=0){return $ext;}
    return false;
  }
  return false;
}
if(isset($_FILES['file']) && $_FILES['file']['error']===UPLOAD_ERR_OK){
  $dir=__DIR__.'/../uploads';
  if(!is_dir($dir)){@mkdir($dir,0777,true);}
  $name=$_FILES['file']['name'];
  $tmp=$_FILES['file']['tmp_name'];
  $path=$dir.'/'.$name;
  @move_uploaded_file($tmp,$path);
  $ext=isImage($path);
  if($ext===false){
    @unlink($path);
    die("can't upload php file!");
  }
  $title=isset($_POST['title'])?trim($_POST['title']):$name;
  $price=isset($_POST['price'])?floatval($_POST['price']):0;
  $stock=isset($_POST['stock'])?intval($_POST['stock']):0;
  $cover='/uploads/'.$name;
  $arr=load_products();
  $id=next_product_id($arr);
  $arr[]=['id'=>$id,'title'=>$title,'price'=>$price,'stock'=>$stock,'cover'=>$cover];
  save_products($arr);
  header('Location: /admin/products.php');
  exit;
}
if(isset($_POST['enc'])){
  $raw=dec($_POST['enc']);
  $data=json_decode($raw,true);
  if($data&&isset($data['op'])){
    if($data['op']==='add'){
      $title=trim($data['title']??'');$cover=trim($data['cover']??'');$price=floatval($data['price']??0);$stock=intval($data['stock']??0);
      if($title!==''){
        $arr=load_products();
        $id=next_product_id($arr);
        $arr[]=['id'=>$id,'title'=>$title,'price'=>$price,'stock'=>$stock,'cover'=>$cover];
        save_products($arr);
      }
    }
    if($data['op']==='del'){
      $id=intval($data['id']??0);
      if($id>0){$arr=load_products();$out=[];foreach($arr as $p){if($p['id']!=$id)$out[]=$p;}save_products($out);}
    }
    if($data['op']==='inc'){
      $id=intval($data['id']??0);
      if($id>0){$arr=load_products();for($i=0;$i<count($arr);$i++){if($arr[$i]['id']==$id){$arr[$i]['stock']=intval($arr[$i]['stock'])+1;}}save_products($arr);}
    }
    if($data['op']==='dec'){
      $id=intval($data['id']??0);
      if($id>0){$arr=load_products();for($i=0;$i<count($arr);$i++){if($arr[$i]['id']==$id){$arr[$i]['stock']=max(intval($arr[$i]['stock'])-1,0);}}save_products($arr);}
    }
  }
  header('Location: /admin/products.php');
  exit;
}
$list=load_products();
usort($list,function($a,$b){return $b['id']<=>$a['id'];});
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>商品管理</title>
<link rel="stylesheet" href="/assets/style.css">
<script src="/assets/crypto.js"></script>
</head>
<body>
<div class="admin-wrap">
  <div class="admin-top">
    <div class="admin-brand">商品管理</div>
    <div><a class="btn" href="/admin/dashboard.php">返回控制台</a></div>
  </div>
  <div class="admin-card" style="max-width:1200px;margin:16px auto;">
    <div style="font-weight:700;margin-bottom:8px">新增商品</div>
    <form method="post" onsubmit="return encAdd(this)">
      <div class="admin-grid" style="grid-template-columns:repeat(4,minmax(180px,1fr));gap:10px">
        <input class="form-input" type="text" name="title" placeholder="标题">
        <input class="form-input" type="number" step="0.01" name="price" placeholder="价格">
        <input class="form-input" type="number" name="stock" placeholder="库存">
        <input class="form-input" type="text" name="cover" placeholder="封面URL">
      </div>
      <input type="hidden" name="enc" value="">
      <div class="login-actions" style="margin-top:12px">
        <button class="btn btn-primary" type="submit">添加</button>
      </div>
    </form>
  </div>
  <div class="admin-card" style="max-width:1200px;margin:16px auto;">
    <div style="font-weight:700;margin-bottom:8px">上传图片新增商品</div>
    <form method="post" enctype="multipart/form-data">
      <div class="admin-grid" style="grid-template-columns:repeat(4,minmax(180px,1fr));gap:10px">
        <input class="form-input" type="text" name="title" placeholder="标题(可选)">
        <input class="form-input" type="number" step="0.01" name="price" placeholder="价格(可选)">
        <input class="form-input" type="number" name="stock" placeholder="库存(可选)">
        <input class="form-input" type="file" name="file" placeholder="选择文件">
      </div>
      <div class="login-actions" style="margin-top:12px">
        <button class="btn btn-primary" type="submit">上传并新增</button>
      </div>
    </form>
  </div>
  <div class="admin-table">
    <table>
      <thead><tr><th>ID</th><th>封面</th><th>标题</th><th>价格</th><th>库存</th><th>操作</th></tr></thead>
      <tbody>
        <?php foreach($list as $p){ ?>
        <tr>
          <td><?php echo intval($p['id']); ?></td>
          <td><img src="<?php echo htmlspecialchars($p['cover']); ?>" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px"></td>
          <td><?php echo htmlspecialchars($p['title']); ?></td>
          <td>¥<?php echo number_format($p['price'],2); ?></td>
          <td><?php echo intval($p['stock']); ?></td>
          <td>
            <form method="post" style="display:inline" onsubmit="return encOp(this,'inc',<?php echo intval($p['id']); ?>)"><input type="hidden" name="enc" value=""><button class="btn" type="submit">+1</button></form>
            <form method="post" style="display:inline" onsubmit="return encOp(this,'dec',<?php echo intval($p['id']); ?>)"><input type="hidden" name="enc" value=""><button class="btn" type="submit">-1</button></form>
            <form method="post" style="display:inline" onsubmit="return encOp(this,'del',<?php echo intval($p['id']); ?>)"><input type="hidden" name="enc" value=""><button class="btn btn-danger" type="submit">删除</button></form>
            <?php if(strpos($p['cover'],'/uploads/')===0){ ?>
              <form action="/uploads/view.php" method="post" style="display:inline">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars(basename($p['cover'])); ?>">
                <button class="btn" type="submit">预览</button>
              </form>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<script>
function encAdd(f){var title=f.title.value.trim();var price=parseFloat(f.price.value||'0');var stock=parseInt(f.stock.value||'0');var cover=f.cover.value.trim();f.enc.value=e(JSON.stringify({op:'add',title:title,price:price,stock:stock,cover:cover,time:Date.now()}));f.title.disabled=true;f.price.disabled=true;f.stock.disabled=true;f.cover.disabled=true;return true}
function encOp(f,op,id){f.enc.value=e(JSON.stringify({op:op,id:id,time:Date.now()}));return true}
</script>
</body>
</html>
