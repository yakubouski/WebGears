<?php
namespace DB;
require_once __DIR__.'./../sql.class.php';

class MySqlResult extends \SqlResult {
    /**
     * @var mysqli_result 
     */
    private $hResult;
    private $numRows;
    private $numFoundRows;
    public function __construct(&$result,$numResultRows,$numFoundRows=false) {
        $this->hResult =& $result;
        $this->numRows = $numResultRows;
	$this->numFoundRows = $numFoundRows;
    }
    
    public function __destruct() {
        $this->hResult->free();
    }
    
    /**
     * Возвращает строку результата запроса как объект класса
     * @param string $Class название класса
     * @param mixed $Params дополнительные параметры инициализации конструктора класса
     * @return stdClass 
     */
    public function FetchObject($Class = '\SqlObject', $Params = array()) { 
        return $this->hResult->fetch_object($Class, $Params); 
    }
    
    /**
     * Получить сторку выборки как ассоциативный массив
     * @return mixed 
     */
    public function FetchAssocRow() { return $this->hResult->fetch_array(MYSQLI_ASSOC); }
    /**
     * Получить сторку выборки как нумерованный массив
     * @return mixed 
     */
    public function FetchNumRow() { return $this->hResult->fetch_array(MYSQLI_NUM); }
    /**
     * Получить сторку выборки как ассоциативный и нумерованный массив
     * @return mixed 
     */
    public function FetchRow($Type=MYSQLI_ASSOC) { return $this->hResult->fetch_array($Type); }
    /**
     * Установить указатель выборки на на начало
     * @return boolean 
     */
    public function Reset() { return $this->hResult->data_seek(0); }
    /**
     * Получить количество строк SQL запроса
     * @return int 
     */
    public function Count() {return $this->numRows;}
    
    /**
     * Получить количество найденных строк SQL запроса
     * @return int 
     */
    public function Found() {return $this->numFoundRows;}
    
    public function Seek($Row) {
	$this->hResult->data_seek($Row);
    }

}

class MySql extends \SqlDbDriver
{
    /**
     * @var mysqli 
     */
    private $hDbi;
    private $dbHost,$dbUser,$dbPass,$dbSchema,$dbPort,$dbEncoding,$dbOptions,$dbLC;
    /**
     * @return mysqli
     */
    protected function handle()
    {
        if(!$this->hDbi) {
	    /**
	     * @var mysqli Description
	     */
            $this->hDbi = mysqli_init();
	    !empty($this->dbOptions) &&  $this->hDbi->options(MYSQLI_READ_DEFAULT_GROUP,$this->dbOptions);
            !($this->hDbi->real_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbSchema, $this->dbPort)) && 
		    trigger_error(__METHOD__.' '.$this->hDbi->connect_error,E_USER_WARNING);
            $this->hDbi->set_charset($this->dbEncoding);
	    !empty($this->dbLC) && $this->hDbi->query("SET lc_time_names = '{$this->dbLC}'/*,GLOBAL group_concat_max_len = 20000000, SESSION group_concat_max_len = 20000000*/");
        }
        return $this->hDbi;
    }
    
    public function init($dbHost=false,$dbUser=false,$dbPass=false,$dbSchema=false,$dbOptions='',$dbPort=3306,$dbEncoding='UTF8',$lcCode='ru_RU')
    {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbSchema = $dbSchema;
        $this->dbPort = $dbPort;
        $this->dbEncoding = $dbEncoding;
	$this->dbOptions = $dbOptions;
	$this->dbLC = $lcCode;
	return $this;
    }
    
    public function __construct($dbHost,$dbUser,$dbPass,$dbSchema=false,$dbOptions='',$dbPort=3306,$dbEncoding='UTF8',$lcCode='ru_RU') {
	call_user_func_array([$this,'init'], func_get_args());
    }


    public function open() { $this->handle(); return $this; }
    public function close() {return $this;}

    public function escape($args) {
	if(is_array($args)) {
	    foreach ($args as &$value) {
		$value = $this->handle()->real_escape_string($value);
	    }
	    return $args;
	}
	else {
	    return $this->handle()->real_escape_string($args);
	}
    }

    public function foundRows() {
	$found_rows = 0;
        ($found_result = $this->handle()->query('SELECT FOUND_ROWS() as `count`')) && (($found_rows = $found_result->fetch_object()->count));
        $found_result && $found_result->free();
        return $found_rows;
    }

    public function insertId() {
	return $this->handle()->insert_id;
    }



    public function query($sql, $args) {
	$result = empty($args) ? $this->handle()->query(\Sql::Format($sql)) : $this->handle()->query(\Sql::Format($sql, $args));
	
	if($result) {
            return is_object($result) ? new MySqlResult($result, $result->num_rows,  preg_match('/\bSQL_CALC_FOUND_ROWS\b/i', $sql) ? $this->foundRows() : $result->num_rows) : 
                ( $this->handle()->insert_id?:($this->handle()->affected_rows!=-1?$this->handle()->affected_rows:true) );
        }
        elseif($this->handle()->errno) {
            trigger_error(__CLASS__.'::query('.$sql.') '.$this->handle()->error,E_USER_WARNING);
        }
	return false;
    }
}
