<?php

namespace Passman;

use LogicException;

class Passman
{
    private ConsoleHelper $consoleHelper;
    private PasswordQuestionHelper $passHelper;
    private EncryptedLoader $encryptedLoader;

    public function __construct(string $fileWithPasswords)
    {
        $this->consoleHelper = new ConsoleHelper();
        $this->passHelper = new PasswordQuestionHelper($this->consoleHelper);

        $this->encryptedLoader = $this->createEncryptedLoader($fileWithPasswords, "Master password:");
    }

    public function execute(string $command, string $alias): void
    {
        $console = $this->consoleHelper;
        switch ($command) {
            case 'set':
                $this->setPassword($alias);
                break;
            case 'remove':
                $this->removePassword($alias);
                break;
            case 'change-masterpass':
                $this->changeMasterPassword();
                die("Done.\n");

            case 'get':
                $pass = $this->getPassword($alias);
                $console->writeln("Password: " . $pass);
                break;
            case 'list':
                $aliases = $this->getAliases();
                $console->writeln("Available passwords in the vault:");
                foreach ($aliases as $alias) {
                    $console->writeln(" - $alias");
                }

                break;

            case 'copy':
                $console->copy($this->getPassword($alias));
                $console->writeln("Copied.");
                break;
            default:
                throw new LogicException("No such command.");
        }
    }

    function getUsage(): string
    {
        return <<<USAGE
Usage:
    passman set <alias>
    passman get <alias>
    passman list
    passman copy <alias>
    passman remove <alias>
    #passman change-masterpass

USAGE;
    }

    protected function getAliases(): array
    {
        $passwords = $this->encryptedLoader->load();
        return  array_keys($passwords);
    }

    protected function getPassword(string $alias): string
    {
        $passwords = $this->encryptedLoader->load();
        if (!isset($passwords[$alias])) {
            throw new LogicException("This password does not exits.\n");
        }
        return $passwords[$alias];
    }

    protected function removePassword(string $alias): void
    {
        $passwords = $this->encryptedLoader->load();
        if (isset($passwords[$alias])) {
            if (!$this->consoleHelper->confirm("Are you sure you want to remove '$alias'? [y/n] ")) {
                return;
            }
        } else {
            throw new LogicException("There is no such password.\n");
        }
        unset($passwords[$alias]);
        $this->encryptedLoader->save($passwords);
    }

    protected function setPassword(string $alias): void
    {
        $passwords = $this->encryptedLoader->load();
        if (isset($passwords[$alias])) {
            if (!$this->consoleHelper->confirm('This password already exist. Rewrite? [y/n] ')) {
                return;
            }
        }
        $passwords[$alias] = $this->passHelper->ask("Provide password for '$alias':");

        $this->encryptedLoader->save($passwords);
    }

    protected function createEncryptedLoader(string $fileWithPasswords, string $message): EncryptedLoader
    {
        return new EncryptedLoader(
            new Encryptor($this->passHelper->ask($message)),
            new Filesystem(),
            $fileWithPasswords
        );
    }

    protected function changeMasterPassword(): void
    {
        $oldEncryptedLoader = $this->encryptedLoader;
        $newEncryptedLoader = $this->createEncryptedLoader(
            $oldEncryptedLoader->getPath(),
            "New master password:"
        );

        $passwords = $oldEncryptedLoader->load();
        $newEncryptedLoader->save($passwords);

        $this->encryptedLoader = $newEncryptedLoader;
    }


}