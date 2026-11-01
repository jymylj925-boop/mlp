<?php
require_once __DIR__.'/../lib.php'; headers_secure(); session_start_safe();
$user=current_user(); if(!$user) out(['ok'=>false,'error'=>'ابتدا وارد حساب شوید'],401);
$in=body_json(); csrf_check($in); $f=store_read('favorites',[]);
if(($_SERVER['REQUEST_METHOD']??'GET')==='GET') {
 $ids=$f[$user['id']]??[]; out(['ok'=>true,'ids'=>$ids]);
}
if(($_SERVER['REQUEST_METHOD']??'')!=='POST') out(['ok'=>false],405);
$id=filter_var($in['id']??null,FILTER_VALIDATE_INT); $type=$in['type']??'movie';
if(!$id||!in_array($type,['movie','series'],true)) out(['ok'=>false,'error'=>'ورودی نامعتبر'],422);
$key=$type.':'.$id; $list=$f[$user['id']]??[];
if(in_array($key,$list,true)) $list=array_values(array_diff($list,[$key])); else $list[]=$key;
$f[$user['id']]=$list; store_write('favorites',$f); out(['ok'=>true,'ids'=>$list]);
