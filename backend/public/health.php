<?php
require_once __DIR__.'/../lib.php'; headers_secure();
out(['ok'=>true,'service'=>'MR FILM','utc'=>gmdate('c')]);
