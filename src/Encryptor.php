<?php

namespace Passman;

class Encryptor
{
    private string $masterPass;

    public function __construct(string $masterPass)
    {
        $this->masterPass = $masterPass;
    }

    function decrypt(string $content): string
    {
        $decrypted = '';
        $map = str_pad('', strlen($content), $this->masterPass);
        foreach (str_split($content) as $index => $symbol) {
            $decrypted .= chr(ord($symbol) - ord($map[$index]));
        }

        return $decrypted;
    }

    function encrypt(string $content): string
    {
        $encrypted = '';
        $map = str_pad('', strlen($content), $this->masterPass);
        foreach (str_split($content) as $index => $symbol) {
            $encrypted .= chr(ord($symbol) + ord($map[$index]));
        }

        return $encrypted;
    }
}