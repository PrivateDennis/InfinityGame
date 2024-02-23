<?php

use Dotenv\Dotenv;
use infinityGame\Components\InfinityMaker;
use infinityGame\Controller\Document;
use infinityGame\Controller\FileHandler;
use infinityGame\Controller\Ollama\Client;
use infinityGame\Controller\Request;

require_once __DIR__ . '/bootstrap.php';

// create your own config file. copy .config to .my_config and tweak as you need
$configFile = file_exists('.my_config') ? '.my_config' : '.config';

$dotenv = Dotenv::createImmutable('./', $configFile);
$dotenv->load();

define('IG_ENDPOINT', $_ENV['IG_ENDPOINT']);
define('IG_PATH', $_ENV['IG_PATH']);
define('IG_LANGUAGE_MODEL', $_ENV['IG_LANGUAGE_MODEL']);

$do = Request::get('do');
$terms = Request::get('terms');
$prompt = Request::get('prompt');

$bypassCache = (Request::get('bypassCache', false) === 'true');

$response = 'Ask me!';

if (!empty($do)) {

    switch ($do) {

        default:
            break;

        case 'saveSettings':
            $configFile = __DIR__ . DS . '.my_config';

            $appName = Request::get('APP_NAME', $_ENV['APP_NAME'], 'POST');
            $appVersion = Request::get('APP_VERSION', $_ENV['APP_VERSION'], 'POST');
            $apiEndpoint = Request::get('IG_ENDPOINT', $_ENV['IG_ENDPOINT'], 'POST');
            $appPath = Request::get('IG_PATH', $_ENV['IG_PATH'], 'POST');
            $appModel = Request::get('IG_LANGUAGE_MODEL', $_ENV['IG_LANGUAGE_MODEL'], 'POST');

            if (file_exists($configFile)) {
                $success = rename($configFile, str_replace('.my', '.old_my', $configFile));
            }

            $success = file_put_contents($configFile, "APP_NAME=\"" . $appName . "\"\n" .
                "APP_VERSION=\"" . $appVersion . "\"\n" .
                "IG_ENDPOINT=\"" . $apiEndpoint . "\"\n" .
                "IG_PATH=\"" . $appPath . "\"\n" .
                "IG_LANGUAGE_MODEL=\"" . $appModel . "\"\n");

            header('location: ./?do=settings#' . ($success === false ? '#settings-saved-failed' : '#settings-saved-successfully'));
            exit;

        case 'settings':
            require_once __DIR__ . '/infinityGame/View/settings.php';
            break;

        case 'show':
            $client = new Client();
            $endpoint = Client::getUri('/tags');
            $modelList = json_decode(file_get_contents($endpoint));
            /**
             * @debug STOP ++++++++++++++++++++++++++++++++++++
             * @todo REMOVE THIS DEBUGGER HERE !
             **/
            die('<pre>' . print_r($modelList, 1) . __FILE__ . ' ' . __LINE__ . '</pre>');

            $client->send($endpoint);
            $modelList = json_decode(Client::$lastResponse, true);
            /**
             * @debug STOP ++++++++++++++++++++++++++++++++++++
             * @todo REMOVE THIS DEBUGGER HERE !
             **/
            die('<pre>' . print_r([
                    'Client::$lastResponse' => Client::$lastResponse,
                    '$modelList' => $modelList,
                ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');

            $endpoint = Client::getUri('/show');
            Client::$data = [
                "name" => Client::getModel(), // Replace with your request data
            ];

            $client->send($endpoint);
            $modelFileInfo = json_decode(Client::$lastResponse, true);
            unset($modelFileInfo['license']);
            /**
             * @debug STOP ++++++++++++++++++++++++++++++++++++
             * @todo REMOVE THIS DEBUGGER HERE !
             **/
            die('<pre>' . print_r([
                    '$modelFileInfo' => $modelFileInfo,
                ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');
            break;

        case 'prompt':
            $client = new Client();
            try {
                $response = $client->generate($prompt);
            } catch (Exception $e) {
                $response = $e->getMessage();
            }
            Document::toJson(['success' => true, 'response' => $response]);
            break;


        case 'reset-all':
            FileHandler::removeDir(_CACHE_PATH);
            FileHandler::removeDir(_LOG_DIR);
            header('location: ./#cleared');
            exit;

        case 'reset-cache':
            FileHandler::removeDir(_CACHE_PATH);
            header('location: ./#cache-deleted');
            exit;

        case 'reset-logs':
            FileHandler::removeDir(_LOG_DIR);
            header('location: ./#logs-deleted');
            exit;

        case 'askGod':
            $infinityMaker = new InfinityMaker();
            $cleanTerms = InfinityMaker::sanitizeTerms($terms);
            $response = $infinityMaker->getCachedCraft($cleanTerms, $bypassCache);

            list($newElement, $emoji) = explode(';', $response);

            $results = [
                'response' => trim($newElement),
                'icon' => trim($emoji),
                'new' => InfinityMaker::$isNewItem,
                'trace' => InfinityMaker::$trace,
            ];

            if ($results['new']) {
                $logFile = _LOG_DIR . DS . 'creation_' . date('Y') . '.log';
                $logData = "[" . date('Y-m-d H:i:s') . "]\t" .
                    ($bypassCache !== false ? "recreated: " : "created: ") . $results['new_element'] . " from " . $terms[0] . " + " . $terms[1] . "\n";

                file_put_contents($logFile, $logData, FILE_APPEND);
            }

            Document::toJson($results);
    }
}
require_once __DIR__ . '/infinityGame/View/main.php';

