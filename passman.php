#!/usr/bin/env php
<?php
require __DIR__ . "/vendor/autoload.php";

if ($argc < 2) {
    die(get_usage());
}

# restricted to
# - show any password except `passamn get`
# - don't store plain password

[$file, $command, $alias] = array_pad($argv, 3, '');

//$fileWithPasswords = getenv('HOME') . '/.config/passman';

execute($command, $alias);
