<?php
namespace DB;
require_once '../sql.class.php';

class MySql extends \SqlDbDriver
{
    /**
     * @var mysqli 
     */
    private $hDbi;
    private $dbHost,$dbUser,$dbPass,$dbSchema,$dbPort,$dbEncoding,$dbOptions;
    /**
     * @return mysqli
     */
    private function handle()
    {
        if(!$this->hDbi) {
	    /**
	     * @var mysqli Description
	     */
            $this->hDbi = mysqli_init();
	    !empty($this->dbOptions) &&  $this->hDbi->options(MYSQLI_READ_DEFAULT_GROUP,$this->dbOptions);
            !($this->hDbi->real_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbSchema, $this->dbPort)) && 
                \Error::Exception($this->hDbi->connect_error);
            $this->hDbi->set_charset($this->dbEncoding);
	    $this->hDbi->query("SET lc_time_names = 'ru_RU'/*,GLOBAL group_concat_max_len = 20000000, SESSION group_concat_max_len = 20000000*/");
        }
        return $this->hDbi;
    }
    
    public function init($dbHost=false,$dbUser=false,$dbPass=false,$dbSchema=false,$dbOptions='',$dbPort=3306,$dbEncoding='UTF8')
    {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbSchema = $dbSchema;
        $this->dbPort = $dbPort;
        $this->dbEncoding = $dbEncoding;
	$this->dbOptions = $dbOptions;
	return $this;
    }
    
    public function __construct($dbHost,$dbUser,$dbPass,$dbSchema=false,$dbOptions='',$dbPort=3306,$dbEncoding='UTF8') {
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
            return is_object($result) ? new \Sql\MysqlResult($result, $result->num_rows,  preg_match('/\bSQL_CALC_FOUND_ROWS\b/i', $sql) ? $this->foundRows() : $result->num_rows) : 
                ( $this->handle()->insert_id?:($this->handle()->affected_rows!=-1?$this->handle()->affected_rows:true) );
        }
        elseif($this->handle()->errno) {
            trigger_error(__CLASS__.'::query('.$sql.') '.$this->handle()->error,E_USER_ERROR);
        }
	return false;
    }
}
