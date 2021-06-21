<?php

function get_password(string $fileWithPasswords, string $alias): string
{
    return get_passwords($fileWithPasswords, expect_password("Master password: "))[$alias];
}

function get_passwords(string $fileWithPasswords, string $masterPass)
{
    $content = file_get_contents($fileWithPasswords);
    if (empty($content)){
        return [];
    }
    $passwords = json_decode(decrypt($content, $masterPass), true);
    if ($passwords === null){
        die("Wrong password.\n");
    }
    return $passwords;
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