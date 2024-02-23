<?php

namespace infinityGame\Controller\Ollama;

use Exception;

class Client
{
    public static string $endpoint = IG_ENDPOINT;
    public static string $path = IG_PATH;
    public static string $model = IG_LANGUAGE_MODEL;

    public static bool $stream = false;
    public static string $lastPrompt;
    public static array $data = [];
    public static string $uri = '';
    public static mixed $lastResponse;

    public static mixed $context;


    public function __construct($options = [])
    {
        if (is_array($options) && count($options) > 0) {
            $this->setOptions($options);
        }
    }


    public function setOptions(array $options): void
    {
        if (isset($options['endpoint'])) {
            self::$path = $options['endpoint'];
        }
        if (isset($options['model'])) {
            self::$path = $options['model'];
        }
        if (isset($options['model'])) {
            self::$path = $options['model'];
        }
    }

    /**
     * @return mixed
     */
    public static function getContext()
    {
        if (!empty(self::$context)) {
            return self::$context;
        }

        return false;
    }

    public static function getModel(): string
    {
        return self::$model;
    }

    public static function getUri($method = ''): string
    {
        self::$uri = self::$endpoint . self::$path . $method;
        return self::$uri;
    }

    public function isAlive()
    {
        $endpoint = self::getUri('/generate');

        self::$data = [
            "model" => self::getModel(),
            "stream" => self::$stream,
        ];

        $this->send($endpoint);

        if (self::$lastResponse) {
            $response = json_decode(self::$lastResponse, 1);
            if (isset($response['done'])) {
                return true;
            }
        }

        return false;
    }

    public function send($endpoint)
    {
        $curl = curl_init($endpoint);

        curl_setopt($curl, CURLOPT_POST, 1); // Set the request method to POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(self::$data)); // Set the request data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

        self::$lastResponse = curl_exec($curl);

        curl_close($curl);
    }


    /**
     * @throws Exception
     */
    public function prompt($prompt): mixed
    {
        $endpoint = self::getUri('/generate');
        self::$lastPrompt = $prompt;

        self::$data = [
            "model" => self::getModel(), // Replace with your request data
            "prompt" => self::$lastPrompt,
            "stream" => self::$stream,
            "options" => [
                'seed' => 101,
                'temperature' => 0,
            ],
        ];

        $context = self::getContext();
        if (!empty($context)) {
            self::$data['context'] = $context;
        }

        $this->send($endpoint);

        $logFile = _LOG_DIR . DS . 'history_' . date('Y-m-d') . '.log';
        $logData = "[" . date('Y-m-d H:i:s') . "]\tPrompt:" . self::$lastPrompt . "\n" .
            "\tResponse:" . print_r(self::$lastResponse, 1) . "\n" .
            "\tData:" . print_r(self::$data, 1) . "\n" .
            "\n";

        file_put_contents($logFile, $logData, FILE_APPEND);

        if (!self::$lastResponse) {
            throw new Exception('ERROR RESPONSE' . print_r([
                    'lastPrompt' => self::$lastPrompt,
                    'uri' => self::$uri,
                    'data' => self::$data,
                    'response' => self::$lastResponse,
                ], 1), 1000);
        }

        self::$lastResponse = json_decode(self::$lastResponse, 1);

        if (!self::$lastResponse['response']) {
            throw new Exception('ERROR RESPONSE ' . print_r([
                    'lastPrompt' => self::$lastPrompt,
                    'uri' => self::$uri,
                    'data' => self::$data,
                    'response' => self::$lastResponse,
                ], 1), 1020);
        }

        return self::$lastResponse;
    }

    /**
     * @param $prompt
     * @return string
     * @throws Exception
     */
    public function generate($prompt): string
    {
        $response = $this->prompt($prompt);
        if ($response === false) {
            return false;
        }
        return $response['response'];
    }

    public function setContext(array $context): void
    {
        self::$context = $context;
    }


    public static function termsToKey($terms): string
    {

        return trim(strtolower(implode('-', $terms)));
    }

}