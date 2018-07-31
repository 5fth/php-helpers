<?php
namespace Spiw\Helpers;

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
    
    
}