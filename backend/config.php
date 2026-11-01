<?php
declare(strict_types=1);
define('APP_SECRET', getenv('MRFILM_APP_SECRET') ?: 'REPLACE_WITH_RANDOM_64_HEX_CHARS_BEFORE_DEPLOYMENT');
define('DATA_DIR', __DIR__ . '/data');
define('SESSION_NAME', 'mrfilm_session');
define('APP_URL', 'https://ac.m.ihweb.ir');
if (!is_dir(DATA_DIR)) { mkdir(DATA_DIR, 0750, true); }
