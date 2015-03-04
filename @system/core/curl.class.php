<?php


/**
 * @method CUrlOptions CURLOPT_AUTOREFERER(bool $Enable) Description
 * @method CUrlOptions CURLOPT_BINARYTRANSFER(bool $Enable) Description
 * @method CUrlOptions CURLOPT_FOLLOWLOCATION(bool $Enable) Description
 * @method CUrlOptions CURLOPT_FORBID_REUSE(bool $Enable) Description
 * @method CUrlOptions CURLOPT_FRESH_CONNECT(bool $Enable) Description
 * @method CUrlOptions CURLOPT_HEADER(bool $Enable) Description
 * @method CUrlOptions CURLOPT_HEADER_OUT(bool $Enable) Description
 * @method CUrlOptions CURLOPT_RETURNTRANSFER(bool $Enable) Description
 * @method CUrlOptions CURLOPT_VERBOSE(bool $Enable) Description
 * @method CUrlOptions CURLOPT_ENCODING(string $Encoding) Description
 * @method CUrlOptions CURLOPT_COOKIE(string $Cookie) Description
 * @method CUrlOptions CURLOPT_REFERER(string $Referer) Description
 * @method CUrlOptions CURLOPT_USERAGENT(string $UserAgent) Description
 * @method CUrlOptions CURLOPT_USERPWD(string $User,string $Password)
 * @method CUrlOptions CURLOPT_HTTPAUTH(int $Auth = CURLAUTH_NONE | CURLAUTH_BASIC | CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM )
 * @method CUrlOptions CURLOPT_NOBODY(bool $Enable) Description
 * @method CUrlOptions CURLOPT_HTTPHEADER(array $Headers) Description
 * @method CUrlOptions CURLOPT_HTTP_VERSION(int $Version) Description
 * 
 * 
 * 
 * 
 * @method CUrlOptions SET_COMPRESSION($Type='gzip') Description
 * @method CUrlOptions SET_COOKIE_FILE(string $CookieFile) Description
 * @method CUrlOptions SET_TIMEOUT(int $Request,int $Connection) Description
 * @method CUrlOptions SET_PROXY(string $Ip,int $Port,string $AuthLogion='',string $AuthPwd='') Description
 * @method CUrlOptions SET_SSL(string $CertFile='',string $PrKeyFile='',string $CAFile='') Description
 * 
 * 
 * @method string GET_PROXY_IP() Description
*/
abstract class CUrlOptions {
    public function __call($name, $arguments) {
	switch ($name) {
	    case 'SET_SSL': 
		@list($CertFile,$PrKeyFile,$CAFile) = $arguments;
		if(!empty($CertFile)) {
		    $this->SetOption(CURLOPT_SSL_VERIFYPEER , true)->SetOption(CURLOPT_SSL_VERIFYHOST,true)->
			    SetOption(CURLOPT_SSLCERT,$CertFile);
		    !empty($PrKeyFile) && $this->SetOption(CURLOPT_SSLKEY , $PrKeyFile);
		    !empty($CAFile) && $this->SetOption(CURLOPT_CAINFO , $CAFile);
		}
		else {
		    $this->SetOption(CURLOPT_SSL_VERIFYPEER , false);
		}
		break;
	    case 'SET_COMPRESSION': $this->SetOption(CURLOPT_ENCODING , 'gzip'); break;
	    case 'SET_COOKIE_FILE': 
		$CookieFile = Application::Path($arguments[0]);
		Application::MkDir(dirname($CookieFile),true, 0774);
		$this->SetOption(CURLOPT_COOKIEFILE , $CookieFile)->SetOption(CURLOPT_COOKIEJAR,$CookieFile); 
		break;
	    case 'SET_TIMEOUT':
		$this->SetOption(CURLOPT_TIMEOUT , @intval($arguments[0]))->SetOption(CURLOPT_CONNECTTIMEOUT,@intval($arguments[1])); 
		break;
	    case 'GET_PROXY_IP':
		return isset($this->curlProxyIp) ? $this->curlProxyIp : $_SERVER['SERVER_ADDR'];
		break;
	    case 'SET_PROXY':
		@list($Ip,$Port,$Login,$Pwd) = $arguments;
		if(empty($Ip)) {
		    $this->curlProxyIp = $Ip;
		    $this->SetOption(CURLOPT_PROXY,false);
		}
		else {
		    $this->curlProxyIp = $Ip;
		    $this->SetOption(CURLOPT_PROXY,"$Ip:$Port");
		    (!empty($Login) && !empty($Pwd)) && $this->SetOption(CURLOPT_PROXYUSERPWD, "$Login:$Pwd");
		}
		break;
	    case 'CURLOPT_USERPWD':
		$this->SetOption(CURLOPT_USERPWD , !empty($arguments[0]) ? ($arguments[0].(!empty($arguments[1]) ? (':'.$arguments[1]):''))  :''); 
		break;
	    default:
		!is_null(constant($name)) && $this->SetOption(constant($name),$arguments[0]);
	}
	return $this;
    }
}

