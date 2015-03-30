<?php
define ('FILE_GZIP',0x010000,true);
define ('FILE_JSON',0x020000,true);
define ('FILE_SERIALIZE',0x040000,true);
define('IS_WINDOWS',DIRECTORY_SEPARATOR=='\\',true);
define('APP_BASE_DIRECTORY',realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR,true);
define('APP_SYSTEM_DIRECTORY',__DIR__.DIRECTORY_SEPARATOR,true);

!defined('DEBUG') && define('DEBUG',false,true);
!defined('APP_INTERNAL_ENCODING') && define('APP_INTERNAL_ENCODING','utf-8',true);
!defined('APP_SESSION_PATH') && define('APP_SESSION_PATH','.sessions/',true);
!defined('APP_LOCALE_CODE') && define('APP_LOCALE_CODE','ru_RU.UTF-8',true);
!defined('APP_LOCALE_LANG') && define('APP_LOCALE_LANG','rus',true);
!defined('APP_LOCALE_LANGUAGE') && define('APP_LOCALE_LANGUAGE','russian',true);
!defined('APP_LOCALE') && define('APP_LOCALE','ru_RU',true);
!defined('APP_MODULES_DIRECTORY') && define('APP_MODULES_DIRECTORY','modules/',true);
!defined('APP_MODELS_DIRECTORY') && define('APP_MODELS_DIRECTORY','models/',true);
!defined('APP_COMPILE_DIRECTORY') && define('APP_COMPILE_DIRECTORY','.compile/',true);
!defined('APP_LOG_DIRECTORY') && define('APP_LOG_DIRECTORY',APP_BASE_DIRECTORY.'.log/',true);
!defined('APP_DEFAULT_MODULE') && define('APP_DEFAULT_MODULE','index',true);

/**
 * @package Core
 * @subpackage Application
 * @author Andrei Yakubouski
 */
final class Application {
    static public $directoryVirtualBase = APP_BASE_DIRECTORY;
    static public $directoryVirtualCompile = APP_BASE_DIRECTORY.APP_COMPILE_DIRECTORY;
    static public $directoryVirtualModules = APP_BASE_DIRECTORY.APP_MODULES_DIRECTORY;
    static public $directoryVirtualModels = APP_BASE_DIRECTORY.APP_MODELS_DIRECTORY;
    static public $directoryLogs = APP_LOG_DIRECTORY;
    static public $IsDebugMode = DEBUG;

    static private $ApplicationDBI,$ApplicationModules,$ApplicationControllers;

    /**
     * Определение виртуального хоста
     * @param string $RootDirectory относительный путь к коренвой директории виртуального хоста
     * @return string относительный путь коренвой директории 
     */
    static public function Virtual($RootDirectory=null) {
	if(!is_null($RootDirectory)) {
	    self::$directoryVirtualBase = APP_BASE_DIRECTORY.$RootDirectory.'/';
	    self::$directoryVirtualCompile = self::$directoryVirtualBase.APP_COMPILE_DIRECTORY;
	    self::$directoryVirtualModules = self::$directoryVirtualBase.APP_MODULES_DIRECTORY;
	    self::$directoryVirtualModels = self::$directoryVirtualBase.APP_MODELS_DIRECTORY;
	}
	return self::$directoryVirtualBase;
    }
    /**
     * Зарегистрировать или получить глобальный объект БД
     * @param \db\driver $instance
     * @return  \db\driver
     */
    static public function DBI($instance=null) {
	is_object($instance) && (self::$ApplicationDBI = $instance);
	return self::$ApplicationDBI;
    }
    /**
     * Запуск процесса обработки запроса
     * @param boolean $SessionStart Заускать сессию
     * @param string $SessionName имя сессии
     * @param string $SessionId ID сессии
     * @param int $SessionTTL время жизни сессии
     */
    static public function Run($SessionStart=FALSE,$SessionClassHandler=NULL, $SessionName=NULL, $SessionPath=NULL, $SessionTTL=NULL, $SessionId=NULL)
    {
	self::__rewriteResources();
	Session::Initialize($SessionClassHandler, $SessionName, $SessionPath, $SessionTTL, $SessionId);
        self::__dispatch();
    }

