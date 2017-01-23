<?php
namespace Cache;
function HttpInvalidateCache() {
    return (strcasecmp(@$_SERVER['HTTP_PRAGMA'], 'no-cache')==0 || strcasecmp(@$_SERVER['HTTP_CACHE_CONTROL'], 'no-cache')==0);
}
class Data 
{

    static public function Session($Name,$Key,$Getter/*, $arg1, $arg2*/) {
        ( !isset($_SESSION[$Name][$Key]) || \Cache\HttpInvalidateCache()) && ($_SESSION[$Name][$Key] = call_user_func_array($Getter, array_slice(func_get_args(),3)));
        return $_SESSION[$Name][$Key];
    }
}
