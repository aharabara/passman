<?php

use Passman\ConsoleHelper;
use Passman\EncryptedLoader;
use Passman\Encryptor;
use Passman\Filesystem;
use Passman\PasswordQuestionHelper;


function get_confirmation(string $msg): bool
{
    $response = strtolower(readline($msg));
    if ($response === 'y') return true;
    if ($response === 'n') return false;

    return get_confirmation($msg);
}

function get_usage(): string
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


# content = brown fox jumps over the lazy dog
# map     = testtesttesttesttesttesttesttestt
# result  =.s;af,lpwemeiasjdpqw;


function execute($command, $alias, ?ConsoleHelper $helper = null, ?Filesystem $filesystem = null): void
{
    $fileWithPasswords = './.passman';
    if (!file_exists($fileWithPasswords)) {
        touch($fileWithPasswords);
    }

    try {

        $consoleHelper = $consoleHelper ?? new ConsoleHelper();
        $passHelper = new PasswordQuestionHelper($consoleHelper);
        $masterPass = $passHelper->ask("Master password: ");

        $encryptedLoader = new EncryptedLoader(
            new Encryptor($masterPass),
            $filesystem ?? new Filesystem
        );

        switch ($command) {
            case 'set':
                $passwords = $encryptedLoader->load($fileWithPasswords);
                if (isset($passwords[$alias])) {
                    if (!get_confirmation('This password already exist. Rewrite? [y/n] ')) {
                        return;
                    }
                }
                $passwords[$alias] = $passHelper->ask("Provide password for '$alias':");

                $encryptedLoader->save($fileWithPasswords, $passwords);
                die;
            case 'get':
                $passwords = $encryptedLoader->load($fileWithPasswords);
                if (!isset($passwords[$alias])) {
                    die("This password does not exits.\n");
                }
                print "Password:" . $passwords[$alias] . "\n";
                die;
            case 'list':
                $aliases = array_keys($encryptedLoader->load($fileWithPasswords));
                print "Available passwords in the vault:\n";
                foreach ($aliases as $alias) {
                    print " - $alias\n";
                }
                die;
            case 'remove':
                $passwords = $encryptedLoader->load($fileWithPasswords);
                if (isset($passwords[$alias])) {
                    if (!get_confirmation("Are you sure you want to remove '$alias'? [y/n] ")) {
                        return;
                    }
                } else {
                    die("There is no such password.\n");
                }
                unset($passwords[$alias]);
                $encryptedLoader->save($fileWithPasswords, $passwords);
                die;
            case 'copy':
                $passwords = $encryptedLoader->load($fileWithPasswords);
                if (!isset($passwords[$alias])) {
                    die("This password does not exits.\n");
                }
                $password = $passwords[$alias];
                shell_exec("echo \"$password\" | xsel -ib");
                echo "Copied.\n";
                die;
            case 'change-masterpass':
                $passwords = $encryptedLoader->load($fileWithPasswords);
                $encryptedLoader->save($fileWithPasswords, $passwords);
                die("Done.\n");
            default:
                die("No such command.\n");
        }

    } catch (Throwable $e) {
        die($e->getMessage() . PHP_EOL);
    }
}

