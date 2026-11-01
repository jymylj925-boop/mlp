<?php
require_once __DIR__.'/../lib.php';headers_secure();admin_required();if($_SERVER['REQUEST_METHOD']!=='POST')exit;csrf_check($_POST);
$sid=filter_var($_POST['series_id']??null,FILTER_VALIDATE_INT);$sn=filter_var($_POST['season_no']??null,FILTER_VALIDATE_INT);$en=filter_var($_POST['episode_no']??null,FILTER_VALIDATE_INT);
$title=trim((string)($_POST['title']??''));$url=trim((string)($_POST['watch_url']??''));
$series=store_read('series',[]);$exists=false;foreach($series as $s)if((int)$s['id']===$sid)$exists=true;
if(!$sid||!$sn||!$en||!$exists||$title===''||!valid_https_url($url))exit('اطلاعات فصل/قسمت نامعتبر است');
$a=store_read('seasons',[]);$a[]=['id'=>bin2hex(random_bytes(8)),'series_id'=>$sid,'season_no'=>$sn,'episode_no'=>$en,'title'=>$title,'watch_url'=>$url];store_write('seasons',$a);header('Location: index.php');exit;
