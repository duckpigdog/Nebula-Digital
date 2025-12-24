<?php
$k='k9B!1x@Z';
function enc($s){global $k;$o='';$l=strlen($k);$n=strlen($s);for($i=0;$i<$n;$i++){ $o.=chr((ord($s[$i])^ord($k[$i%$l]))+3);}return base64_encode($o);}
function dec($b){global $k;$a=base64_decode($b);$o='';$l=strlen($k);$n=strlen($a);for($i=0;$i<$n;$i++){ $o.=chr(((ord($a[$i])-3)^ord($k[$i%$l])));}return $o;}
function j($arr){header('Content-Type: application/json; charset=utf-8');echo json_encode($arr,JSON_UNESCAPED_UNICODE);exit;}

function data_dir(){return __DIR__.'/../data';}
function products_file(){return data_dir().'/products.json';}
function iplock_file(){return data_dir().'/iplock.json';}

function ensure_data_dir(){ $d=data_dir(); if(!is_dir($d)){@mkdir($d,0777,true);} }

function default_products(){
  return [
    ['id'=>1,'title'=>'智能手机 X','price'=>3999.00,'stock'=>50,'cover'=>'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop'],
    ['id'=>2,'title'=>'轻薄笔记本 Pro','price'=>6999.00,'stock'=>30,'cover'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'],
    ['id'=>3,'title'=>'降噪耳机','price'=>899.00,'stock'=>120,'cover'=>'https://images.unsplash.com/photo-1511367461989-f85a21fda167?w=800&auto=format&fit=crop'],
    ['id'=>4,'title'=>'智能手表','price'=>1299.00,'stock'=>80,'cover'=>'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=800&auto=format&fit=crop'],
    ['id'=>5,'title'=>'蓝牙音箱','price'=>499.00,'stock'=>200,'cover'=>'https://images.unsplash.com/photo-1519677100203-a0e668c92439?w=800&auto=format&fit=crop'],
    ['id'=>6,'title'=>'机械键盘','price'=>699.00,'stock'=>150,'cover'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'],
    ['id'=>7,'title'=>'游戏鼠标','price'=>399.00,'stock'=>180,'cover'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop'],
    ['id'=>8,'title'=>'显示器 27\"','price'=>1699.00,'stock'=>60,'cover'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop']
  ];
}

function load_products(){
  ensure_data_dir();
  $f=products_file();
  if(file_exists($f)){
    $json=@file_get_contents($f);
    $arr=json_decode($json,true);
    if(is_array($arr))return $arr;
  }
  $arr=default_products();
  @file_put_contents($f,json_encode($arr,JSON_UNESCAPED_UNICODE));
  return $arr;
}

function save_products($arr){
  ensure_data_dir();
  @file_put_contents(products_file(),json_encode($arr,JSON_UNESCAPED_UNICODE));
}

function next_product_id($arr){
  $max=0; foreach($arr as $p){ if(isset($p['id'])&&$p['id']>$max)$max=$p['id']; } return $max+1;
}

function get_users(){
  return [
    ['id'=>1,'username'=>'admin','password'=>'password','role'=>'admin'],
    ['id'=>2,'username'=>'testuser','password'=>'123456','role'=>'user']
  ];
}

function iplock_get($ip){
  ensure_data_dir();
  $f=iplock_file();
  if(file_exists($f)){
    $json=@file_get_contents($f);
    $arr=json_decode($json,true);
    if(isset($arr[$ip]))return $arr[$ip];
  }
  return ['fail'=>0,'lock_until'=>0];
}

function iplock_set($ip,$fail,$lock){
  ensure_data_dir();
  $f=iplock_file();
  $arr=[];
  if(file_exists($f)){
    $json=@file_get_contents($f);
    $tmp=json_decode($json,true);
    if(is_array($tmp))$arr=$tmp;
  }
  $arr[$ip]=['fail'=>intval($fail),'lock_until'=>intval($lock)];
  @file_put_contents($f,json_encode($arr,JSON_UNESCAPED_UNICODE));
}
