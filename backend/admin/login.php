<?php
require_once __DIR__.'/../lib.php'; headers_secure(); session_start_safe(); $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 rate_limit('admin_login',6,300); $u=trim((string)($_POST['username']??''));$p=(string)($_POST['password']??'');$found=null;
 foreach(store_read('admins',[]) as $a)if(hash_equals((string)$a['username'],$u)&&password_verify($p,$a['password'])){$found=$a;break;}
 if($found){session_regenerate_id(true);$_SESSION['admin_id']=$found['id'];$_SESSION['csrf']=bin2hex(random_bytes(32));header('Location: index.php');exit;}
 usleep(400000);$err='اطلاعات ورود صحیح نیست.';
}
?><!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ورود مدیریت</title><style>body{font-family:tahoma;background:#0b1019;color:white;display:grid;place-items:center;min-height:100vh}.box{width:min(90%,380px);background:#182233;padding:25px;border-radius:16px}input,button{width:100%;padding:13px;margin:8px 0;box-sizing:border-box;border-radius:8px}input{background:#0d1522;color:white;border:1px solid #40506a}button{background:#ef315c;color:white;border:0}.err{color:#ff9aaa}</style><div class="box"><h2>ورود مدیر MR FILM</h2><p class="err"><?=esc($err)?></p><form method="post"><input name="username" required autocomplete="username" placeholder="نام کاربری"><input type="password" name="password" required autocomplete="current-password" placeholder="رمز عبور"><button>ورود</button></form></div></html>
