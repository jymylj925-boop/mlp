<?php
require_once __DIR__.'/../lib.php';
if(PHP_SAPI!=='cli'){http_response_code(403);exit("CLI only\n");}
$u=$argv[1]??'';$p=$argv[2]??'';
if(!preg_match('/^[a-zA-Z0-9_.-]{4,32}$/',$u)||strlen($p)<14){fwrite(STDERR,"username 4-32 chars; password min 14 chars\n");exit(1);}
$a=store_read('admins',[]);if($a){fwrite(STDERR,"Admin already exists.\n");exit(1);}
$a[]=['id'=>bin2hex(random_bytes(16)),'username'=>$u,'password'=>password_hash($p,PASSWORD_DEFAULT)];
store_write('admins',$a);echo "Admin created\n";
