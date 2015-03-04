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
!defined('APP_MODULES_DIRECTORY') && define('APP_MODULES_DIRECTORY',APP_BASE_DIRECTORY.'modules/',true);
!defined('APP_MODELS_DIRECTORY') && define('APP_MODELS_DIRECTORY',APP_BASE_DIRECTORY.'models/',true);
!defined('APP_COMPILE_DIRECTORY') && define('APP_COMPILE_DIRECTORY',APP_BASE_DIRECTORY.'.compile/',true);
!defined('APP_LOG_DIRECTORY') && define('APP_LOG_DIRECTORY',APP_BASE_DIRECTORY.'.log/',true);
!defined('APP_DEFAULT_MODULE') && define('APP_DEFAULT_MODULE','index',true);

/**
 * @author Andrei Yakubouski
 */
final class Application {
    static public $directoryVirtualBase = APP_BASE_DIRECTORY;
    static public $directoryVirtualCompile = APP_COMPILE_DIRECTORY;
    static public $directoryVirtualModules = APP_MODULES_DIRECTORY;
    static public $directoryVirtualModels = APP_MODELS_DIRECTORY;
    static public $directoryLogs = APP_LOG_DIRECTORY;
    static public $IsDebugMode = DEBUG;

    static private $ApplicationDBI,$ApplicationModules,$ApplicationControllers;

