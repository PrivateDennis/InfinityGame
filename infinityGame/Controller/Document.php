<?php

namespace infinityGame\Controller;

use JetBrains\PhpStorm\NoReturn;

class Document
{


    public static string $title = 'Infinity Game';
    public static string $content = '';

    #[NoReturn] public static function toJson(array $array): void
    {
        @ob_end_clean();
        header('Content-type: application/json');
        die(json_encode($array));
    }

    public function start(): Document
    {
        ob_start();
        return $this;
    }

    public function end(): static
    {
        self::$content = ob_get_contents();
        ob_end_clean();
        return $this;
    }

    public static function renderDevContainer(array $array, $float = true)
    {
        $className = 'debug-container';
        if ($float === true) {
            $className = 'debug-container-fluid';
        }

        return '<div class="' . $className . '" style="z-index: 99999;">' .
            '<a data-toggle="collapse" href="#collapseDebugContainer" aria-expanded="false" aria-controls="collapseDebugContainer">' .
            '<i class="fe fe-cpu fe-24"></i>' .
            '</a>' .
            '<div class="collapse" id="collapseDebugContainer" ><hr/><div class="card card-body">' .
            '<pre style="max-width: 80vh;height: 80vh; overflow: auto; text-align: left">' .
            print_r($array, 1) . '</pre>' . __FILE__ . "::" . __LINE__ .
            '<a data-toggle="collapse" href="#collapseDebugContainer" aria-expanded="false" aria-controls="collapseDebugContainer">close</a>' .
            '</div></div></div>';
    }

    public function send()
    {
        $credits = $_ENV['APP_NAME'] . ' ' . $_ENV['APP_VERSION'] . ' (c) ' . date('Y') . ' Dennis Decker';
        $template = file_get_contents(__DIR__ . '/../Template/template.html');
        $html = str_replace('<!--[title]-->', self::$title, $template);
        $html = str_replace('<!--[content]-->', self::$content, $html);
        $html = str_replace('<!--[credits]-->', $credits, $html);


        ob_end_clean();
        die($html);
    }
}