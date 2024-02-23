<?php

namespace infinityGame\Controller;

use InvalidArgumentException;

class FileHandler
{

    public static function removeDir($path): void
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("$path must be a directory");
        }
        if (substr($path, strlen($path) - 1, 1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $files = glob($path . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::removeDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }

   public static function formatBytes($bytes)
    {
        if ($bytes < 1024)
            return $bytes . ' B';
        else if ($bytes < 1048576)
            return round($bytes / 1024, 2) . ' KB';
        else if ($bytes < 1073741824)
            return round($bytes / 1048576, 2) . ' MB';
        else if ($bytes < 1099511627776)
            return round($bytes / 1073741824, 2) . ' GB';
        else return round($bytes / 1099511627776, 2) . ' TB';
    }

}