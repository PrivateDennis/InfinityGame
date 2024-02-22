<?php

use Dotenv\Dotenv;
use infinityGame\Components\InfinityMaker;
use infinityGame\Controller\Document;
use infinityGame\Controller\Ollama\Client;
use infinityGame\Controller\Request;

require_once __DIR__ . '/bootstrap.php';


$dotenv = Dotenv::createImmutable('./', '.config');
$dotenv->load();

define('IG_ENDPOINT', $_ENV['IG_ENDPOINT']);
define('IG_PATH', $_ENV['IG_PATH']);
define('IG_LANGUAGE_MODEL', $_ENV['IG_LANGUAGE_MODEL']);

$do = Request::get('do');
$terms = Request::get('terms');

$bypassCache = (Request::get('bypassCache', false) === 'true');

if (!empty($do)) {
    $infinityMaker = new InfinityMaker();
    $response = $infinityMaker->resolve($terms, $bypassCache);
    if (!$response) {
        Document::toJson(['success' => false, 'error' => InfinityMaker::$errors, 'trace' => InfinityMaker::$trace]);
    }
    Document::toJson($response);
}

$prompt = Request::get('prompt');
$response = 'Ask me!';

if (!empty($prompt)) {
    $client = new Client();
    try {
        $response = $client->generate($prompt);
    } catch (Exception $e) {
        $response = $e->getMessage();
    }
    Document::toJson(['success' => true, 'response' => $response]);
}


require_once __DIR__ . '/infinityGame/View/main.php';

