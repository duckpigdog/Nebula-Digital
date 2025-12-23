<?php
require __DIR__.'/../lib/config.php';
if(!isset($_GET['enc'])){j(['ok'=>1,'msg'=>'活动在线']);}
$raw=dec($_GET['enc']);
$data=json_decode($raw,true);
if(!$data||!isset($data['action'])){j(['ok'=>1,'msg'=>'活动在线']);}
if($data['action']==='flash'){
  j(['ok'=>1,'msg'=>'活动在线']);
}
j(['ok'=>1,'msg'=>'活动在线']);
