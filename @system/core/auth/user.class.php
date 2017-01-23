<?php
namespace Auth;
abstract class User extends \Virtual
{
    /** @var int Id пользователя */ static public $Id;
    /** @var User Экземпляр объекта */ static public $Object;
    public function __construct($UserProperties=[]) {
	parent::__construct($UserProperties);
	$this->__wakeup();
    }
    public function __wakeup() {
	self::$Object = $this;
	$reflection = new \ReflectionClass(get_class($this));
	foreach ($reflection->getStaticProperties() as $n=>$v) {
	    empty($v) && $this->__static($n, $n);
	}
    }
    static public function Is($SIGNOUT_VARIABLE='signout',$SESSION_ID=SESSION_USER_VARIABLE) {
	isset($_REQUEST[$SIGNOUT_VARIABLE]) && self::SignOut('/',$SESSION_ID);
	return (isset($_SESSION[$SESSION_ID]) && !empty($_SESSION[$SESSION_ID]));
    }
    static protected function SignInBasic() {
	
    }
    /**
     * Авторизация пользователя через БД
     * @param \SqlObject $SqlResult
     * @return boolean
     */
    static public function SignInDb($SqlResult,$SESSION_ID=SESSION_USER_VARIABLE) {
	$Result = get_class($SqlResult)==='SqlObject' || is_subclass_of($SqlResult, 'SqlObject') ? $SqlResult : \Sql::Object($SqlResult);
	if(!$Result->IsEmpty()) {
	    $Class = get_called_class();
	    $_SESSION[$SESSION_ID] = new $Class($Result->ArrayObject());
	    return true;
	}
	\Exception('invalid_login_params');
    }

    static public function SignIn(){}
    
    static public function SignOut($RedirectUri='/',$SESSION_ID=SESSION_USER_VARIABLE) {
	if(isset($_SESSION[$SESSION_ID])) {
	    self::$Object = null;
	    $_SESSION[$SESSION_ID] = null;
	    unset($_SESSION[$SESSION_ID]);
	    session_destroy();
	    while (ob_get_level()) ob_end_clean();
	    header('Location: '.$RedirectUri);
	    exit;
	}
    }
}
