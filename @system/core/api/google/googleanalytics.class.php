<?php
namespace Api;
/**
 * @method array management->accountSummaries() Description
 */
class GoogleAnalytics extends \Api\Google
{
    private $AccessToken = false;
    
    const ApiUrl = 'https://www.googleapis.com/analytics/v3';
    
    const DefaultScope = 'https://www.googleapis.com/auth/analytics.readonly';

    public function Authorize($RedirectUrl,$Scope=self::DefaultScope,$State='',$ReNew=false) {
        return parent::Authorize($RedirectUrl, $Scope, $State, $ReNew);
    }
    
    /**
     * @param type $Token
     * @return \Api\GoogleAnalytics
     */
    public function SetAccessToken($Token) {
        $this->AccessToken = is_array($Token) && isset($Token['access_token']) ? $Token['access_token'] : $Token;
        return $this;
    }
    
    public function __get($method) {
        switch ($method) {
            case 'management':
                return new ApiRestObject([
                    'accountSummaries'=>function(){
                        return $this->__call_api_method('/management/accountSummaries/');
                    },
                    'accounts'=>function(){
                        return $this->__call_api_method('/management/accounts/');
                    }
                ]);
        }
    }
    
    private function __call_api_method($Url,$Params=[]) {
        return $this->CallJson(self::ApiUrl.$Url,['access_token'=>$this->AccessToken]);
    }
}

class ApiRestObject {
    private $ApiMethodList;
    public function __construct($MethodsList) {
        $this->ApiMethodList = $MethodsList;
    }
    public function __get($object) {
        return isset($this->ApiMethodList[$object]) && !is_callable($this->ApiMethodList[$object]) ? new ApiRestObject($this->ApiMethodList[$object]) : null;
    }
    
    public function __call($method,$args) {
        return is_callable($this->ApiMethodList[$method]) ? call_user_func_array($this->ApiMethodList[$method], $args) : null;
    }
}