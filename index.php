<?php

if ($argc < 2){
    die(<<<USAGE
Usage:
    passman set <alias>
    passman get <alias>
    #passman copy <alias>
    #passman remove <alias>
    #passman change-masterpass
USAGE
);
}

[$file, $command, $alias] = array_pad($argv, 3, '');

$fileWithPasswords = getenv('HOME') . '/.config/passman';
if (!file_exists($fileWithPasswords)){
    touch($fileWithPasswords);
}

switch ($command){
    case 'set':
        set_password($fileWithPasswords, $alias);
    die;
    case 'get':
        print "Passowrd:" . get_password($fileWithPasswords, $alias)."\n";
    die;
    case 'list':
        $aliases = get_aliases($fileWithPasswords);
        foreach ($aliases as $alias){
            print " - $alias\n";
        }
        die;
    case 'copy':
//        get_password($fileWithPasswords, $alias);
    die;
    case 'remove':
//        get_password($fileWithPasswords, $alias);
    die;
}


function get_aliases(string $fileWithPasswords): array
{
    return array_keys(get_passwords($fileWithPasswords, expect_password("Master password: ")));
}

function set_password(string $fileWithPasswords, string $alias): void
{
    $masterPass = expect_password("Master password: ");
    $passwords = get_passwords($fileWithPasswords, $masterPass);
    $passwords[$alias] = expect_password("Provide password for '$alias':");
    file_put_contents($fileWithPasswords, encrypt(json_encode($passwords), $masterPass));
}

function expect_password(string $msg): string
{
    // turn off echo
    `/bin/stty -echo`;
    $password = readline($msg);
    // turn echo back on
    `/bin/stty echo`;

    print "\n";

    if (empty($password)){
        return expect_password($msg);
    }
    return $password;
}

function decrypt(string $content, string $key):string{
    $decrypted = '';
    $map = str_pad('', strlen($content), $key);
    foreach (str_split($content) as $index => $symbol) {
        $decrypted .= chr(ord($symbol) - ord($map[$index]));
    }

    return $decrypted;
}

function encrypt(string $content, string $key):string{
    $encrypted = '';
    $map = str_pad('', strlen($content), $key);
    foreach (str_split($content) as $index => $symbol) {
        $encrypted .= chr(ord($symbol) + ord($map[$index]));
    }

    return $encrypted;
}