#!/usr/bin/env php
<?php
require __DIR__ . "/vendor/autoload.php";

$path = './.passman';

touch($path);
$passman = new Passman\Passman($path);

if ($argc < 2) {
    die($passman->getUsage());
}

# restricted to
# - show any password except `passamn get`
# - don't store plain password
//$fileWithPasswords = getenv('HOME') . '/.config/passman';


[$file, $command, $alias] = array_pad($argv, 3, '');

try {
    ($passman)->execute($command, $alias);
} catch (Throwable $e) {
    die($e->getMessage() . PHP_EOL);
}
