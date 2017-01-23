<?php
class Form 
{
    static public function SecToTime($Sec,$Format='%02d:%02d:%02d',&$H=null,&$M=null,&$S=null) {
        $Sec = intval($Sec);
        $H = floor($Sec/3600);
        $M = floor(($Sec - $H*3600) / 60);
        $S = $Sec - $H*3600 - $M * 60;
        return sprintf($Format,$H,$M,$S);
    }
    
    static public function MinutesToText($Minutes,$Glue=' ') {
        $D = intval(floor($Minutes/(24*60)));
        $H = intval(floor(($Minutes - $D*24*60)/60));
        $M = intval(floor(($Minutes - $H*60 - $D*24*60)));
        $Txt = [];
        !empty($D) && $Txt[] = sprintf('%d&nbsp;дн.',$D);
        ((empty($H) && !empty($D)) || !empty($H)) && $Txt[] = sprintf('%d&nbsp;ч.',$H);
        
        (empty($M) && (empty($H) && empty($D))) ? $Txt[] = sprintf('%d&nbsp;мин.',$M) : '';
        
        !empty($M) && $Txt[] = sprintf('%d&nbsp;мин.',$M);
        
        return implode($Glue, $Txt);
    }
    
    /**
     * Убирает из пути все непечатанные символы, символы переходы типа ../ /./ и т.д.
     * @param string $value
     * @param bool $trim убирает начальные и конечные смиволы /\
     * @return string
     */
    static public function Path($value,$trim=false) {
        return $trim ? trim(preg_replace('%(\.\./)|(/\./)|([^\w.\\\\/-]+)%', '/', $value),'\\/'):
            preg_replace('%(\.\./)|(/\./)|([^\w.\\\\/-]+)%', '/', $value);
    }
    /**
     * Преобразует значение к целому, убирает все символы кроме цифр [+-]
     * @param mixed $value
     * @return int
     */
    static public function Int($value) {
	is_string($value) && ($value = preg_replace('%[^\d\+\-]+%', '', $value));
	return intval($value);
    }
    /**
     * Преобразует значение в строку
     * @param mixed $value
     * @param int $Length максимальное количество символов
     * @param bool $Escape strip_tags
     * @param bool $Trim trim
     * @return string
     */
    static public function String($value,$Length=0,$Escape=true,$Trim=true) {
	$value = (string)$value;
	$Length && ($value = mb_substr($value, 0,$Length));
	$Escape && ($value = strip_tags($value));
	$Trim && ($value = trim($value));
	return $value;
    }
    /**
     * Преобразует строку в символьное число, убирая все кроме цифр
     * @param mixed $value
     * @return string
     */
    static public function Digits($value) {
	return preg_replace('/\D+/', '', (string)$value);
    }
    /**
     * Проверяет строку на шаблон адреса эл. почты, если свопадает, то возвращает адрес, если нет то пустую строку
     * @param mixed $value
     * @return string
     */
    static public function EMail($value) {
	$value = trim((string)$value);
	return preg_match('/^[\w-.]+?@[\w-.]+?\.\w+$/m', $value) ? $value : '';
    }
    /**
     * Преобразует значение в вещественному числу
     * @param mixed $value
     * @param int $decimals знков вещественной части
     * @return float
     */
    static public function Float($value,$decimals=2) {
	return number_format(floatval(str_replace([' ',','],['','.'],(string)$value)),$decimals,'.','') ;
    }
    /**
     * Преобразует значение в объект времени/даты, также можно установить любое значение даты 
     * @param mixed $value
     * @param int $d установить значение дня, либо null, если оставить текущим
     * @param int $m установить значение месяца, либо null, если оставить текущим
     * @param int $Y установить значение года, либо null, если оставить текущим
     * @param string $Format предполагаемый формат, даты и времени, по умолчанию 'Y-m-d'
     * @return DateTime
     */
    static public function Date($value,$d=null,$m=null,$Y=null,$Format='Y-m-d') {
	$value = is_null($value) ? date($Format) : (string)$value;
	$date = DateTime::createFromFormat($Format, $value);
	!(is_null($d) || is_null($m) || is_null($Y)) && $date->setDate(intval($Y?:$date->format('Y')),intval($m?:$date->format('m')),intval($d?:$date->format('d')));
	return $date;
    }
    