    static public function Virtual($RootDirectory=null) {
	if(!is_null($RootDirectory)) {
	    self::$directoryVirtualBase = $RootDirectory.'/';
	    self::$directoryVirtualCompile = self::$directoryVirtualBase.APP_COMPILE_DIRECTORY;
	    self::$directoryVirtualModules = self::$directoryVirtualBase.APP_MODULES_DIRECTORY;
	    self::$directoryVirtualModels = self::$directoryVirtualBase.APP_MODELS_DIRECTORY;
	}
	return self::directoryVirtualBase;
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
 * Класс работы с файловой системой отнистельной базаовой директории приложения @see $_SERVER['DOCUMENT_ROOT']
 */
final class File {
    /**
     * @ignore
     */
    static public function FullPath($PathName) {
	return APP_BASE_DIRECTORY.$PathName;
    }
    
    /**
     * Проверяет существует файл или нет
     * @param string $PathName
     * @return bool
     */
    static public function Exist($PathName) {
	return file_exists(APP_BASE_DIRECTORY.$PathName);
    }
    /**
     * Возващает UNIX time последнего изменения файла
     * @param string $PathName
     * @return int|false
     */
    static public function Time($PathName) {
	return filemtime(APP_BASE_DIRECTORY.$PathName);
    }

    /**
     * Создание директории
     * @param string $Path - относительный путь
     * @param bool|string $htaccess - закрыть директорию .htaccess (deny from all)
     * @param int $Mod
     */
    static public function MkDir($Path,$htaccess=false,$Mod=0774) {
	if(self::Exist($Path)) return true;
	if(($res = mkdir(self::FullPath($Path),$Mod,true)) && !empty($htaccess)) {
	    $firstDirectoryPath = preg_replace('%^([^/\\\\]+).*%m', '\1', $Path);
	    !empty($firstDirectoryPath) && 
		@file_put_contents(APP_BASE_DIRECTORY.$firstDirectoryPath.DIRECTORY_SEPARATOR.'.htaccess', ($htaccess === true?"order deny,allow\r\ndeny from all":$htaccess));
	}
	return $res;
    }
    /**
     * Записать файл по относительному пути. Если путь не существует то он будет создан
     * @param string $FilePathName
     * @param mixed $Data
     * @param int $Flags - Дополнительно можно использовать флаги: 
     *	FILE_GZIP - содержимое файла будет сжиматься, 
     *	FILE_JSON - данный будут сериализованы с помощью json_encode,
     *	FILE_SERIALIZE - данные будут сериализованы с помощью serialize
     * @return bool
     */
    static public function Write($FilePathName,$Data,$Flags=0) {
	self::MkDir(dirname($FilePathName),true);
	($Flags & FILE_SERIALIZE) && ($Data = serialize($Data));
	($Flags & FILE_JSON) && ($Data = json_encode($Data));
	($Flags & FILE_GZIP) && ($Data = gzencode($Data,9));
	$Flags = $Flags & (~(FILE_GZIP|FILE_JSON|FILE_SERIALIZE));
	return file_put_contents(self::FullPath($FilePathName), $Data,$Flags);
    }
    /**
     * Прочитать содержимое файла
     * @param string $FilePathName
     * @param int $Flags смотри application::write_file
     * @return mixed
     */
    static public function Read($FilePathName,$Flags=0) {
	$content = file_get_contents(self::FullPath($FilePathName));
	if(!empty($content)) {
	    ($Flags & FILE_GZIP) && ($content = gzdecode($content));
	    ($Flags & FILE_JSON) && ($content = json_decode($content,true));
	    ($Flags & FILE_SERIALIZE) && ($content = unserialize($content));
	}
	else {
	    $content = '';
	}
	return $content;
    }
    
    /**
     * Получить список файлов и папк в каталоге
     * @param string $PathName
     * @param function $Filter
     * @return array
     */
    static public function Dir($PathName,$Filter=false) {
	$filelist = array();
	$PathName = self::FullPath($PathName);
	
	if (($dir = opendir($PathName))) {
	    $PathName = rtrim($PathName, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	    if (is_callable($Filter)) {
		while (false !== ($fname = readdir($dir))) { if($fname == '.' || $fname == '..')     continue;
		    $info = pathinfo($PathName . $fname);
		    unset($info['dirname']);
		    $info['extension'] = strtolower($info['extension']);
		    $info['fullname'] = $PathName . $fname;
		    $info['is_dir'] = is_dir($info['fullname']);
		    call_user_func($Filter, $info) && $filelist[$info['basename']] = $info;
		}
	    } else {
		while (false !== ($fname = readdir($dir))) { if($fname == '.' || $fname == '..')     continue;
		    $info = pathinfo($PathName . $fname);
		    unset($info['dirname']);
		    $info['extension'] = isset($info['extension']) ? strtolower($info['extension']) : '';
		    $info['fullname'] = $PathName . $fname;
		    $info['is_dir'] = is_dir($info['fullname']);
		    $filelist[$info['basename']] = $info;
		}
	    }
	    closedir($dir);
	}
	return $filelist;
    }
    
    /**
     * Выгрузить файл
     * @param string $FilePathName
     * @param string $Mime
     * @param array $Headers
     * @param bool $Cache
     * @param int $CacheTTL
     */
    static public function DownloadFile($FilePathName,$Mime,$Headers=[],$Cache=false,$CacheTTL=0) {
	$fileName = self::FullPath($FilePathName);

	while (@ob_get_level()) { @ob_end_clean(); }
	
        header('Content-type: '.$Mime);
	!empty($Headers) &&  @array_map('header',$Headers);
	
	if($Cache && file_exists($fileName)) {
	    $etagFile = @md5_file($fileName);
	    $lastModified = @filemtime($fileName);
	    @header('Pragma: cache');
	    $CacheTTL ? @header("Cache-Control: max-age=".$CacheTTL) : @header('Cache-Control: public');
	    $CacheTTL && @header("Expires: ".gmdate("D, d M Y H:i:s", time() + $CacheTTL) . " GMT");
	    @header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
	    @header("Etag: ".$etagFile);
	    
	    $ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
	    $etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
	    
	    if(@strtotime($ifModifiedSince) == $lastModified || $etagHeader == $etagFile){
		header("HTTP/1.1 304 Not Modified");
		exit;
	    }
	}
	@file_exists($fileName) && readfile($fileName);
        exit;
    }
    /**
     * Выгрузить содержимое
     * @param type $Content
     * @param type $Mime
     * @param type $Headers
     */
    static public function DownloadContent($Content,$Mime,$Headers=[]) {
	while (@ob_get_level()) { @ob_end_clean(); }
        header('Content-type: '.$Mime);
	!empty($Headers) &&  @array_map('header',$Headers);
	print (string)$Content;
        exit;
    }
}

final class Console {
    static public function Error($object) {
	if(DEBUG) {
	    echo '<script>console.error('.  json_encode($object,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).');</script>';
	}
    }
}
Application::__initialize(DEBUG);