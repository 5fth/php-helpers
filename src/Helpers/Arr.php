<?php

namespace Spiw\Helpers;

class Arr
{
    /**
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    public static function equals(array $arr1, array $arr2)
    {
        sort($arr1);
        sort($arr2);
        if (count($arr1) !== count($arr2)) {
            return false;
        }
        return json_encode($arr1) == json_encode($arr2);
    }
    
    
    /**
     * @param $main
     * @param $excludedData
     * @return array
     * Removes elements of $excludedData from $main
     * @example array_exclude([1,2,3], [2]) returns [1,3]
     */
    
    public static function exclude($main, $excludedData)
    {
        $result = [];
        
        foreach ($main as $elem) {
            if (!in_array($elem, $excludedData)) {
                $result[] = $elem;
            }
        }
        return $result;
    }
    
    
    /**
     * @param array $array
     * @return mixed
     */
    public static function end(array $array)
    {
        return array_values(array_slice($array, -1))[0];
    }
    
    public static function is_assoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    public static function mask(array $source, array $mask)
    {
        if (self::is_assoc($mask)) {
            return array_intersect_key($source, $mask);
        }
        
        return array_intersect_key($source, array_flip($mask));
    }
    
}