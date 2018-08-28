<?php
$vendor = 'Spiw';
$output_directory = 'src';
$output_file = __DIR__ . '/' . $output_directory . '/' . 'helpers.php';
$helpers_mask = __DIR__ . '/src/Helpers/*.php';

function create_func_string($funcName, $paremeters, $returning)
{
    $syntax = '
if(!function_exists("%s")) {
    function %s(%s) {
        return %s;
    }
}
';
    $parametersSyntanx = '';

    $returningSyntax = '%s(%s)';

    if ($paremeters) {
        foreach ($paremeters as $paremeter) {
            $parametersSyntanx .= '$' . $paremeter->getName() . ',';
        }
        $parametersSyntanx = preg_replace("/,$/", '', $parametersSyntanx);
    }

    return sprintf($syntax, $funcName, $funcName, $parametersSyntanx, sprintf($returningSyntax, $returning, $parametersSyntanx));
}

function create_file($functions)
{
    global $output_file;
    $file = fopen($output_file, "w") or die("Unable to open file!");
    fwrite($file, '<?php' . PHP_EOL);
    foreach ($functions as $function) {
        fwrite($file, $function . PHP_EOL);
    }
    fwrite($file, 'if(!is_laravel()){load_env();}' . PHP_EOL);
    fclose($file);
}

function class_name_from_path($path)
{
    $parts = explode('/', $path);
    return str_replace('.php', '', $parts[count($parts) - 1]);
}

$functionStrings = [];

foreach (glob($helpers_mask) as $filename) {
    require $filename;

    $className = class_name_from_path($filename);

    $relativePath = explode('src/', $filename);
    $relativePath = $relativePath[count($relativePath) - 1];
    $classPath = '\\' . $vendor . '\\' . str_replace('.php', '', str_replace('/', '\\', $relativePath));

    $methods = get_class_methods($classPath);

    if ($methods) {
        foreach ($methods as $method) {
            $functionName = strtolower($className) . '_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
            if ($className == 'Util') {
                $functionName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
            }

            $reflection = new ReflectionMethod($classPath . '::' . $method);
            $parameters = $reflection->getParameters();

            $functionStrings[] = create_func_string($functionName, $parameters, $classPath . '::' . $method);
        }
    }
}

create_file($functionStrings);