    static public function __autoload($Class) {
	$Class = str_replace('\\',DIRECTORY_SEPARATOR,strtolower(ltrim($Class,'\\')));
	if(!(file_exists(($filename = APP_SYSTEM_DIRECTORY.$Class.'.class.php')) || 
		file_exists(($filename = self::$directoryVirtualModels.$Class.'.class.php')))) {return FALSE;}
	require_once $filename;
    }
    
    /**
     * @ignore
     */
    static public function __initialize($Debug=false)
    {
	self::$IsDebugMode = $Debug;
	
	@mb_internal_encoding(APP_INTERNAL_ENCODING);
	@setlocale(LC_ALL, APP_LOCALE_CODE, APP_LOCALE_LANG, APP_LOCALE_LANGUAGE);
	@header('Content-Type: text/html; charset=utf-8');
	if(self::$IsDebugMode) {
	    @ini_set('display_errors','On');
	    @ini_set('display_startup_errors','On');
	    @ini_set('log_errors', 'On');
	    @ini_set('ignore_repeated_errors', 'On');
	    @error_reporting(-1);
	    @ini_set('error_log', self::$directoryLogs.'.php.log');
	} else {
	    @ini_set('display_errors','Off');
	    @ini_set('display_startup_errors','Off');
	    @ini_set('log_errors', 'On');
	    @ini_set('ignore_repeated_errors', 'On');
	    @error_reporting(-1);
	    @ini_set('error_log', self::$directoryLogs.'.php.log');
	}
	@spl_autoload_register('Application::__autoload');
    }
    
    static private function __rewriteResources() {
	static $AllowedExt = ['css'=>'text/css','js'=>'application/javascript','less'=>'text/css'];
	
	if((isset($_GET['::resource']) && !empty($_GET['::resource'])) && isset($AllowedExt[strtolower($_GET['::type'])])) {
	    $type = strtolower($_GET['::type']);
	    if($type == 'less') {
		\Lib\Less::Compile(Application::$directoryVirtualModules.$_GET['::resource']);
	    }
	    else {
		\File::DownloadFile(Application::$directoryVirtualModules.$_GET['::resource'],[], $AllowedExt[$type], true);
	    }
	}
	
	isset($_GET['::less']) && !empty($_GET['::less']) && \Lib\Less::Compile($_GET['::less']);
    }
    
    static private function __dispatch()
    {
        $requestRoute = !empty($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] !== '/') ? explode('/',ltrim($_SERVER['PATH_INFO'],'/')) : ['index'];
	
        $controllerName = strip_tags(array_shift($requestRoute));

	try {

	    if (($controller = self::Controller($controllerName))) {
		$methodName = isset($requestRoute[0]) ? strip_tags($requestRoute[0]) : NULL;
		((empty($methodName) || !method_exists($controller, ('on' . $methodName))) && ($methodName = 'Default')) || array_shift($requestRoute);
		(!method_exists($controller, "On$methodName")) && (Error::Exception('',$controller));
	    } elseif (!(($controller = self::Controller(APP_DEFAULT_MODULE)) && ($methodName = $controllerName) && method_exists($controller, "On$controllerName"))) {
		throw new Exception('', 0);
	    }
	    call_user_func_array(array($controller, "On$methodName"), $requestRoute);
	    
	} catch(Error $e) {
	    $DefaultContoller = self::Controller(APP_DEFAULT_MODULE);
	    if($DefaultContoller && method_exists($DefaultContoller, 'On404')) {
		$DefaultContoller->On404();
	    }
	    else {
		ob_clean();
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Page not found");
	    }
	    exit;
	} catch(Exception $e) {
	    ob_clean();
	    header("HTTP/1.0 500 Web Application error");
	    exit;
	}
    }
    
    /**
     * Получить объек по имени контролера
     * @param string $className имя контроллера
     * @return boolean|Controller 
     */
    static public function Controller($ControllerName,$load_only=false)
    {
	$ControllerName = strtolower($ControllerName);
	
        if(isset(self::$ApplicationControllers[$ControllerName]))
        {
            return self::$ApplicationControllers[$ControllerName];
        }
        else
        {
            if(!file_exists(($filename = (self::$directoryVirtualModules.$ControllerName . '/' . $ControllerName . '.controller.php'))))
            {
                return false;
            }
	    
	    ($ControllerName != APP_DEFAULT_MODULE) && self::Controller(APP_DEFAULT_MODULE, true);
            
	    include_once $filename;
	    
	    if(!$load_only) {
		$classOf = $ControllerName.'Controller';
		self::$ApplicationControllers[$ControllerName] = new $classOf;
		return self::$ApplicationControllers[$ControllerName];
	    }
        }
    }
    
    /**
     * Получает объект модуля. 
     *
     * @param string $ModuleName имя модуля
     * @return boolean|Module объект контроллера или NULL если контроллер не существует либо нет доступа
     */
    static public function Module($ModuleName)
    {
        $ModuleName = strtolower($ModuleName);

        if(isset(self::$ApplicationModules[$ModuleName]))
        {
            return self::$ApplicationModules[$ModuleName];
        }
        else
        {
            if(!file_exists(($filename = (self::$directoryVirtualModules.$ModuleName . '/' . $ModuleName . '.module.php'))))
            {
                return false;
            }
            include_once $filename;
            $classOf = $ModuleName.'Module';
            self::$ApplicationModules[$ModuleName] = new $classOf;
            return self::$ApplicationModules[$ModuleName];
        }
    }
}
/**
 * @package Core
 * @subpackage Application
 */
