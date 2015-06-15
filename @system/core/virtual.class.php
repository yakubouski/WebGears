<?php
class Virtual implements \ArrayAccess
{
    /**
     * @var array 
     */
    protected $objMembers;
    /**
     *	Нельзя использовать один массив для объявленных и виртуальных, виртуальные переменные это вычисляемые иначае при сериализации они будут сохраняться
     * @var array 
     */
    private $objVirtual = [];
    public function __construct($Members=[]) {
	$this->objMembers = $Members;
	$this->objVirtual = [];
    }
    
    private function __virtual($member) {
	if (isset($this->objVirtual[$member])) return $this->objVirtual[$member];
        if (method_exists($this, $member)) {
            $this->objVirtual[$member] = $this->$member();
            return $this->objVirtual[$member];
        }
        return NULL;
    }

    public function offsetExists($offset) { return isset($this->objMembers[$offset]) || isset($this->objVirtual[$offset]) || method_exists($this, $offset); }
    public function offsetGet($offset) { return (isset($this->objMembers[$offset]) ? $this->objMembers[$offset] : ($this->__virtual($offset))); }
    public function offsetSet($offset, $value) { isset($this->objMembers[$offset]) ? $this->objMembers[$offset] = $value : $this->objVirtual[$offset] = $value; }
    public function offsetUnset($offset) { unset($this->objMembers[$offset]); unset($this->objVirtual[$offset]); }
    public function __get($name) { return $this->offsetGet($name); }
    public function __set($name, $value) { return $this->offsetSet($name, $value); }
    public function __isset($name) { return $this->offsetExists($name); }
    public function __unset($name) { return $this->offsetUnset($name); }
    
    public function IsEmpty() {	return empty($this->objMembers); }
    
    /**
     * Иннициализировать статическую перменную, одно из преминений, это инициализация глобальных статических объектов, 
     * к пример авторизованного пользователя
     * @param string $static_name название статической перменной
     * @param string $member_name название свойства объекта
     */
    protected function __static($static_name,$member_name) {
	static::$$static_name = $this->$member_name;
    }
    
    public function __sleep() {
	return ['objMembers'];
    }
    
    /**
     * @return \ArrayObject
     */
    public function ArrayObject() {
	return new \ArrayObject(array_merge($this->objMembers,$this->objVirtual));
    }
    
    /**
     * Создает виртуальный объект из строки после explode
     * 
     * <code>
     * $obj = Virtual::Explode('|','1|Имя|Фамилия','id','Firstname','Lastname');<br>
     * echo $obj['Lastname'];<br>
     * </code>
     * 
     * @param string $delimiter
     * @param string $string
     * @param mixed $prop
     * @param mixed $prop1
     * 
     * @return \Virtual 
     */
    static public function Explode($delimiter,$string) {
	if(empty($string)) return NULL;
	$values1 = array_slice(array_merge(explode($delimiter,$string)?:array(), array_fill(0, func_num_args()-2, NULL)),0,func_num_args()-2);
	return new Virtual(array_combine(array_slice(func_get_args(), 2), $values1));
    }
    /**
     * Создает виртуальный объект со свойствами $Members
     * @param array $Members
     * @return \Virtual
     */
    static public function Object(array $Members=[]) {
	return new Virtual($Members);
    }
    
    
}


