<?php
/**
 * Description of session
 *
 * @author Andrei Yakubouski
 */
class Session extends SessionHandler
{
    static public function Initialize($SessionClassHandler=FALSE,$SessionName=NULL,$SessionPath=NULL,$SessionTTL=NULL,$SessionId=NULL) 
    {
	!empty($SessionPath) && ($SessionPath = trim($SessionPath,'\\/'));
	!empty($SessionPath) && !file_exists(Application::$directoryVirtualBase.$SessionPath) &&
		mkdir(Application::$directoryVirtualBase.$SessionPath,true,0770);
	!empty($SessionPath) && !file_exists(Application::$directoryVirtualBase.$SessionPath.'/.htaccess') &&
	    file_put_contents(Application::$directoryVirtualBase.$SessionPath.'/.htaccess', "order deny,allow\ndeny from all");
	!empty($SessionPath) && file_exists(Application::$directoryVirtualBase.$SessionPath) &&
		@session_save_path(Application::$directoryVirtualBase.$SessionPath);
	$SessionName && @session_name($SessionName);
	$SessionTTL && session_set_cookie_params($SessionTTL);
	$SessionId && @session_id($SessionId);
	$SessionClassHandler && @session_set_save_handler(new $SessionClassHandler, true);
	@session_start();
    }
    
    static public function Sql($Table,$DBI) 
    {
	 $SessionPath && (\File::MkDir(empty($SessionPath)?APP_SESSION_PATH:$SessionPath,true,0770) && 
		@session_save_path(\File::Path(empty($SessionPath)?APP_SESSION_PATH:$SessionPath)));
	$SessionName && @session_name($SessionName);
	$SessionTTL && session_set_cookie_params($SessionTTL);
	$SessionId && @session_id($SessionId);
	$SessionClassHandler && session_set_save_handler(new $SessionClassHandler, true);
    }
}

class SqlSessionHandler extends SessionHandler {
    const SQL_OPEN = "DELETE FROM `%s` WHERE `%s` < (CURRENT_TIMESTAMP - INTERVAL %d SECOND)";
    private $sessionTTL;
    private $sqlDBI, $sqlTable, $sqlFieldId, $sqlFieldData, $sqlFieldTime;
    public function __construct($DBI,$sqlTable,$SessionTTL,$sqlFieldId='session_id',$sqlFieldData='session_data',$sqlFieldTime='session_time') {
	$this->sqlDBI = empty($DBI) ? Application::DBI() : $DBI;
	$this->sqlTable = $sqlTable;
	$this->sqlFieldId = $sqlFieldId;
	$this->sqlFieldData = $sqlFieldData;
	$this->sqlFieldTime = $sqlFieldTime;
	$this->sessionTTL = $SessionTTL;
    }
    private function query($SQL) {
	return (!empty($this->sqlDBI) ? call_user_func_array([$this->sqlDBI,'query'], func_get_args()) : false);
    }
    /**
     * Open the session
     * @return bool
     */
    public function open($save_path, $session_id) {
	return $this->query(self::SQL_OPEN,  $this->sqlTable,  $this->sqlFieldTime,  $this->sessionTTL);
    }
    /**
     * Close the session
     * @return bool
     */
    public function close() {
        return true;
    }
    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id) {
        $sql = sprintf("SELECT data FROM %s WHERE id = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
        if ($result = $this->dbConnection->query($sql)) {
            if ($result->num_rows && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                return $record['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
        
    }
    
    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data) {
        $sql = sprintf("REPLACE INTO %s VALUES('%s', '%s', '%s')",
        			   $this->dbTable, 
                       $this->dbConnection->escape_string($id),
                       $this->dbConnection->escape_string($data),
                       time());
        return $this->dbConnection->query($sql);
    }
    /**
     * Destoroy the session
     * @param int session id
     * @return bool
     */
    public function destroy($id) {
        $sql = sprintf("DELETE FROM %s WHERE `id` = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
        return $this->dbConnection->query($sql);
	}
	
	
    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public function gc($max) {
        $sql = sprintf("DELETE FROM %s WHERE `timestamp` < '%s'", $this->dbTable, time() - intval($max));
        return $this->dbConnection->query($sql);
    }
}