<?php

namespace infinityGame\Components;

use infinityGame\Controller\Ollama\Client;
use infinityGame\Model\CacheModel;

class InfinityMaker
{
    public static array $trace = [];

    public static array $errors = [];
    public static $clientLastResponse;

    public function getNewTerm(array $terms, $bypassCache = false, $reminder = false)
    {
        $prompt = '';
        if ($reminder) {
            $prompt .= 'Only one word response please! ';
        }
        $prompt .= 'You are a game master. You can answer with only one word no emojis! ' .
            implode(' and ', $terms) . ' equals? ';

        self::$trace[] = 'Prompt: ' . $prompt;

        $cacheModel = new CacheModel();
        $cacheItem = $cacheModel->get('terms-' . implode('-', $terms));

        $createdNew = false;
        $doesExist = $cacheItem->isHit();

        self::$trace[] = 'doesExist: ' . ($doesExist ? 'Yes' : 'NO');
        self::$trace[] = 'bypassCache: ' . ($bypassCache ? 'Yes' : 'NO');

        if (!$doesExist && $bypassCache === false) {
            $cacheItem = $cacheModel->get('terms-' . implode('-', array_reverse($terms)));
            $doesExist = $cacheItem->isHit();
        }

        if (!$doesExist || $bypassCache !== false) {

            $client = new Client();
            if ($reminder && !empty(self::$clientLastResponse) && isset(self::$clientLastResponse['context'])) {
                $client::$context = self::$clientLastResponse['context'];
                self::$trace[] = 'setting context total: ' . count(self::$clientLastResponse['context']);
            }

            try {
                $response = ucfirst(trim($client->generate($prompt)));
                self::$trace[] = 'response: ' . $response;
                self::$clientLastResponse = Client::$lastResponse;

            } catch (\Exception $e) {
                $response = 'Universe';
                self::$errors[] = $e->getMessage();
            }

//            try {
//                $client = new Client();
//                if (isset($lastResponse['context'])) {
//                    $client->setContext($lastResponse['context']);
//                }
//
//                $origAnswerEmoji = trim($client->generate('Find the closest matching emoji to ' . $response . '. Only answer with one single emoji'));
//                $response .= ' ' . $origAnswerEmoji;
//            } catch (\Exception $e) {
//                $response .= '🌌';
//                $errors[] = $e->getMessage();
//            }
//
            $cacheItem = $cacheModel->write($response, CacheModel::toDate('1 years'));
//
            $reverseCacheModel = new CacheModel();
            $cacheItemReverse = $reverseCacheModel->get('terms-' . implode('-', array_reverse($terms)));
            $cacheItemReverse = $reverseCacheModel->write($response, CacheModel::toDate('1 years'));

            $createdNew = true;

            $logFile = _LOG_DIR . '/creation_' . date('Y') . '.log';
            $logData = "[" . date('Y-m-d H:i:s') . "]\t" . ($bypassCache !== false ? "recreated: " : "created: ") . $response . " from " . $terms[0] . " + " . $terms[1] . "\n";

            file_put_contents($logFile, $logData, FILE_APPEND);
        }

        self::$trace[] = 'createdNew: ' . ($createdNew ? 'Yes' : 'NO');

        return $cacheItem->get();
    }