    /**
     * Преобразует значение в объект времени/даты, также можно установить любое значение даты 
     * @param mixed $value
     * @param int $H установить значение часа, либо null, если оставить текущим
     * @param int $i установить значение минуты, либо null, если оставить текущим
     * @param int $d установить значение дня, либо null, если оставить текущим
     * @param int $m установить значение месяца, либо null, если оставить текущим
     * @param int $Y установить значение года, либо null, если оставить текущим
     * @param string $Format предполагаемый формат, даты и времени, по умолчанию 'Y-m-d'
     * @return DateTime
     */
    static public function DateTime($value,$H=null,$i=null,$d=null,$m=null,$Y=null,$Format='Y-m-d H:i') {
	$value = is_null($value) ? date($Format) : (string)$value;
	$date = DateTime::createFromFormat($Format, $value);
	!(is_null($d) || is_null($m) || is_null($Y)) && $date->setDate(intval($Y?:$date->format('Y')),intval($m?:$date->format('m')),intval($d?:$date->format('d')));
	!(is_null($H) || is_null($i)) && $date->setTime(intval($H?:$date->format('H')),intval($i?:$date->format('i')));
	return $date;
    }
    /**
     * Проверяет значение на соотвествие номеру телефона и кодам разрешенных операторов $OpCodes, в случае 
     * успеха возвращает значение в формате ddddddddddddd (строка из цифр)
     * @param mixed $value
     * @param array $OpCodes коды разрешенных операторов, либо пусто, для любых
     * @return string
     */
    static public function Phone($value,$OpCodes=['37529','37533','37525','37544','37517','7','370']) {
	return preg_match('/^('.implode('|', $OpCodes).')\d{6,20}/m', self::Digits($value),$m) ? $m[0] : '';
    }
    /**
     * Возвращает значение $needle, в случае если оно находится в списке разрешенных $Haystack, либо значение $Default
     * @param mixed $needle
     * @param array $Haystack список разрешенных значений
     * @param mixed $Default значение по умолчанию
     * @return mixed
     */
    static public function Enum($needle,$Haystack,$Default='') {
	return in_array($needle, $Haystack) ? $needle : $Default;
    }
    /**
     * Генерирует стойкий пароль заданной длины
     * @param int $Length количество символов в пароле
     * @param bool $UpperCase использовать символы в верхнем регистре
     * @param bool $LowerCase использовать символы в нижнем регистре
     * @param bool $Digits использовать цифры
     * @return string
     */
    static public function Password($Length,$UpperCase=true,$LowerCase=true,$Digits=true) {
        static $aUpper = 'QAZWSXEDCRFVTGBYHNUJMIKOLP', $aLower='qazwsxedcrfvtgbyhnujmikolp', $aDigits='09876543210987654321098765';
        $Alphabet = str_split(''.($UpperCase ? $aUpper : '').($LowerCase ? $aLower : '').($Digits ? $aDigits : '').(!$UpperCase && !$LowerCase && !$Digits ? $aLower:''));

        $AlphabetLength = count($Alphabet)-1;
        $Password = '';
	while((--$Length)>=0) {
            $Password .= $Alphabet[rand(0, $AlphabetLength)];
        }
        return $Password;
    }
    /**
     * Проверяет, я вляется ли запрос сабмитом данных, если я вляется, то может выполнить метод $On, либо возвращает значение $On
     * <code>
     * \Form::Is('add-user',function(){
     *	    User::Add($_POST['add-user']);
     *	    $this->Location('/');
     * });
     * </code>
     * @param string $Action
     * @param type $On
     * @return mixed
     */
    static public function Is($Action,$On=true) {
	return isset($_REQUEST[$Action]) ? (is_callable($On) ? call_user_func_array($On,array_slice(func_get_args(), 2)) : $On) : false;
    }
    /**
     * Преобразует значение в строку
     * @param mixed $value
     * @param int $Length максимальное количество символов
     * @param bool $Trim trim
     * @return string
     */
    static public function Text($value,$Length=0, $Trim = true) {
	$Trim && ($value = trim((string) $value));
	$Length && ($value = mb_substr($value, 0, $Length));
	return (string) $value;
    }
    /**
     * Извлекает из URL домен
     * @param string $value
     * @return string
     */
    static public function Domain($value,&$proto=null) {
        $proto = preg_match('%^\s*https://%i', (string) $value) ? 'https':'http';
	return preg_replace('%^\s*(?:.*?://)?([^/?#]+).*$%m', '\1', (string) $value);
    }
    /**
     * Преобразует строку в URL
     * @param string $value
     * @param string $Proto протокол, в случае отсутствия его
     * @return string
     */
    static public function Url($value, $Proto = 'http://') {
	return preg_match('%^\s*(\w+://)?(\S+)%m', (string) $value, $m) ? ((!empty($m[1]) ? $m[1] : $Proto ) . $m[2]) : '';
    }
    /**
     * Разбивает URL на составляющие
     * @param string $Url
     * @param bool $ParseQuery преобразует query в массив параметров
     * @param bool $ReturnAllParts $ReturnAllParts вернуть все элементы URL, даже если они пустые
     * @return [scheme,user,pass,host,path,query,fragment,0=>scheme,1=>user,2=>pass,3=>host,4=>path,5=>query,6=>fragment]
     */
    static public function UrlParse($Url,$ParseQuery=false,$ReturnAllParts=true) {
	$__ = empty($Url) ? [] : parse_url($Url);
	if($ParseQuery) {
	    if(isset($__['query']) && !empty($__['query'])) {
		$ParseQuery && parse_str($__['query'],$QueryParams);
	    }
	    else {
		$QueryParams = [];
	    }
	    $QueryParams = array_map('urldecode', $QueryParams);
	}
	else {
	    $QueryParams = isset($__['query']) ? $__['query'] : '';
	}
	$QueryParams = 
	$UrlParts = [
	    0=>isset($__['scheme']) ? $__['scheme'] : '',
	    1=>isset($__['user']) ? $__['user'] : '',
	    2=>isset($__['pass']) ? $__['pass'] : '',
	    3=>isset($__['host']) ? $__['host'] : '',
	    4=>isset($__['path']) ? $__['path'] : '',
	    5=>$QueryParams,
	    6=>isset($__['fragment']) ? $__['fragment'] : '',
	];
	$UrlParts['scheme'] =&  $UrlParts[0];
	$UrlParts['user'] =&  $UrlParts[1];
	$UrlParts['pass'] =&  $UrlParts[2];
	$UrlParts['host'] =&  $UrlParts[3];
	$UrlParts['path'] =&  $UrlParts[4];
	$UrlParts['query'] =&  $UrlParts[5];
	$UrlParts['fragment'] =&  $UrlParts[6];
	
	return $ReturnAllParts ? $UrlParts : array_filter($UrlParts);
    }
    /**
     * Собирает URL из параметров, либо заменяет их
     * @param string $Url
     * @param array $Query
     * @param type $Fragment
     * @param type $Path
     * @param type $Host
     * @param type $Scheme
     * @param type $User
     * @param type $Pass
     * @return string
     */
    static public function UrlBuild($Url,array $Query,$Fragment=false,$Path=false,$Host=false,$Scheme=false,$User=false,$Pass=false) {
	
	$UrlParts = self::UrlParse($Url,true);
	$Scheme = empty($Scheme) ? $UrlParts['scheme'] : $Scheme;
	$Host = empty($Host) ? $UrlParts['host'] : $Host;
	$User = empty($User) ? $UrlParts['user'] : $User;
	$Pass = empty($Pass) ? $UrlParts['pass'] : $Pass;
	$Path = empty($Path) ? $UrlParts['path'] : '/'.ltrim($Path,'\\/');
	$Query = array_merge($UrlParts['query'],$Query);
	$Fragment = empty($Fragment) ? $UrlParts['fragment'] : $Fragment;
	
	$UserPass = (empty($User) && empty($Pass) ? '' : (empty($Pass) ? "$User@" : "$User:$Pass@" ) );
	
	return ((empty($Scheme) && empty($Host) || empty($Host)) ? '' : ( (empty($Scheme) ? '//' : "$Scheme://" ) . $UserPass . $Host )) . 
		$Path . (!empty($Query) ? ('?'.http_build_query($Query, '_')) : '') . (!empty($Fragment) ? ('#'.$Fragment) : '');
    }
    /**
     * Сформировать список загруженных файлов
     * @param array $value $_FILES['file']
     * @return array
     */
    static public function Files($value) {
	$Files = [];
	if(is_array($value) && !empty($value)) {
	    if (isset($value['name']) && !is_array($value['name'])) {
		$Files[] = $value;
	    }
	    else {
		for ($i = 0; $i < count($value['name']); $i++) { 
		    $Files[] = ['name'=>$value['name'][$i],'size'=>$value['size'][$i],'type'=>$value['type'][$i],'error'=>$value['error'][$i],'tmp_name'=>$value['tmp_name'][$i]];
		}
	    }
	}
	
	return $Files;
    }
    /**
     * Загрузить полученные файлы в папку, создается два файла один с содержимым, второй с описанием файла. В качестве имени файла используется уникальный ID
     * @param array $Files массив файлов @see(\Form::Files(...))
     * @param string $PathTo Путь, куда будут перемещены загруженный файлы
     * @param int $MaxFileSize максимальный размер одного файла, если превышает, то файл не будет загружен, если = 0, то любой размер
     * @param array $Ext массив разрешенных расширений файлов (в lowercase)
     * @param array $Mime массив разрешенных типов mime (в lowercase)
     * @return array Список файлов, в случае если файл загружен успешно у него пояаится атрибут $file['id'] с уникальным именем
     */
    static public function Upload(array $Files,$PathTo,$MaxFileSize=0,array $Ext=[],array $Mime=[]) {
	$PathTo = rtrim($PathTo,'/\\').DIRECTORY_SEPARATOR;
	File::MkDir($PathTo,true);
	foreach ($Files as &$file) {
	    if($file['error']==0 && $MaxFileSize && $file['size'] > $MaxFileSize) { $file['error'] = UPLOAD_ERR_INI_SIZE; continue; }
	    if($file['error']==0 && !empty($Ext) && !in_array(File::Type($file['name']), $Ext)) { $file['error'] = UPLOAD_ERR_EXTENSION; continue; }
	    if($file['error']==0 && !empty($Mime) && !in_array(File::Type($file['type']), $Mime)) { $file['error'] = UPLOAD_ERR_EXTENSION; continue; }
	    
	    $destFileName = time().'.'.rand(10000,90000).sha1($file['name']);
	    
	    if ($file['error'] != 0 || !is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], File::FullPath($PathTo).$destFileName)) { $file['error'] = $file['error']?:UPLOAD_ERR_CANT_WRITE; continue;}
	    
