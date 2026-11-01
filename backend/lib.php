<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function headers_secure(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cache-Control: no-store');
    header("Content-Security-Policy: default-src 'self' https: data:; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' https: data:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
function out(array $data, int $status=200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit;
}
function body_json(): array {
    $raw=file_get_contents('php://input'); $d=json_decode($raw ?: '{}',true);
    return is_array($d)?$d:[];
}
function store_read(string $name, array $default=[]): array {
    if (!preg_match('/^[a-z_]+$/',$name)) throw new RuntimeException('bad store');
    $p=DATA_DIR.'/'.$name.'.json'; if (!is_file($p)) return $default;
    $d=json_decode((string)file_get_contents($p),true); return is_array($d)?$d:$default;
}
function store_write(string $name,array $data): void {
    if (!preg_match('/^[a-z_]+$/',$name)) throw new RuntimeException('bad store');
    $p=DATA_DIR.'/'.$name.'.json'; $fp=fopen($p,'c+');
    if(!$fp) throw new RuntimeException('storage unavailable');
    try {
        if(!flock($fp,LOCK_EX)) throw new RuntimeException('storage busy');
        ftruncate($fp,0); rewind($fp);
        fwrite($fp,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        fflush($fp); flock($fp,LOCK_UN);
    } finally { fclose($fp); }
}
function session_start_safe(): void {
    if(session_status()===PHP_SESSION_ACTIVE) return;
    session_name(SESSION_NAME);
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']),
      'httponly'=>true,'samesite'=>'Lax']);
    ini_set('session.use_strict_mode','1'); ini_set('session.use_only_cookies','1');
    session_start();
}
function csrf_make(): string {
    session_start_safe(); if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(array $d): void {
    session_start_safe(); $v=$d['csrf']??($_SERVER['HTTP_X_CSRF_TOKEN']??'');
    if(!is_string($v)||!hash_equals($_SESSION['csrf']??'', $v)) out(['ok'=>false,'error'=>'درخواست نامعتبر است'],403);
}
function ip_hash(): string { return hash_hmac('sha256',$_SERVER['REMOTE_ADDR']??'unknown',APP_SECRET); }
function rate_limit(string $scope,int $limit=30,int $window=60): void {
    $data=store_read('rate_limits',[]); $key=hash('sha256',$scope.':'.ip_hash()); $now=time();
    $e=$data[$key]??['start'=>$now,'count'=>0];
    if($now-(int)$e['start'] >= $window) $e=['start'=>$now,'count'=>0];
    $e['count']++; $data[$key]=$e; store_write('rate_limits',$data);
    if($e['count']>$limit) out(['ok'=>false,'error'=>'درخواست‌های زیادی ارسال شده؛ کمی بعد تلاش کنید'],429);
}
function admin_required(): void {
    session_start_safe(); if(empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
}
function current_user(): ?array {
    session_start_safe(); $id=$_SESSION['user_id']??null; if(!$id)return null;
    foreach(store_read('users',[]) as $u) if(($u['id']??'')===$id) return ['id'=>$u['id'],'username'=>$u['username']];
    return null;
}
function valid_https_url(string $url): bool {
    if($url==='') return true;
    return filter_var($url,FILTER_VALIDATE_URL)!==false && strtolower((string)parse_url($url,PHP_URL_SCHEME))==='https';
}
function esc(string $s): string { return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
