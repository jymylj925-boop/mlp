<?php
require_once __DIR__.'/../lib.php'; headers_secure(); session_start_safe();
$in=body_json(); $action=$in['action']??'';
if($action==='me') out(['ok'=>true,'user'=>current_user(),'csrf'=>csrf_make()]);
if($action==='logout') { csrf_check($in); $_SESSION=[]; session_regenerate_id(true); out(['ok'=>true]); }
rate_limit('auth',10,300);
if($action==='register') {
 $u=trim((string)($in['username']??'')); $p=(string)($in['password']??'');
 if(!preg_match('/^[\p{L}\p{N}_.-]{3,24}$/u',$u)||strlen($p)<10) out(['ok'=>false,'error'=>'نام کاربری ۳ تا ۲۴ نویسه و رمز حداقل ۱۰ کاراکتر باشد'],422);
 $users=store_read('users',[]);
 foreach($users as $x) if(mb_strtolower($x['username'])===mb_strtolower($u)) out(['ok'=>false,'error'=>'این نام کاربری قبلاً ثبت شده است'],409);
 $row=['id'=>bin2hex(random_bytes(16)),'username'=>$u,'password'=>password_hash($p,PASSWORD_DEFAULT),'created_at'=>gmdate('c')];
 $users[]=$row; store_write('users',$users); session_regenerate_id(true); $_SESSION['user_id']=$row['id']; $_SESSION['csrf']=bin2hex(random_bytes(32));
 out(['ok'=>true,'user'=>['id'=>$row['id'],'username'=>$u],'csrf'=>$_SESSION['csrf']]);
}
if($action==='login') {
 $u=trim((string)($in['username']??'')); $p=(string)($in['password']??''); $found=null;
 foreach(store_read('users',[]) as $x) if(mb_strtolower($x['username'])===mb_strtolower($u)){$found=$x;break;}
 if(!$found||!password_verify($p,$found['password'])) {usleep(300000);out(['ok'=>false,'error'=>'نام کاربری یا رمز اشتباه است'],401);}
 session_regenerate_id(true); $_SESSION['user_id']=$found['id']; $_SESSION['csrf']=bin2hex(random_bytes(32));
 out(['ok'=>true,'user'=>['id'=>$found['id'],'username'=>$found['username']],'csrf'=>$_SESSION['csrf']]);
}
out(['ok'=>false,'error'=>'عملیات نامعتبر'],400);
