<?php
require_once __DIR__.'/../lib.php';headers_secure();admin_required();if($_SERVER['REQUEST_METHOD']!=='POST')exit;csrf_check($_POST);
$type=$_POST['type']??'';if(!in_array($type,['movie','series'],true))exit('نوع نامعتبر');
$title=trim((string)($_POST['title']??''));if($title===''||mb_strlen($title)>120)exit('عنوان نامعتبر');
$poster=trim((string)($_POST['poster']??''));$watch=trim((string)($_POST['watch_url']??''));
if(!valid_https_url($poster)||!valid_https_url($watch))exit('لینک باید HTTPS معتبر باشد');
$key=$type==='movie'?'movies':'series';$items=store_read($key,[]);$ids=array_map(fn($x)=>(int)$x['id'],$items);$id=$ids?max($ids)+1:1;
$items[]=['id'=>$id,'type'=>$type,'title'=>$title,'poster'=>$poster,'genre'=>mb_substr(trim((string)($_POST['genre']??'')),0,100),'year'=>(int)($_POST['year']??0),'description'=>mb_substr(trim((string)($_POST['description']??'')),0,3000),'watch_url'=>$watch,'created_at'=>gmdate('c')];
store_write($key,$items);header('Location: index.php');exit;
