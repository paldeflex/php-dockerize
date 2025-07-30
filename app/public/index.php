<?php

use Composer\Autoload\ClassLoader;

$serverSoftware = $_SERVER['SERVER_SOFTWARE'];
if (stripos($serverSoftware, 'nginx') === false) {
    exit("✘ Ошибка: скрипт не запущен под nginx (SERVER_SOFTWARE={$serverSoftware})");
}
echo "✔ Nginx: {$serverSoftware} <br>";

$requiredExtensions = ['pdo', 'pdo_mysql'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        exit("✘ Ошибка: требуется расширение PHP '{$ext}', оно не загружено. <br>");
    }
}
echo "✔ PHP версия: " . PHP_VERSION . "<br>";
echo "✔ Успешно загружены расширения PHP: " . implode(', ', $requiredExtensions);
echo "<br>";


$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    exit('✘ Ошибка: не найден файл vendor/autoload.php<br>');
}
$loader = require $autoload;
if (!($loader instanceof ClassLoader)) {
    exit('✘ Архитектура автозагрузки нарушена<br>');
}
echo '✔ Composer autoload работает! <br>';

if (!class_exists(\App\Test::class)) {
    exit('✘ Ошибка: класс App\Test не найден. Проверьте mapping в composer.json и структуру /src<br>');
}
echo '✔ PSR-4 автозагрузка App\Test: ' . \App\Test::ok() . '<br>';

if (!function_exists('dump')) {
    exit('✘ Ошибка: функция dump() из symfony/var-dumper не найдена.<br>');
}
echo '✔ symfony/var-dumper доступен:<br>';
dump(['status' => 'ok', 'time' => date('Y-m-d H:i:s')]);

$requiredEnv = ['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD'];
$missing = [];
foreach ($requiredEnv as $key) {
    $val = getenv($key);
    if ($val === false || $val === '') {
        $missing[] = $key;
    }
}
if ($missing) {
    exit('✘ Ошибка: не заданы переменные окружения: ' . implode(', ', $missing) . '<br>');
}
echo '✔ Переменные окружения заданы<br>';

$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_DATABASE');
$dbUser = getenv('DB_USERNAME');
$dbPass = getenv('DB_PASSWORD');

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
    $pdo = new \PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "✔ MySQL: соединение установлено (версия: {$version}) <br>";
} catch (\PDOException $e) {
    exit("✘ Ошибка MySQL: " . $e->getMessage() . '<br>');
}

if (!extension_loaded('xdebug')) {
    exit("✘ Ошибка: расширение Xdebug не загружено. Проверьте сборку образа и наличие pecl xdebug<br>");
}

if (!extension_loaded('xdebug')) {
    exit("✘ Ошибка: расширение Xdebug не загружено. Проверьте сборку образа и наличие pecl xdebug<br>");
}
$xdVersion = phpversion('xdebug') ?: 'unknown';
echo "✔ Xdebug загружен (версия: {$xdVersion})<br>";

$xMode = ini_get('xdebug.mode') ?: '';
if (stripos($xMode, 'debug') === false) {
    exit("✘ Ошибка: в xdebug.mode нет «debug». Текущий xdebug.mode={$xMode}<br>");
}
echo "✔ xdebug.mode = {$xMode}<br>";

$xStart = ini_get('xdebug.start_with_request');

if (!filter_var($xStart, FILTER_VALIDATE_BOOLEAN)) {
    exit("✘ Ошибка: xdebug.start_with_request должно быть включено (yes/on/1), текущее значение={$xStart}<br>");
}
echo "✔ xdebug.start_with_request = {$xStart}<br>";

$xHost = ini_get('xdebug.client_host') ?: '';
if (empty($xHost)) {
    exit("✘ Ошибка: xdebug.client_host не задан<br>");
}
echo "✔ xdebug.client_host = {$xHost}<br>";

if (!function_exists('xdebug_info')) {
    exit("✘ Ошибка: функция xdebug_info() не доступна. Проверьте, что режим developer включён<br>");
}

echo '✔ Функция xdebug_info() доступна, Xdebug работает корректно.<br>';

if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    exit('✘ PHPUnit не найден (класс PHPUnit\Framework\TestCase недоступен). Убедитесь, что вы установили phpunit/phpunit и запустили composer install без --no-dev.<br>');
}
echo '✔ PHPUnit доступен (TestCase загружен).<br>';

$testsDir = __DIR__ . '/../tests';
$testFiles = glob($testsDir . '/*Test.php');
if (!$testFiles) {
    exit("✘ В папке tests не найдено ни одного файла *Test.php (искомый путь: {$testsDir}).<br>");
}
echo '✔ Найдено ' . count($testFiles) . ' тестовых файлов:<br>';
foreach ($testFiles as $file) {
    echo '  – ' . basename($file) . '<br>';
}
