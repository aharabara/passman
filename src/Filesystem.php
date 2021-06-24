<?php

namespace Passman;

class Filesystem
{

    public function get(string $filePath): string
    {
        return file_get_contents($filePath);
    }


    public function put(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content);
    }

}