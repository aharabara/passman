<?php
if ($argc < 2){
    die(<<<USAGE
    Usage:
        passman set <alias>
        passman get <alias>
        #passman copy <alias>
        #passman remove <alias>
        #passman change-masterpass
USAGE
    );
}

# restricted to
# - show any password except `passamn get`
# - don't store plain password

[$file, $command, $alias] = array_pad($argv, 3, '');

$fileWithPasswords = getenv('HOME') . '/.config/passman';
if (!file_exists($fileWithPasswords)){
    touch($fileWithPasswords);
}


switch ($command){
    case 'set':
//        set_password($fileWithPasswords, $alias);
        die;
    case 'get':
        print "Passowrd:" . get_password($fileWithPasswords, $alias)."\n";
        die;
    case 'list':
//        $aliases = get_aliases($fileWithPasswords);
//        foreach ($aliases as $alias){
//            print " - $alias\n";
//        }
        die;
    case 'copy':
//        get_password($fileWithPasswords, $alias);
        die;
    case 'remove':
//        get_password($fileWithPasswords, $alias);
        die;
}
