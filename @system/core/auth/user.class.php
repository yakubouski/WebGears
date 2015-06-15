<?php
namespace Auth;
class User extends \Virtual
{
    private static $Object;
    public function __construct($Members=[]) {
	parent::__construct($Members);
	self::$Object = $this;
	$this->__wakeup();
    }
    static public function Get() {
	return self::$Object;
    }
    public function __wakeup() {
	self::$Object = $this;
	$reflection = new \ReflectionClass(get_class($this));
	foreach ($reflection->getStaticProperties() as $n=>$v) {
	    empty($v) && $this->__static($n, $n);
	}
    }
}
