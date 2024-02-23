<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

require_once __DIR__ . '/vendor/autoload.php';

const DS = DIRECTORY_SEPARATOR;

if (!defined('_VAR_DIR')) {
    define('_VAR_DIR', __DIR__ . DS.'var');
}


if (!defined('_LOG_DIR')) {
    define('_LOG_DIR', _VAR_DIR . DS. 'log');
}

if (!defined('_CACHE_PATH')) {
    define('_CACHE_PATH', _VAR_DIR. DS . 'cache');
}

if (!is_dir(_VAR_DIR)) {
    mkdir(_VAR_DIR);
}
if (!is_dir(_LOG_DIR)) {
    mkdir(_LOG_DIR);
}
if (!is_dir(_CACHE_PATH)) {
    mkdir(_CACHE_PATH);
}