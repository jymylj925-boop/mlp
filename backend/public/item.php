<?php
require_once __DIR__.'/../lib.php'; headers_secure(); rate_limit('item',120,60);
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT); $type=$_GET['type']??'movie';
if(!$id||!in_array($type,['movie','series'],true))out(['ok'=>false],400);
$items=store_read($type==='movie'?'movies':'series',[]);
foreach($items as $x) if((int)$x['id']===$id) {
 if($type==='series') $x['seasons']=array_values(array_filter(store_read('seasons',[]),fn($s)=>(int)$s['series_id']===$id));
 out(['ok'=>true,'item'=>$x]);
}
out(['ok'=>false,'error'=>'محتوا پیدا نشد'],404);
