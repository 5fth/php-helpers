<?php

if(!function_exists("arr_equals")) {
    function arr_equals($arr1,$arr2) {
        return \Spiw\Helpers\Arr::equals($arr1,$arr2);
    }
}


if(!function_exists("arr_exclude")) {
    function arr_exclude($main,$excludedData) {
        return \Spiw\Helpers\Arr::exclude($main,$excludedData);
    }
}


if(!function_exists("arr_end")) {
    function arr_end($array) {
        return \Spiw\Helpers\Arr::end($array);
    }
}


if(!function_exists("arr_is_assoc")) {
    function arr_is_assoc($arr) {
        return \Spiw\Helpers\Arr::is_assoc($arr);
    }
}


if(!function_exists("arr_mask")) {
    function arr_mask($source,$mask) {
        return \Spiw\Helpers\Arr::mask($source,$mask);
    }
}


if(!function_exists("db_replace")) {
    function db_replace($config,$callback) {
        return \Spiw\Helpers\DB::replace($config,$callback);
    }
}


if(!function_exists("db_import_json")) {
    function db_import_json($config,$tableName,$json) {
        return \Spiw\Helpers\DB::importJson($config,$tableName,$json);
    }
}


if(!function_exists("db_import_sql")) {
    function db_import_sql($config,$filePath) {
        return \Spiw\Helpers\DB::importSql($config,$filePath);
    }
}


if(!function_exists("is_json")) {
    function is_json($string) {
        return \Spiw\Helpers\Is::json($string);
    }
}


if(!function_exists("is_cli")) {
    function is_cli() {
        return \Spiw\Helpers\Is::cli();
    }
}


if(!function_exists("is_laravel")) {
    function is_laravel() {
        return \Spiw\Helpers\Is::laravel();
    }
}


if(!function_exists("dd")) {
    function dd($arg) {
        return \Spiw\Helpers\Util::dd($arg);
    }
}


if(!function_exists("pd")) {
    function pd($arg) {
        return \Spiw\Helpers\Util::pd($arg);
    }
}


if(!function_exists("console_log")) {
    function console_log($arg) {
        return \Spiw\Helpers\Util::consoleLog($arg);
    }
}


if(!function_exists("load_env")) {
    function load_env() {
        return \Spiw\Helpers\Util::loadEnv();
    }
}


if(!function_exists("env")) {
    function env($key) {
        return \Spiw\Helpers\Util::env($key);
    }
}


if(!function_exists("app_path")) {
    function app_path() {
        return \Spiw\Helpers\Util::appPath();
    }
}


if(!function_exists("only_numbers")) {
    function only_numbers($str) {
        return \Spiw\Helpers\Util::onlyNumbers($str);
    }
}


if(!function_exists("get_item_from_object_path")) {
    function get_item_from_object_path($path,$data) {
        return \Spiw\Helpers\Util::getItemFromObjectPath($path,$data);
    }
}

if(!is_laravel()){load_env();}
