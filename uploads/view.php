<?php
$name=isset($_POST['file'])?basename($_POST['file']):'';
if($name===''){die('no file');}
$path=__DIR__.'/'.$name;
if(!file_exists($path)){die('not found');}
$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
if(in_array($ext,['jpg','jpeg','png','gif','webp'])){include $path;exit;}
die('invalid');