class Module {
    /**
     * Выполнить SQL запрос
     * @param string $SqlQuery формат SQL запроса
     * @param mixed $Param1
     * @return \SqlResult
     */
    protected function sqlQuery($SqlQuery) {
	return call_user_func_array("Sql::Query", func_get_args());
    }
    /**
     * Выполнить SQL запрос и вернуть результат в виде объекта
     * @param string $SqlQuery формат SQL запроса
     * @param array $QueryArgs масств аргументов
     * @param false|string|function $SubClassIterator
     * @return \SqlObject 
     */
    protected function sqlObject($SqlQuery,array $QueryArgs=[],$SubClassIterator=false) {
	array_unshift($QueryArgs, $Query);
	return Sql::Object(call_user_func_array("Sql::Query", $QueryArgs), $SubClassIterator);
    }
    
    /**
     * Выполнить SQL запрос и вернуть результат в виде набора строк
     * @param string $SqlQuery формат SQL запроса
     * @param array $QueryArgs масств аргументов
     * @param false|string|function $SubClassIterator
     * @return \SqlObject 
     */
    protected function sqlRecordset($SqlQuery,array $QueryArgs=[],$SubClassIterator=false) {
	array_unshift($QueryArgs, $Query);
	return Sql::Recordset(call_user_func_array("Sql::Query", $QueryArgs), $SubClassIterator);
    }
    
    public function __callStatic($ModuleName, $arguments) {
	return Application::Module($ModuleName) ?: new \Exception("Module: {$name} not found.");
    }
    
    protected function getUserId() {
	return class_exists('User') ? User::Get()->getId() : null;
    }
    protected function getUserGroup() {
	return class_exists('User') ? User::Get()->getGroup() : null;
    }
}

/**
 * @package Core
 * @subpackage Application
 */
abstract class Controller {
    
    abstract public function OnDefault();
    
    public static function  __callStatic($name,  $arguments)
    {
	return Application::Controller($name) ?: new \Exception("Controller: {$name} not found.");
    }
    
    /**
     * @param type $tplLocation
     * @param type $tplArgs
     * @return \Html\Template
     */
    protected function tpl($tplLocation,$tplArgs=[]) {
	return Html::Template($tplLocation, $tplArgs);
    }
    
    protected function Location($Url='/',$Params=[], $HttpCode=0) {
	while (ob_get_level()) ob_end_clean();
	$HttpCode ? header('Location: '.$Path.(!empty($Params) ? ('?'.http_build_query($Params)) :''),TRUE,$HttpCode) : 
	    header('Location: '.$Path.(!empty($Params) ? ('?'.http_build_query($Params)) :''));
	exit;
    }
    
    protected function HTTP_404($PageContent=false,$Code='404 Not Found') {
	while (ob_get_level()) ob_end_clean();
	header("HTTP/1.0 $Code");
	header("Status: $Code");
	!empty($PageContent) && print($PageContent);
	exit;
    }


    public function getUserID() { return class_exists('User') ? User::Get()->getId() : null; }
    public function getUserGroup() { return class_exists('User') ? User::Get()->getGroup() : null;  }
}


Application::__initialize(DEBUG);