class CUrl extends CUrlOptions
{
    private $curlHandle;
    protected $curlFollowLocation;
    protected $curlEnableHeaders;
    protected $curlResponseHeaders;

    const AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6 ( .NET CLR 3.5.30729; .NET4.0E)';

    protected function handle()
    {
        if($this->curlHandle) return $this->curlHandle;
        $this->curlHandle = curl_init();
        return $this->curlHandle;
    }
    
    public function __construct($ReturnTransfer=true,$EnableHeader=true,$FollowLocation=true,$AutoReferer=true) {
	$this->CURLOPT_RETURNTRANSFER($ReturnTransfer)->CURLOPT_HEADER($EnableHeader)->CURLOPT_FOLLOWLOCATION($FollowLocation)->
		CURLOPT_AUTOREFERER($AutoReferer)->CURLOPT_USERAGENT(self::AGENT);
    }
    
    public function  __destruct()
    {
        if($this->curlHandle) curl_close($this->curlHandle);
    }

    protected  function SetOption($Option,$Value)
    {
        curl_setopt($this->handle(), $Option,$Value);
        return $this;
    }

    /**
     * Выполнить запрос GET
     * @param string $Url
     * @param array $Args
     * @return CUrlResult
     */
    public function GET($Url,$Args=[]) {
        !empty($Args) && $Url = Std::UrlBuild($Url,$Args);
	return $this->__request($Url, false, NULL);
    }
    /**
     * Выполнить запрос POST
     * @param string $Url
     * @param array $Args
     * @return CUrlResult
     */
    public function POST($Url,$Args) {
        return $this->__request($Url, true, $Args);
    }
    
    /**
     * Выполнить запрос HEAD
     * @param type $Url
     * @param array $Args
     * @return CUrlResult
     */
    public function HEAD($Url,$Args=[]) {
	return $this->CURLOPT_NOBODY(true)->GET($Url,$Args=[]);
    }
    
    /**
     * Получить содержимое страницы GET запросом
     * @param string $Url
     * @param array $Args
     * @return CUrlResult 
     */
    static public function Content($Url,$Args=[]) {
	$curl = new CUrl;
	return $curl->GET($Url,$Args);
    }


    protected function __request($Url,$IsPost,$PostData)
    {
        curl_setopt($this->handle(), CURLOPT_URL, $Url);
        
        if($IsPost)
        {
            curl_setopt($this->handle(), CURLOPT_POST, 1);
            curl_setopt($this->handle(), CURLOPT_POSTFIELDS, $PostData);
        }
        else
        {
            curl_setopt($this->handle(), CURLOPT_HTTPGET, 1);
        }
	return new CUrlResult(curl_exec($this->handle()),curl_errno($this ->handle()),curl_error($this ->handle()),curl_getinfo($this->handle()));
    }
}

