<?php
abstract class SqlResult {
    /**
     * Возвращает строку результата запроса как объект класса
     * @param string $Class название класса
     * @param mixed $Params дополнительные параметры инициализации конструктора класса
     * @return stdClass 
     */
    abstract public function FetchObject($Class='\SqlObject',$Params=array());
    
    /**
     * Получить сторку выборки как ассоциативный массив
     * @return mixed 
     */
    abstract public function FetchAssocRow();
    /**
     * Получить сторку выборки как нумерованный массив
     * @return mixed 
     */
    abstract public function FetchNumRow();
    /**
     * Получить сторку выборки как ассоциативный и нумерованный массив
     * @return mixed 
     */
    abstract public function FetchRow();
    /**
     * Установить указатель выборки на на начало
     * @return boolean 
     */
    abstract public function Seek($Row);
    /**
     * Получить количество строк SQL запроса
     * @return int 
     */
    abstract public function Count();
    /**
     * Получить количество найденных строк SQL запроса
     * @return int 
     */
    abstract public function Found();
}

class SqlRecordset implements \Iterator, \Countable, \ArrayAccess {
    /**
     * @var SqlResult
     */
    private $QueryResult,$Iterator,$NumRows,$FoundRows;
    public function __construct($Result) {
        $this->QueryResult = $Result; $this->NumRows = $this->QueryResult ? $this->QueryResult->Count() : 0; $this->FoundRows = $this->QueryResult ? $this->QueryResult->Found() : $this->NumRows; $this->Iterator = 0;
    }
    public function count($mode = 'COUNT_NORMAL') {return $this->NumRows;}
    public function current() {return $this->QueryResult ? $this->QueryResult->FetchAssocRow() : 0;}
    public function key() {return $this->Iterator;}
    public function next() {++$this->Iterator;}
    public function rewind() {$this->Iterator = 0; $this->QueryResult ? $this->QueryResult->Seek(0) : null;}
    public function valid() {return $this->Iterator < $this->count();}
    public function offsetExists($offset) {return $offset < $this->count();}
    public function offsetGet($offset) { $this->QueryResult ? $this->QueryResult->Seek($offset) : null; return $this->QueryResult ? $this->QueryResult->FetchAssocRow() : null; }
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    public function found() {return $this->FoundRows;}
    public function IsEmpty() {
        return $this->NumRows == 0;
    }
    public function toArray($function=false) { 
	$Array = []; 
	if(is_callable($function)) {
	    foreach ($this as $r) { $Array[] = $function($r); } 
	}
	else {
	    foreach ($this as $r) { $Array[] = $r; } 
	}
	return $Array; 
    }
    public function toGroupArray($function=false) { 
	$Array = []; 
	if(is_callable($function)) {
	    foreach ($this as $r) { $function($r,$Array); } 
	}
	else {
	    foreach ($this as $r) { $Array[] = $r; } 
	}
	return $Array; 
    }
    public function Column($columnkey) { 
	$Array = []; 
	foreach ($this as $r) { $Array[] = @$r[$columnkey]; } 
	return $Array; 
    }
    
    public function toSortArray(callable $cmp_function) {
        $Array = [];
        foreach ($this as $r) { $Array[] = $r; } 
        usort($Array, $cmp_function);
        return $Array;
    }
}

/**
 * Класc для манипуляции результатом зароса, прдеставленным как объект
 */
class SqlObject implements \ArrayAccess {
    private $objFields;

    public function __construct($Result,$SubClassIterator=null) {
	$this->objFields = is_object($Result) ? $Result->FetchAssocRow() : $Result;
	is_callable($SubClassIterator) && $SubClassIterator($this);
    }

    private function __virtual($field) {
        if (method_exists($this, $field)) {
            if (isset($this->objFields[$field])) return $this->objFields[$field];
            $this->objFields[$field] = $this->$field();
            return $this->objFields[$field];
        }
        return NULL;
    }

    public function offsetExists($offset) { return isset($this->objFields[$offset]) || method_exists($this, $offset); }
    public function offsetGet($offset) { return (isset($this->objFields[$offset]) ? $this->objFields[$offset] : ($this->__virtual($offset))); }
    public function offsetSet($offset, $value) { $this->objFields[$offset] = $value; }
    public function offsetUnset($offset) { unset($this->objFields[$offset]); }
    public function __get($name) { return $this->offsetGet($name); }
    public function __set($name, $value) { return $this->offsetSet($name, $value); }
    public function __isset($name) { return $this->offsetExists($name); }
    public function __unset($name) { return $this->offsetUnset($name); }
    
    public function IsEmpty() {	return empty($this->objFields); }
    
    protected function __static($static_name,$member_name) {
	static::$$static_name = $this->$member_name;
    }
    
    public function ArrayObject() {
	return new \ArrayObject($this->objFields);
    }
}

class SqlUpdate {

    private $FieldsSet = [];

    private function filter($Set,$IgnoreNULL) {
	return array_filter($Set,function($v) use($IgnoreNULL) {
	   return ($IgnoreNULL && is_null($v)) ? false : true; 
	});
    }

    public function __construct(array $Set,$IgnoreNULL=true) {
        $this->FieldsSet = $this->filter($Set, $IgnoreNULL);
    }
    
    public function fields($AsArray=false) {
	return $AsArray ? array_keys($this->FieldsSet) : implode(', ',  array_keys($this->FieldsSet));
    }
    
    public function values($After=[],$Before=[]) {
	$Values = array_values($this->FieldsSet);
	!empty($Before) && ($Values = array_merge($Before,$Values));
	!empty($After) && ($Values = array_merge($Values,$After));
	return $Values;
    }
    