	    $file['id'] = $destFileName;
	    
	    \File::Write($PathTo.'.'.$destFileName, $file, FILE_JSON|FILE_GZIP);
	}
	return $Files;
    }
    
    static public function UploadData($Data,$PathTo,$Name=NULL,$Mime=NULL) {
	$PathTo = rtrim($PathTo,'/\\').DIRECTORY_SEPARATOR;
	File::MkDir($PathTo,true);
        $destFileName = !empty($Name) ? $Name : (time().'.'.rand(10000,90000).sha1($Data));
        $Files = [['type'=>$Mime,'name'=>$destFileName,'error'=>0,'size'=>strlen($Data),'id'=>$destFileName]];
        \File::Write($PathTo.$destFileName, $Data);
        \File::Write($PathTo.'.'.$destFileName, $Files[0], FILE_JSON|FILE_GZIP);
	return $Files;
    }
    
    /**
     * Загрузить полученные файлы в папку, создается два файла один с содержимым, второй с описанием файла. В качестве имени файла используется уникальный ID
     * @param array $Files массив файлов @see(\Form::Files(...))
     * @param string $PathTo Путь, куда будут перемещены загруженный файлы
     * @param int $MaxFileSize максимальный размер одного файла, если превышает, то файл не будет загружен, если = 0, то любой размер
     * @param array $Ext массив разрешенных расширений файлов (в lowercase)
     * @param array $Mime массив разрешенных типов mime (в lowercase)
     * @return array Список файлов, в случае если файл загружен успешно у него пояаится атрибут $file['id'] с уникальным именем
     */
    static public function UploadImages(array $Files,$PathTo,$MaxFileSize=0,array $Ext=[],array $Mime=[],$ResizeToWidthHeight=false) {
	$PathTo = rtrim($PathTo,'/\\').DIRECTORY_SEPARATOR;
	File::MkDir($PathTo,true);
	foreach ($Files as &$file) {
	    if($file['error']==0 && $MaxFileSize && $file['size'] > $MaxFileSize) { $file['error'] = UPLOAD_ERR_INI_SIZE; continue; }
	    if($file['error']==0 && !empty($Ext) && !in_array(File::Type($file['name']), $Ext)) { $file['error'] = UPLOAD_ERR_EXTENSION; continue; }
	    if($file['error']==0 && !empty($Mime) && !in_array(File::Type($file['type']), $Mime)) { $file['error'] = UPLOAD_ERR_EXTENSION; continue; }
	    
	    $destFileName = time().'.'.rand(10000,90000).sha1($file['name']);
	    
	    if ($file['error'] != 0 || !is_uploaded_file($file['tmp_name']) || !self::move_uploaded_image_file($ResizeToWidthHeight,$file['type'],$file['tmp_name'], File::FullPath($PathTo).$destFileName)) { $file['error'] = $file['error']?:UPLOAD_ERR_CANT_WRITE; continue;}
	    
	    $file['id'] = $destFileName;
	    
	    \File::Write($PathTo.'.'.$destFileName, $file, FILE_JSON|FILE_GZIP);
	}
	return $Files;
    }
    
    static private function move_uploaded_image_file ($MaxWidthAndHeight,$Mime,$TempFile,$DestFile) {
        $IsJpeg = false;
        if(!is_uploaded_file($TempFile)) return false;
        switch(strtolower($Mime)) {
            case 'image/jpeg': case 'image/jpg':
                $img = imagecreatefromjpeg( $TempFile );
                $IsJpeg = true;
            break;
            case 'image/png':
                $img = imagecreatefrompng( $TempFile );
                imagesavealpha($img,true); 
                imagealphablending($img, true); 
            break;
            case 'image/gif':
                $img = imagecreatefromgif( $TempFile );
                imagesavealpha($img,true); 
            break;
            default:
                return false;
        }
        
        $width = imagesx( $img );
        $height = imagesy( $img );
        
        if($MaxWidthAndHeight && ($width > $MaxWidthAndHeight || $height > $MaxWidthAndHeight)) {
            if($width>$height) {
                $new_width = $MaxWidthAndHeight;
                $new_height = floor( $height * ( $MaxWidthAndHeight / $width ) );
            }
            else {
                $new_height = $MaxWidthAndHeight;
                $new_width = floor( $width * ( $MaxWidthAndHeight / $height ) );
            }
            $tmp_img = imagecreatetruecolor( $new_width, $new_height );
            if($IsJpeg) {
                imagealphablending($tmp_img, true); 
                $transparent = imagecolorallocatealpha( $tmp_img, 0, 0, 0, 127 ); 
                imagefill( $tmp_img, 0, 0, $transparent ); 
            }

            imagesavealpha($tmp_img,true); 
            imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
            
            switch(strtolower($Mime)) {
                case 'image/jpeg': case 'image/jpg':
                    imagejpeg( $tmp_img, $DestFile,100 );
                break;
                case 'image/png':
                    imagepng( $tmp_img, $DestFile);
                break;
                case 'image/gif':
                    imagegif( $tmp_img, $DestFile);
                break;
                default:
                    return false;
            }
        }
        else {
            imagedestroy($img);
            move_uploaded_file($TempFile, $DestFile);
        }
        return true;
    }
    
    /**
     * Получить информацию о загруженном файле
     * @param string $FileId ID файла. @see(\Form::Upload(...))
     * @param string $Path путь, куда был загружен файл
     * @param array $File ссылка на массив, куда будут возвращена информация о файле в случае успеха
     * @return array|boolean массив с информацией о загруженном файле или false
     */
    static public function FileInfo($FileId,$Path,&$File=[]) {
	$Path = rtrim($Path,'/\\').DIRECTORY_SEPARATOR;
	$FileId = preg_replace('%[^\w\.-]+%', '', $FileId);
	if(File::Exist($FileName = $Path.'.'.$FileId)) {
	    $File = \File::Read($FileName, FILE_JSON|FILE_GZIP);
	    $File['path'] = $Path.$FileId;
            $File['ext'] = pathinfo($File['name'],PATHINFO_EXTENSION);
	    return $File;
	}
	return false;
    }
    /**
     * Скачать, ранее загруженный файл. В случае успеха или не успеха, выполнение скрипта прекращается
     * @param string $FileId
     * @param string $Path
     * @param array $File
     */
    static public function Download($FileId,$Path,&$File=[]) {
	if(self::FileInfo($FileId, $Path,$File) !== false) {
	    \File::DownloadFile($File['path'], $File['type'],["Content-Disposition: attachment;filename=\"".$File['name']."\";filename*=utf-8''".  rawurlencode($File['name'])]);
	}
	exit;
    }
    
    /**
     * Скачать, ранее загруженный файл. В случае успеха или не успеха, выполнение скрипта прекращается
     * @param string $FileName
     * @param string $Mime
     * @param mixed $Content
     */
    static public function DownloadContent($FileName,$Mime,$Content) {
        \File::DownloadContent($Content, $Mime,["Content-Disposition: attachment;filename=\"".($FileName)."\";filename*=utf-8''".rawurlencode($FileName)]);
	exit;
    }
    
    /**
     * Генерируется cтойкий пароль
     * @param int $length длина праоля
     * @param string $chars алфавит пароля
     * @return string
     */
    public static function GeneratePassword($length = 8, $chars = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ')
    {
        #36 ^ 6 = 2 176 782 336 unique combinations minimum
        $chars = count_chars($chars,$mode = 3); #gets unique chars
        $len = strlen($chars);
        mt_srand((double) microtime() * 1000000);  #initialize
        for ($password = '', $i = 0; $i < $length; $i++) {
            $password .= substr($chars,mt_rand(0,$len - 1),1);
        }
        return $password;
    }

    static public function Value(&$Array,$Name,$Type,$Default='',$Format=false,$Required=false,$FailedText='') {
        
        return new FormInputValue(isset($Array[$Name]) ? $Array[$Name] : NULL,$Type,$Default,$Format,$Required,$FailedText);
    }
    static public function Assert() {
        $Exception = FALSE;
        $InvalidFields = [];
        foreach (func_get_args() as $arg) {
            (is_a($arg,'FormInputValue') && !$arg->Assert()) && $InvalidFields[] = $arg->FailedText;
            $Exception |= (is_a($arg,'FormInputValue') && !$arg->Assert()) || (is_scalar($arg) && empty($arg));
        }
        if($Exception) {throw new FormException('form_validate_exception', $InvalidFields);}
        return !$Exception;
    }
    const Int = 1;
    const Decimal = 2;
    const String = 3;
    const Text = 4;
    const Html = 5;
    const File = 6;
    const Files = 7;
    const Image = 8;
    const Json = 9;
    const Date = 10;
    const DateTime = 11;
    const Time = 12;
    const Enum = 13;
    const Url = 14;
    const Email = 15;
    const Domain = 16;
    const Phone = 17;
    const Digits = 18;
    const AlphaDigits = 19;
    const dmY = 20;
}

