<?php

namespace Passman;

class ConsoleHelper
{
    public function readline(string $msg): string
    {
        return (string)readline($msg);
    }

    function confirm(string $msg): bool
    {
        $response = strtolower($this->readline($msg));
        if ($response === 'y') return true;
        if ($response === 'n') return false;

        return $this->confirm($msg);
    }

    public function copy(string $value)
    {
        shell_exec("echo \"$value\" | xsel -ib");
    }

    public function writeln(string $content = ""): void
    {
        print $content . PHP_EOL;
    }


}