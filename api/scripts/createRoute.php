<?php
namespace Api\Scripts;
use Api\Routes;
use Api\Core\RouteFactory;

require_once('../vendor/autoload.php');

$line = "";

//$argv[1] can contains class name if passed launching script with "php crateRoute.php"
$handle = fopen ('php://stdin','r');
if( !isset($argv[1]) ) {
    echo 'Insert route name: ';
    $line = fgets($handle);
} else {
    $line = $argv[1];
}

$line = preg_replace('/\s+/', '', $line);

while($line  === '') {
    echo 'ERROR: Name non valid' . PHP_EOL;
    echo 'Insert route name: ';
    $line = fgets($handle);
    $line = preg_replace('/\s+/', '', $line);
}

fclose($handle);

echo PHP_EOL;

$className = ucfirst($line);
$filePath = __DIR__.'/../src/routes/';
$fileName = "{$className}.php";

if( !file_exists($filePath . $fileName) && !class_exists("Api\Routes\{$className}") ) {
    $newClassContents = RouteFactory::defineClass($className/*, $implements*/);

    $newFile = fopen($filePath . $fileName, 'w');
    fwrite($newFile, '<?php' . PHP_EOL . $newClassContents);
    fclose($newFile);
    echo "SUCCESS: Class {$className} created in 'routes' folder" . PHP_EOL;
}
else{
    echo "ERROR: Class {$className} already exists" . PHP_EOL;
}


