<?php

namespace Passman;

class ConsoleHelper
{
    public function readline(string $msg): string
    {
        return (string) readline($msg);
    }


}