    public function depricatedRemindAgain(array $terms, $bypassCache = false)
    {
        $prompt = 'only one word please!';

        self::$trace[] = 'Prompt: ' . $prompt;

        $cacheModel = new CacheModel();
        $cacheItem = $cacheModel->get('terms-' . implode('-', $terms));

        $createdNew = false;
        $doesExist = $cacheItem->isHit();

        self::$trace[] = 'doesExist: ' . ($doesExist ? 'Yes' : 'NO');
        self::$trace[] = 'bypassCache: ' . ($bypassCache ? 'Yes' : 'NO');

        if (!$doesExist && $bypassCache === false) {
            $cacheItem = $cacheModel->get('terms-' . implode('-', array_reverse($terms)));
            $doesExist = $cacheItem->isHit();
        }

        if (!$doesExist || $bypassCache !== false) {

            $client = new Client();

            if (!empty(self::$clientLastResponse) && isset(self::$clientLastResponse['context'])) {
                $client::$context = self::$clientLastResponse['context'];
                self::$trace[] = 'setting context total: ' . count(self::$clientLastResponse['context']);
            }

            try {
                $response = ucfirst(trim($client->generate($prompt)));
                self::$trace[] = 'response: ' . $response;
                self::$clientLastResponse = Client::$lastResponse;

            } catch (\Exception $e) {
                $response = 'Universe';
                self::$errors[] = $e->getMessage();
            }

            $cacheItem = $cacheModel->write($response, CacheModel::toDate('1 years'));

            $reverseCacheModel = new CacheModel();
            $cacheItemReverse = $reverseCacheModel->get('terms-' . implode('-', array_reverse($terms)));
            $cacheItemReverse = $reverseCacheModel->write($response, CacheModel::toDate('1 years'));

            $createdNew = true;

            $logFile = _LOG_DIR . '/creation_' . date('Y') . '.log';
            $logData = "[" . date('Y-m-d H:i:s') . "]\t" . ($bypassCache !== false ? "recreated: " : "created: ") . $response . " from " . $terms[0] . " + " . $terms[1] . "\n";

            file_put_contents($logFile, $logData, FILE_APPEND);
        }

        self::$trace[] = 'createdNew: ' . ($createdNew ? 'Yes' : 'NO');

        return $cacheItem->get();
    }

    public function getEmoji(string $term, $bypassCache = false)
    {
        $prompt = 'Find the closest matching emoji to ' . $term . '. Only answer with one single emoji';
        self::$trace[] = 'Prompt: ' . $prompt;

        $cacheModel = new CacheModel();
        $cacheItem = $cacheModel->get('emoji-' . $term);

        $createdNew = false;
        $doesExist = $cacheItem->isHit();

        self::$trace[] = 'doesExist: ' . ($doesExist ? 'Yes' : 'NO');
        self::$trace[] = 'bypassCache: ' . ($bypassCache ? 'Yes' : 'NO');


        if (!$doesExist || $bypassCache !== false) {

            $client = new Client();
            if (!empty(self::$clientLastResponse) && isset(self::$clientLastResponse['context'])) {
                $client::$context = self::$clientLastResponse['context'];
                self::$trace[] = 'setting context total: ' . count(self::$clientLastResponse['context']);
            }

            try {
                $response = ucfirst(trim($client->generate($prompt)));

                self::$trace[] = 'response: ' . $response;
                self::$clientLastResponse = Client::$lastResponse;

            } catch (\Exception $e) {
                $response = 'Universe';
                self::$errors[] = $e->getMessage();
            }

            $cacheItem = $cacheModel->write($response, CacheModel::toDate('1 years'));

            $createdNew = true;
            $logFile = _LOG_DIR . '/creation_' . date('Y') . '.log';
            $logData = "[" . date('Y-m-d H:i:s') . "]\t" . ($bypassCache !== false ? "recreated: " : "created: ") . ' emoji ' . $response . " from " . $term . "\n";

            file_put_contents($logFile, $logData, FILE_APPEND);
        }

        self::$trace[] = 'createdNew: ' . ($createdNew ? 'Yes' : 'NO');

        return $cacheItem->get();
    }

    /**
     * @param array $terms
     * @param bool $bypassCache
     * @return array
     */
    public function resolve(array $terms, bool $bypassCache = false): array
    {
        $term = $this->getNewTerm($terms, $bypassCache);
        $parts = explode(' ', $term);
        if (count($parts) > 1) {
            self::$trace[] = 'multipart answer: ' . $term;

            $term = $this->getNewTerm($terms, $bypassCache = false, $remind = true);
            $parts = explode(' ', $term);
            self::$trace[] = 'reminder response: ' . $term;

            if (count($parts) > 1) {
                self::$errors[] = 'AI cant follow my rules. Too, bad!';
                return false;
            }
//            $term = $this->remindAgain($terms, true);
        }

        $emoji = $this->getEmoji($term, $bypassCache);

        return [
            'terms' => $terms,
            'response' => ucfirst($term),
            'icon' => $emoji,
            'trace' => self::$trace,
            'errors' => self::$errors,
        ];
    }
}