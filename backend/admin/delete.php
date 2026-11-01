<?php
require_once __DIR__.'/../lib.php';headers_secure();admin_required();if($_SERVER['REQUEST_METHOD']!=='POST')exit;csrf_check($_POST);
$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);$type=$_POST['type']??'';
if(!$id||!in_array($type,['movie','series'],true))exit('درخواست نامعتبر');
$key=$type==='movie'?'movies':'series';$items=store_read($key,[]);$items=array_values(array_filter($items,fn($x)=>(int)$x['id']!==$id));store_write($key,$items);
if($type==='series'){ $s=store_read('seasons',[]);store_write('seasons',array_values(array_filter($s,fn($x)=>(int)$x['series_id']!==$id))); }
header('Location: index.php');exit;