/**
 * @property string $url - URL запроса
 * @property string $content_type -  тип контента ответа
 * @property string $charset - кодовая старница
 * @property string $mime - mime тип содержимого станицы (text/html)
 * @property int $http_code - код ответа сервера
 * @property int $header_size - размер всех заголовков ответа
 * @property int $request_size - размер запроса
 * @property int $filetime - время изменения файла
 * @property string $ssl_verify_result - резултата провери ssl сертификата
 * @property int $redirect_count - количество редиректов
 * @property float $total_time - общее время запроса
 * @property float $namelookup_time - время разрешения имени домена
 * @property float $connect_time - время подключения
 * @property float $pretransfer_time - общее время пере приемом данных
 * @property int $size_upload - выгружено байт
 * @property int $size_download - загружено байт
 * @property float $speed_download - скорость загрузки
 * @property float $speed_upload - скорость выгрузки
 * @property int $download_content_length - размер загруженного содержимого
 * @property int $upload_content_length - размер выгруженного содержимого
 * @property float $starttransfer_time - время начала передачи данных
 * @property float $redirect_time - общее время редиректов
 * @property int $redirect_url - url редиректа
 * @property int $primary_ip - ip адрес сервера
 * @property int $primary_port - порт сервера
 * @property int $local_ip - ip адрес клиента
 * @property int $local_port - локальный порт клиента
 * @property string $certinfo - информация о сертификате
 * @property string $request_header - заголовок запроса
 * @property CUrlHeaders $headers - зоголовок ответа
 * @property int $errno - код ошибкиCUrl, 0 - в случае успеха
 * @property int $error - расшифровка кода ошибки если не 0
 * @property string $content - содержимое ответа. возможно также приведение к типу string. (string)$Result
 */
abstract class CUrlInfo {
    public function __get($name) {	
	if($name == 'errno') return $this->resultCUrlErrNo;
	elseif($name == 'error') return $this->resultCUrlError;
	elseif($name == 'headers') return $this->resultHeader;
	elseif($name == 'charset') return $this->resultCharset;
	elseif($name == 'mime') return $this->resultMime;
	elseif($name == 'content') return $this->resultContent;
	elseif($name == 'curl') return $this->resultCUrlInfo;
	return @$this->resultCUrlInfo[$name];
    }
    
    public function __set($param,$value) {
	$this->{$param} = $value;
    }
}

class CUrlHeaders implements ArrayAccess,  Countable, IteratorAggregate {
    public $Headers;
    public function __construct($headers) {
	$this->Headers = [];
	if (!empty($headers) && preg_match_all('/^\s*(.*?):\s*(.*)\s*/m', $headers, $matches, PREG_SET_ORDER)) {
	    foreach ($matches as $hdr) {
		$option = mb_strtolower($hdr[1]);
		!isset($this->Headers[$option]) ? ($this->Headers[$option] = $hdr[2]) : 
		    (!is_array($this->Headers[$option]) ? ($this->Headers[$option] = [$this->Headers[$option],$hdr[2]]) : $this->Headers[$option][] = $hdr[2]);
	    }
	}
    }

    public function count() { return count($this->Headers); }
    public function getIterator() { return new ArrayIterator($this->Headers); }
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    public function offsetExists($offset) {
	return isset($this->Headers[mb_strtolower($offset)]);
    }
    public function offsetGet($offset) {
	$offset = mb_strtolower($offset);
	return isset($this->Headers[$offset]) ? $this->Headers[$offset] : false;
    }
}

class CUrlResult extends CUrlInfo {
    protected $resultContent;
    protected $resultCharset;
    protected $resultMime;
    protected $resultHeader;
    protected $resultCUrlErrNo;
    protected $resultCUrlError;
    protected $resultCUrlInfo;
    public function __construct($Content,$CUrlErrNo,$CUrlError,$CUrlInfo) {
	$this->resultCUrlInfo = $CUrlInfo;
	$this->resultCUrlErrNo = $CUrlErrNo;
	$this->resultCUrlError = $CUrlError;
	$this->resultMime = preg_match('/^\s*(.*?)\s*(?:;|$)/m', $this->content_type,$mime) ? $mime[1] : false;
	$this->resultCharset = preg_match('/charset\s*=\s*([\w-]+)/i', $this->content_type,$charset) ? $charset[1] : false;
	$this->resultHeader = new CUrlHeaders(mb_substr($Content, 0, $this->header_size));
	$this->resultContent = mb_substr($Content,$this->header_size);
    }
    public function __toString() { return (string)$this->resultContent; }
}
