<?php

use Passman\ConsoleHelper;
use PHPUnit\Framework\TestCase;

class PasswordInputTest extends TestCase
{

    public function testGetPasswords()
    {
        /* @fixme extract data to dataProvider */
        $message = "message";
        $expectedPassword = "password";

        $consoleHelper = $this->createMock(ConsoleHelper::class);
        $consoleHelper
            ->method('readline')
            ->with($message)
            ->willReturn($expectedPassword);

        $actualPassword = (new \Passman\PasswordQuestionHelper(($consoleHelper ?? new \Passman\ConsoleHelper())))->ask($message);
        self::assertEquals($expectedPassword, $actualPassword, "Passwords are different");
    }

    public function dataProvider(): array
    {
        return [
        ];
    }

}