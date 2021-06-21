<?php

function get_password(string $fileWithPasswords, string $alias): string
{
    $passwords = get_passwords($fileWithPasswords, expect_password("Master password: "));
    if (!isset($passwords[$alias])) {
        die("This password does not exits.\n");
    }
    return $passwords[$alias];
}

function set_password(string $fileWithPasswords, string $alias): void
{
    $masterPass = expect_password("Master password: ");
    $passwords = get_passwords($fileWithPasswords, $masterPass);
    if (isset($passwords[$alias])) {
        if (!get_confirmation('This password already exist. Rewrite? [y/n] ')) {
            return;
        }
    }
    $passwords[$alias] = expect_password("Provide password for '$alias':");

    save_passwords($fileWithPasswords, $passwords, $masterPass);
}

function remove_password(string $fileWithPasswords, string $alias): void
{
    $masterPass = expect_password("Master password: ");
    $passwords = get_passwords($fileWithPasswords, $masterPass);
    if (isset($passwords[$alias])) {
        if (!get_confirmation("Are you sure you want to remove '{$alias}'? [y/n] ")) {
            return;
        }
    } else {
        die("There is no such password.\n");
    }
    unset($passwords[$alias]);
    save_passwords($fileWithPasswords, $passwords, $masterPass);
}

function save_passwords(string $fileWithPasswords, array $passwords, string $masterPass): void
{
    file_put_contents($fileWithPasswords, encrypt(json_encode($passwords), $masterPass));
}

function get_confirmation(string $msg): bool
{
    $response = strtolower(readline($msg));
    if ($response === 'y') return true;
    if ($response === 'n') return false;

    return get_confirmation($msg);
}

function get_aliases(string $fileWithPasswords): array
{
    return array_keys(get_passwords($fileWithPasswords, expect_password("Master password: ")));
}

function change_master_pass(string $fileWithPasswords): void
{
    $passwords = get_passwords($fileWithPasswords, expect_password("Master password: "));
    save_passwords($fileWithPasswords, $passwords, expect_password("Provide new master-password:"));
}


function get_passwords(string $fileWithPasswords, string $masterPass)
{
    $content = file_get_contents($fileWithPasswords);
    if (empty($content)) {
        return [];
    }
    $passwords = json_decode(decrypt($content, $masterPass), true);
    if ($passwords === null) {
        die("Wrong password.\n");
    }
    return $passwords;
}

# content = brown fox jumps over the lazy dog
# map     = testtesttesttesttesttesttesttestt
# result  =.s;af,lpwemeiasjdpqw;


function decrypt(string $content, string $key): string
{
    $decrypted = '';
    $map = str_pad('', strlen($content), $key);
    foreach (str_split($content) as $index => $symbol) {
        $decrypted .= chr(ord($symbol) - ord($map[$index]));
    }

    return $decrypted;
}

function encrypt(string $content, string $key): string
{
    $encrypted = '';
    $map = str_pad('', strlen($content), $key);
    foreach (str_split($content) as $index => $symbol) {
        $encrypted .= chr(ord($symbol) + ord($map[$index]));
    }

    return $encrypted;
}


function expect_password(string $msg): string
{
    // turn off echo
    `/bin/stty -echo`;
    $password = readline($msg);
    // turn echo back on
    `/bin/stty echo`;

    print "\n";

    if (empty($password)) {
        return expect_password($msg);
    }
    return $password;
}
