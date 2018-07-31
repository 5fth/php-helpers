<?php

namespace Spiw\Helpers;

use PDO;

class DB
{
    public static function replace($config, $callback)
    {
        $conn = new PDO("mysql:host={$config['host']};dbname={$config['db']}", $config['username'], $config['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = $conn->query('SHOW TABLES');
        $tables = $query->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            
            $query = $conn->query("SELECT * FROM ${table}");
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            
            if ($rows) {
                $query = $conn->query("SHOW KEYS FROM ${table} WHERE Key_name = 'PRIMARY'");
                $primary = $query->fetch(PDO::FETCH_ASSOC)['Column_name'];
                
                foreach ($rows as $row) {
                    
                    foreach ($row as $key => $data) {
                        if ($key != $primary) {
                            $updatedData = $callback($data);
                            if ($updatedData != $data) {
                                $prepared = $conn->prepare("UPDATE ${table} SET ${key}='?' WHERE ${primary}=${row[$primary]}");
                                $prepared->execute([$updatedData]);
                            }
                        }
                        
                    }
                }
            }
            
        }
    }
    
    
}