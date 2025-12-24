<?php
require __DIR__.'/../lib/config.php';
$q=null;
if(isset($_GET['enc'])){$raw=dec($_GET['enc']);$data=json_decode($raw,true);if($data&&isset($data['q'])){$q=$data['q'];}}
$all=load_products();
$items=[];
foreach($all as $p){
  if($q){
    if(stripos($p['title'],$q)===false)continue;
  }
  $items[]=['id'=>$p['id'],'title'=>$p['title'],'price'=>$p['price'],'cover'=>$p['cover']];
}
usort($items,function($a,$b){return $a['id']<=>$b['id'];});
j(['list'=>$items]);
