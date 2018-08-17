<?php

namespace Spiw\Helpers;

class Util
{

    public static function dd($arg)
    {
        if (\Spiw\Helpers\Is::cli()) {
            var_dump($arg);
            die;
        }
        echo '<pre>';
        var_dump($arg);
        echo '</pre>';
        die;
    }

    public static function pd($arg)
    {
        if (Is::cli()) {
            print_r($arg);
            die;
        }
        echo '<pre>';
        print_r($arg);
        echo '</pre>';
        die;
    }

    public static function consoleLog($arg)
    {
        if (!Is::json($arg)) {
            $arg = json_encode($arg);
        }
        echo '<script>';
        echo 'console.log(' . $arg . ')';
        echo '</script>';
    }

    public static function loadEnv()
    {
        $Loader = new \josegonzalez\Dotenv\Loader(self::appPath() . '/.env');
        $Loader->parse();
        $Loader->toEnv();
    }

    public static function env($key)
    {
        return $_ENV[$key] ?? null;
    }

    public static function appPath()
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $vendorDir = dirname(dirname($reflection->getFileName()));
        return dirname($vendorDir);
    }

    public static function onlyNumbers($str)
    {
        return preg_replace('/[^0-9]/', '', $str);
    }


    public static function getItemFromObjectPath($path, $data)
    {
        foreach (explode('.', $path) as $key) {
            if (preg_match('/\[(\d+)\]/', $key, $matches)) {
                //it has a number index like [0]
                $data = $data[preg_replace('/\[(\d+)\]/', '', $key)][$matches[1]];
            } elseif (preg_match('/\[[\'"](.*)[\'"]\]/', $key, $matches)) {
                // it has string key ['hello world']
                $data = $data[preg_replace('/[[\'"](.*)[\'"]\]/', '', $key)][$matches[1]];
            } else {
                $data = $data[$key];
            }

        }
        return $data;
    }


}