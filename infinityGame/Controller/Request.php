<?php

namespace infinityGame\Controller;

class Request
{
    public static function get($var, $default = NULL, $type = NULL)
    {

        $type = isset($type) ? strtoupper($type) : 'REQUEST';

        switch ($type) {
            default:
            case 'VAR':
                $var = $var ?? $default;
                break;
            case 'GET':
                $var = $_GET[$var] ?? $default;

                break;
            case 'REQUEST':
                $var = $_REQUEST[$var] ?? $default;
                break;
            case 'POST':
                $var = $_POST[$var] ?? $default;
                break;
        }

        return $var;
    }
}