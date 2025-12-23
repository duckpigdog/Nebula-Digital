<?php
require __DIR__.'/../lib/config.php';
$q=null;
if(isset($_GET['enc'])){$raw=dec($_GET['enc']);$data=json_decode($raw,true);if($data&&isset($data['q'])){$q=$data['q'];}}
if($q){$stmt=$mysqli->prepare("SELECT id,title,price,cover FROM products WHERE title LIKE CONCAT('%',?,'%') ORDER BY id ASC");$stmt->bind_param('s',$q);$stmt->execute();$res=$stmt->get_result();}
else{$res=$mysqli->query("SELECT id,title,price,cover FROM products ORDER BY id ASC");}
$items=[];while($row=$res->fetch_assoc()){$items[]=$row;}j(['list'=>$items]);
