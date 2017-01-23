<?php
namespace Auth;
class Credential 
{
    private $Data;
    public $Global;
    public $Provider;
    public $Credential;
    private function __construct($Data,$Provider,$CredentialName,$Global) {
        $this->Data = $Data;
        $this->Global = $Global;
        $this->Provider = $Provider;
        $this->Credential = $CredentialName;
    }
    public function __get($name) {
        return isset($this->Data[$name]) ? $this->Data[$name] : null;
    }
    public function __set($name,$value) {
        $this->Data[$name] = $value;
    }
    public function __unset($name) {
        unset($this->Data[$name]);
    }
    public function __isset($name) {
        return isset($this->Data[$name]);
    }
    public function UpdateAccessToken($Token) {
        $this->__set('access_token',$Token);
        self::Save($this->Data, $this->Provider, $this->Credential,$this->Global);
    }
    static public function Save($Data,$Provider,$Credential,$Global=false) {
        $Path = ($Global?APP_BASE_DIRECTORY:\Application::$directoryVirtualBase).'.credentials/';
        if(($res = @mkdir($Path,0774,true))) {
            @file_put_contents($Path.'.htaccess', "order deny,allow\r\ndeny from all");
	}
        @mkdir($Path.'/'.$Provider,0774,true);
        return @file_put_contents($Path.$Provider.DIRECTORY_SEPARATOR.$Credential.'.credential',gzencode(json_encode($Data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),9));
    }
    
    /**
     * @param string $Provider
     * @param string $CredentialName
     * @param bool $Global глобальный путь
     * @return \Auth\Credential
     */
    static public function Get($Provider,$CredentialName,$Global=false) {
        $Path = ($Global?APP_BASE_DIRECTORY:\Application::$directoryVirtualBase).'.credentials/';
        $Data = @file_get_contents($Path.$Provider.DIRECTORY_SEPARATOR.$CredentialName.'.credential');
        return new Credential(!empty($Data) ? json_decode(gzdecode($Data),true) : [],$Provider,$CredentialName,$Global);
    }
}
