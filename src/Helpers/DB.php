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

    // TODO: add is_json validation
    public static function importJson($config, $tableName, $json, $sqlFile = null)
    {
        if (!Is::json($json)) {
            throw new \InvalidArgumentException('Json data is required!');
        }

        $conn = new PDO("mysql:host={$config['host']};dbname={$config['db']}", $config['username'], $config['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $columns = self::analyzeJson($json);

        if (!isset($config['drop_columns'])) {
            $config['drop_columns'] = true;
        }

        $sql = self::createSql($tableName, $columns, json_decode($json, true), $config['drop_columns']);

        if (!$sqlFile) {
            $sqlFile = tempnam(sys_get_temp_dir(), md5(microtime())) . '.sql';
        }
        file_put_contents($sqlFile, $sql);
        return self::importSql($config, $sqlFile);

    }

    public static function importSql($config, $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File does not exists!');
        }

        if (!isset($config['mysql'])) {
            $config['mysql'] = 'mysql';
        }

        $command = "${config['mysql']} --user={$config['username']} --password='{$config['password']}' "
            . "-h {$config['host']} -D {$config['db']} < ";

        return shell_exec($command . $filePath);
    }


    /**
     * @param $json
     * Example return ['name' => ['size' => 15,'value' => 'RU1153256005374', 'type' => 'string']
     * @return array
     */
    private static function analyzeJson($json)
    {
        if (!Is::json($json)) {
            throw new \InvalidArgumentException('Provided data is not json!');
        }

        $arr = json_decode($json, true);
        $keys = [];
        foreach ($arr as $item) {
            foreach ($item as $key => $value) {
                if (isset($keys[$key])) {
                    if (strlen($value) > $keys[$key]['size']) {
                        $keys[$key] = [];
                        $keys[$key]['size'] = strlen($value);
                        $keys[$key]['value'] = $value;
                        $keys[$key]['type'] = gettype($value);
                    }
                } else {
                    $keys[$key] = [];
                    $keys[$key]['size'] = strlen($value);
                    $keys[$key]['value'] = $value;
                    $keys[$key]['type'] = gettype($value);
                }
            }
        }
        return $keys;
    }

    private static function createSql($tableName, array $columns, $data = [], $dropColumns = true)
    {
        // Defining column not exists function.
        $sql = file_get_contents(dirname(__DIR__) . '/sql/drop_column_if_exists.sql') . PHP_EOL;

        // Creating table if not exists
        $sql .= "CREATE TABLE IF NOT EXISTS ${tableName} ( id INT AUTO_INCREMENT PRIMARY KEY);" . PHP_EOL;

        if ($dropColumns) {
            // Dropping columns
            foreach ($columns as $name => $column) {
                $sql .= "CALL drop_column_if_exists('${tableName}', '${name}');" . PHP_EOL;
            }
        }

//        // Setting +20 charachter tolerance range.
//        foreach ($columns as $name => $column) {
//            if ($column['type'] == 'string') {
//                $columns[$name]['size'] += 20;
//            }
//        }

        // Converting php types to mysql types

        foreach ($columns as $name => $column) {
            if ($column['type'] == 'string') {
                if ($column['size'] <= 255) {
                    $columns[$name]['type'] = 'VARCHAR';
                } elseif ($column['size'] <= 65535) {
                    $columns[$name]['type'] = 'TEXT';
                } elseif ($column['size'] <= 16777215) {
                    $columns[$name]['type'] = 'MEDIUMTEXT';
                } else {
                    $columns[$name]['type'] = 'LONGTEXT';
                }
            }
        }

        // Add column sql..

        foreach ($columns as $name => $column) {
            if ($name != 'id') {
                $sql .= "ALTER TABLE {$tableName} ADD ${name} ${column['type']}(${column['size']});" . PHP_EOL;
            }
        }

        // Insert Statements..

        foreach ($data as $item) {
            if (isset($item['id'])) {
                unset($item['id']);
            }

            foreach ($item as $key => $value) {
                $value = addslashes($value);
                $item[$key] = "'$value'";
            }

            $sql .= 'INSERT INTO ' . $tableName . ' (' . implode(',', array_keys($item)) . ') VALUES (' . implode(',', array_values($item)) . ');' . PHP_EOL;
        }


        return $sql;
    }


}