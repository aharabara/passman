<?php

namespace Passman;

class PasswordQuestionHelper
{
    private ConsoleHelper $helper;

    public function __construct(ConsoleHelper $helper)
    {
        $this->helper = $helper;
    }

    protected function hideInput(): void
    {
        // turn off echo
        `/bin/stty -echo`;
    }

    protected function showInput(): void
    {
        // turn echo back on
        `/bin/stty echo`;
    }

    public function ask(string $msg): string
    {
        $this->hideInput();
        $password = $this->helper->readline($msg);
        $this->showInput();

        $this->helper->writeln();
        if (empty($password)) {
            return $this->ask($msg);
        }
        return $password;
    }

}