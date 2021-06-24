#!/usr/bin/env php
<?php

use Passman\ConsoleHelper;

require __DIR__ . "/vendor/autoload.php";

$path = './.passman';

touch($path);
$console = new ConsoleHelper();
$passman = new Passman\Passman($path, $console);

# restricted to
# - show any password except `passaman get`
# - don't store plain password
//$fileWithPasswords = getenv('HOME') . '/.config/passman';


while ($content = readline("#>")) {
    try {
        $args = explode(" ", $content);
        [$command, $alias] = array_pad($args, 2, '');
        if (empty($command)) {
            continue;
        }
        ($passman)->execute($command, $alias);
    } catch (Throwable $e) {
        $console->error($e->getMessage());
    }
}
$console->writeln("Good bye.");

