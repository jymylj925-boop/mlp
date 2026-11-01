<?php
require_once __DIR__.'/../lib.php'; headers_secure(); rate_limit('catalog',100,60);
$movies=store_read('movies',[]); $series=store_read('series',[]);
$q=mb_strtolower(trim((string)($_GET['q']??'')));
$filter=function($items)use($q){return array_values(array_filter($items,function($x)use($q){
 return $q==='' || mb_stripos((string)($x['title']??''),$q)!==false || mb_stripos((string)($x['genre']??''),$q)!==false;
}));};
out(['ok'=>true,'movies'=>$filter($movies),'series'=>$filter($series),'user'=>current_user(),'csrf'=>csrf_make()]);
