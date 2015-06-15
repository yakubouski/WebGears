<?php

/**
 * @package Core
 * @subpackage IO
 * Класс работы с файловой системой отнистельной базаовой директории приложения @see $_SERVER['DOCUMENT_ROOT']
 */
final class File {
    /**
     * @ignore
     */
    static public function FullPath($PathName) {
	return Application::Virtual().$PathName;
    }
    
    /**
     * Проверяет существует файл или нет
     * @param string $PathName
     * @return bool
     */
    static public function Exist($PathName) {
	return file_exists(self::FullPath($PathName));
    }
    /**
     * Возващает UNIX time последнего изменения файла
     * @param string $PathName
     * @return int|false
     */
    static public function Time($PathName) {
	return filemtime(self::FullPath($PathName));
    }

    /**
     * Создание директории
     * @param string $Path - относительный путь
     * @param bool|string $DenyFromAll - закрыть директорию .htaccess (deny from all)
     * @param int $Mode
     */
    static public function MkDir($Path,$DenyFromAll=false,$Mode=0774) {
	if(self::Exist($Path)) return true;
	$n = self::FullPath($Path);
	if(($res = mkdir(self::FullPath($Path),$Mode,true)) && !empty($DenyFromAll)) {
	    $firstDirectoryPath = preg_replace('%^([^/\\\\]+).*%m', '\1', $Path);
	    !empty($firstDirectoryPath) && 
		@file_put_contents(self::FullPath($firstDirectoryPath).DIRECTORY_SEPARATOR.'.htaccess', ($DenyFromAll === true?"order deny,allow\r\ndeny from all":$htaccess));
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
	($Flags & FILE_JSON) && ($Data = json_encode($Data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
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
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . filesize($fileName));
	
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
    
    static public function Type($FilePathName) {
	return mb_strtolower(pathinfo($FilePathName,PATHINFO_EXTENSION));
    }
}
