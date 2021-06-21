<?php

require "functions.php";

if ($argc < 2){
    die(<<<USAGE
Usage:
    passman set <alias>
    passman get <alias>
    passman list
    passman copy <alias>
    passman remove <alias>
    #passman change-masterpass

USAGE
    );
}

# restricted to
# - show any password except `passamn get`
# - don't store plain password

[$file, $command, $alias] = array_pad($argv, 3, '');

//$fileWithPasswords = getenv('HOME') . '/.config/passman';

$fileWithPasswords = './.passman';
if (!file_exists($fileWithPasswords)){
    touch($fileWithPasswords);
}


switch ($command){
    case 'set':
        set_password($fileWithPasswords, $alias);
        die;
    case 'get':
        print "Password:" . get_password($fileWithPasswords, $alias)."\n";
        die;
    case 'list':
        $aliases = get_aliases($fileWithPasswords);
        print "Available passwords in the vault:\n";
        foreach ($aliases as $alias){
            print " - $alias\n";
        }
        die;
    case 'remove':
        remove_password($fileWithPasswords, $alias);
        die;
    case 'copy':
        $password = get_password($fileWithPasswords, $alias);
        shell_exec("echo \"$password\" | xsel -ib");
        echo "Copied.\n";
        die;
    case 'change-masterpass':
        change_master_pass($fileWithPasswords);
        die("Done.\n");
    default:
        die("No such command.\n");
}
