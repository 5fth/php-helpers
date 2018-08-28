<?php

namespace Spiw\Helpers;

use ReflectionFunction;

class Is
{

    public static function json($string)
    {
        if (is_object($string) || is_array($string) || is_null($string)) {
            return false;
        }
        return is_array(json_decode($string, true));
    }

    public static function cli()
    {
        return (php_sapi_name() === 'cli');
    }

    public static function laravel()
    {
        try {
            $reflFunc = new ReflectionFunction('env');
            $funcPath = $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();
            return stripos($funcPath, 'laravel') !== false;
        } catch (\ReflectionException $e) {
            return null;
        }
    }


}