class FormException extends Exception {
    public $fields;
    public function __construct($message,$fileds,$code=0) {
        $this->fields = $fileds;
        parent::__construct($message, $code);
    }
    public function getFields() {
        return $this->fields;
    }
}

class FormInputValue {
    public $Value,$Type,$Default,$Format,$Required,$FailedText;
    public function __construct($Value,$Type,$Default,$Format,$Required,$FailedText) {
        $this->Value = $this->Sanitize($Value,$Type,$Format);
        $this->Type = $Type;
        $this->Default = $Default;
        $this->Format = $Format;
        $this->Required = $Required;
        $this->FailedText = $FailedText;
    }
    public function __toString() {
        return !empty($this->Value) ? (string)$this->Value : (string)$this->Default;
    }
    public function Assert() {
        return (($this->Required && !empty($this->Value)) || !$this->Required) ;
    }
    private function dmY($value) {
        return preg_match('/(\d{1,2}).(\d{1,2}).(\d{4})/', $value,$m) ? sprintf('%d-%02d-%02d',intval($m[3]),intval($m[2]),intval($m[1])) : null;
    }
    private function date($value,$format) {
        return preg_match('/(\d{4}-\d{1,2}-\d{1,2})/', $value,$m) ? $m[1] : null;
    }
    private function datetime($value) {
        return preg_match('/(\d{4}-\d{1,2}-\d{1,2}) (\d{1,2}:\d{1,2})(:\d{1,2})?/i', $value,$m) ? ($m[1].$m[2].(isset($m[3])?$m[3]:':00')) : null;
    }
    private function time($value) {
        return preg_match('/(\d{1,2}:\d{1,2})(:\d{1,2})?/i', $value,$m) ? ($m[1].(isset($m[2])?$m[2]:':00')) : null;
    }
    private function digits($value) {
        return !empty($value) ? preg_replace('/\D+/', '', $value) : null;
    }
    private function alphadigits($value) {
        return !empty($value) ? preg_replace('/[^\d\w-=+.]+/', '', $value) : null;
    }
    private function image($File,$params,$mime=['image/png','image/jpeg','image/jpg','image/gif']) {
        if($File['error']==0 && $File['size']!==0 && in_array($File['type'],$mime)) {
            return \Image\Thumbs::Upload(
                    $File['tmp_name'],$File['type'],isset($params['name']) ? $params['name'] : $File['name'],
                    $params['path'],isset($params['max']) ? $params['max'] : 0);
        }
        return null;
        
    }
    private function Sanitize(&$value,&$type,&$format) {
        switch($type) {
            case \Form::Int: return \Form::Int($value);
            case \Form::Decimal: return \Form::Float($value,3);
            case \Form::Digits: return \Form::String($this->digits($value),$format);
            case \Form::AlphaDigits: return \Form::String($this->alphadigits($value),$format);
            case \Form::String: return \Form::String($value,$format);
            case \Form::Text: return \Form::String($value,$format);
            case \Form::Html: return \Form::Text($value,$format);
            case \Form::Date: return $this->date($value);
            case \Form::dmY: return $this->dmY($value);
            case \Form::DateTime: return $this->datetime($value);
            case \Form::Time: return $this->time($value);
            case \Form::Enum: return \Form::Enum($value,$format);
            case \Form::Phone: return \Form::Phone($value,$format);     
            case \Form::Url: return \Form::Url($value,$format);         
            case \Form::Email: return \Form::EMail($value);
            case \Form::Domain: return \Form::Domain($value);
            case \Form::Image: return $this->image($value,$format);
        }
    }
}