    public function __toString() {
	$FilteredArray = [];
	
	foreach($this->FieldsSet as $fld=>$val) {
	    
	    $FilteredArray[] = sprintf($fld,Sql::Escape($val));
	}
	
	return implode(', ',$FilteredArray);
    }
    
}

class SqlInsert {
    private $sqlQuery,$sqlValues,$tableRows,$sqlLimit;
    private $hDBI;
    public $AffectedRows = 0;
    
    public function  __construct($Query,$Limit=100,$DBI=false) {
	
	$this->hDBI = $DBI;
	
        $OnDuplicate = '';
        $this->sqlQuery = preg_replace_callback('/(ON\s+DUPLICATE\s+KEY\s+.*)/si', function($match) use (&$OnDuplicate) {
            $OnDuplicate = $match[1];
            return '::DUPLICATE::';
        }, $Query);
        $this->sqlQuery = preg_replace_callback('/VALUES\s*(\(.*\))/is', function($match) {
            $this->sqlValues = $match[1];
            return 'VALUES::VALUES::';
        }, $this->sqlQuery);
        
        $this->sqlQuery = str_replace('::DUPLICATE::', $OnDuplicate, $this->sqlQuery);
        
        $this->sqlLimit = $Limit;
    }
    public function  __destruct() {$this->Flush();}
    public function Insert()
    {
        if(func_num_args ()) {
            $args = $this->hDBI ? \Sql::Escape($this->hDBI,func_get_args()) : \Sql::Escape(func_get_args());
            $this->tableRows[] = \Sql::Format($this->sqlValues, $args);
            return (count($this->tableRows) != $this->sqlLimit) ? $this : $this->insert();
        }
        if(count($this->tableRows)) {
	    $this->tableRows = array_filter($this->tableRows);
            $this->AffectedRows += $this->hDBI ? \Sql::Query($this->hDBI,str_replace('::VALUES::', implode(', ',$this->tableRows), $this->sqlQuery)) :
		\Sql::Query(str_replace('::VALUES::', implode(', ',$this->tableRows), $this->sqlQuery));
            $this->tableRows = array();
        }
        return $this;
    }
    public function Flush() { $this->insert(); return $this->AffectedRows; }
}

abstract class SqlDbDriver {
    abstract public function query($sql,$args);
    abstract public function escape($args);
    abstract public function open();
    abstract public function close();
    abstract public function foundRows();
    abstract public function insertId();
}


class Sql 
{
    /**
     * Экранирование параметров
     * @param \SqlDriver $dbi [optional] - интерфейс БД, через который делается запрос к БД.
     * @param mixed $arg1 
     * @return mixed
     */
    static public function Escape(/*$dbi, */$arg1/*, $arg2, $arg3, $arg4*/) {
	$args = func_get_args();
	$dbi = is_object($args[0]) ? array_shift($args) : \Application::DBI();
	return (!is_object($dbi) && trigger_error('invalid_application_db_interface')) ? false : call_user_func_array([$dbi,'escape'], $args);
    }

    /**
     * Форматирование запроса
     * @param strinf $Query
     * @param mixed $arg1 
     * @return type
     */
    static public function Format($Query,$Arg1=null/*,...*/) {
	if(func_num_args()>1) {
	    $Args = self::Escape(is_array($Arg1) ? $Arg1 : array_slice(func_get_args(), 1));
	    return vsprintf($Query, $Args);
	}
	else {
	    return $Query;
	}
    }

    /**
     * Создает объект для массовой вставки строк в таблицу БД
     * @param string $Query
     * @param int $Limit
     * @param \SqlDriver $DBI [optional]
     * @return \SqlInsert
     */
    static public function Insert($Query,$Limit=100,$DBI=false) {
	return new \SqlInsert($Query, $Limit, $DBI);
    }
    
    /**
     * Создает объект для обновления значений строк таблицы
     * @param array $Fields
     * @param bool $SkipNullValue
     * @return \SqlUpdate
     */
    static public function Update($Fields=[],$SkipNullValue=true) {
	return new \SqlUpdate($Fields, $SkipNullValue);
    }

    /**
     * @param \SqlDriver $dbi [optional] - интерфейс БД, через который делается запрос к БД.
     * @param string $sql - SQl запрос
     * @param mixed $arg1 
     * @param mixed $arg2 ... 
     * @return \dbResult|bool
     */
    static public function Query(/*$dbi, */$sql/*, $arg1, $arg2, $arg3*/) {

	$args = func_get_args();
	$dbi = is_object($args[0]) ? array_shift($args) : \Application::DBI();
	$sql = array_shift($args);
	return (!is_object($dbi) && trigger_error('invalid_application_db_interface')) ? false : call_user_func([$dbi,'query'],$sql, $args);
    }

    /**
     * @param \SqlResult|bool $result
     * @return \SqlRecordset
     */
    static public function Recordset($Result,$SubClassIterator=null) {
        if(is_callable($SubClassIterator)) {
            $Rs =  new \SqlRecordset($Result);
            return $Rs->toGroupArray($SubClassIterator);
        }
        return new \SqlRecordset($Result);
    }

    /**
     * 
     * @param \SqlResult|bool $Result
     * @param string|function|null $SubClassIterator
     * @return \SqlObject
     */
    static public function Object($Result, $SubClassIterator=null) {
	return is_string($SubClassIterator) ? (new $SubClassIterator($Result)) : new \SqlObject($Result,$SubClassIterator);
    }
    
    static public function MySql($dbHost,$dbUser,$dbPass,$dbSchema=false,$dbOptions='',$dbPort=3306,$dbEncoding='UTF8',$lcCode='ru_RU') {
	return new \DB\MySql($dbHost, $dbUser, $dbPass, $dbSchema, $dbOptions, $dbPort, $dbEncoding,$lcCode);
    }
}
