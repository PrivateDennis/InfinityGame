<?php

namespace infinityGame\Components;

use infinityGame\Controller\Ollama\Client;
use infinityGame\Model\CacheModel;

class InfinityMaker
{
    public static array $trace = [];

    public static array $errors = [];

    public static bool $isNewItem = false;
    public static $clientLastResponse;
    public static bool $doesExist;

    /**
     * @param $terms
     * @return array
     */
    public static function sanitizeTerms($terms): array
    {
        if (!is_array($terms)) {
            /**
             * @debug STOP ++++++++++++++++++++++++++++++++++++
             * @todo REMOVE THIS DEBUGGER HERE !
             **/
            die('<pre>' . print_r([
                    '' => $_REQUEST,
                ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');
        }

        $terms = array_map('strtolower', array_map('trim', $terms));
        sort($terms);
        return $terms;
    }

    public function getCachedCraft(array $terms, $bypassCache = false)
    {
        $cacheModel = new CacheModel();

        $key = Client::termsToKey($terms);
        $cacheItem = $cacheModel->get($key);

        self::$isNewItem = false;

        self::$doesExist = $cacheItem->isHit();

        self::$trace[] = 'doesExist: ' . (self::$doesExist ? 'Yes' : 'NO');
        if($bypassCache){
            self::$trace[] = 'bypassCache';
        }


        if (!self::$doesExist || $bypassCache !== false) {
            self::$clientLastResponse = $this->craft($terms);

            // answer cam in like Combine Fire And Water = Plasma. Let's do it again!
            if (stripos(self::$clientLastResponse['response'], '=') !== false || stripos(self::$clientLastResponse['response'], ':') !== false) {
                self::$trace[] = 'wrong answer-format: ' . self::$clientLastResponse . ' lets do it again!';
                sleep(1);
                self::$clientLastResponse = self::$clientLastResponse = $this->craft($terms, true);
            }

            self::$trace[] = 'response: ' . self::$clientLastResponse['response'];

            $cacheItem = $cacheModel->write(self::$clientLastResponse['response'], CacheModel::toDate('1 years'));
            self::$isNewItem = true;
        }

        return $cacheItem->get();
    }

    public function craft(array $terms, $reminder = false)
    {
        $prompt = '';
        if ($reminder) {
            $prompt .= 'Remember only one word and one emoji response please! ';
        }

//        $prompt .= 'TASK: Combine ' . $terms[0] . ' and ' . $terms[1] . ' to create a new element.
//        Try to keep the element as simple and realistic as possible and only 1 word if possible as well.
//        If two basic elements are combined, you should prioritize making a new thing out of that, rather than simply combining the words.
//        Example: Earth + Earth = Solar System. You are allowed to use one of the inputs as the output, but only if there are no realistic elements.
//        Two of the same item should output a larger version of that item if applicable.
//        Your response should be the name of the new element and MUST contain one and only one emoji to represent the element.
//        The response should never have less than or more than 1 emoji. Example: Combine Fire and Water = Steam;💨.
//        Your output should be in format: name;emoji';


        $prompt .= 'Create new element from: ' . $terms[0] . ' + ' . $terms[1].'. Remember, only respond with 1 word and 1 emoji in format: new_element;emoji';
        self::$trace[] = 'prompt: ' . $prompt;

        $client = new Client();

        $results = null;
        try {
            $results = $client->prompt($prompt);
        } catch (\Exception $e) {
            die('<pre>' . print_r([
                    'ERROR' => $e->getMessage(),
                    'results' => $results,
                    'prompt' => $prompt,
                    'terms' => $terms,
                ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');
        }

        if (empty($results) || !isset($results['response']) || !isset($results['done']) || !$results['done']) {
            die('<pre>' . print_r([
                    'ERROR' => 'no response or not done',
                    'results' => $results,
                    'prompt' => $prompt,
                    'terms' => $terms,
                ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>');
        }
        return $results;
    }
}