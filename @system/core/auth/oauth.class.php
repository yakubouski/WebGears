<?php
namespace Auth;
class OAuth 
{
    protected function Location($Url,$Params=[]) {
	$Params = array_filter($Params, function($v) { return !empty($v);  });
	ob_end_clean();
	header('Location: '.$Url.(empty($Params) ? '':('?'.http_build_query($Params)))); exit;
    }
    protected function ServerUrl($Params) {
        $Params = is_array($Params) ? 
            ('/?'.http_build_query(array_filter($Params, function($v) { return !empty($v);  }))) : 
            ('/'.ltrim($Params,'/'));
	return (isset($_SERVER['HTTPS']) ? 'https://':'http://') . $_SERVER['HTTP_HOST'] . $Params;
    }
    
    protected function Call($Url,$Params,&$Headers=[]) {
	$Params = array_filter($Params, function($v) { return !empty($v);  });
	$result = file_get_contents($Url.(empty($Params)?'':('?'.http_build_query($Params))));
	$Headers = $http_response_header;
	return $result;
    }
    
    protected function CallPost($Url,$Params,&$Headers=[]) {
	$Params = array_filter($Params, function($v) { return !empty($v);  });
	$Context = stream_context_create([
	    'http' => [
		'method' => 'POST',
		'header' => "Content-type: application/x-www-form-urlencoded",
		'content' => http_build_query($Params),
	    ]
	]);
	$result = file_get_contents($Url,false,$Context);
	$Headers = $http_response_header;
	return $result;
    }
    
    protected function CallGet($Url,$Params) {
	if(($Result = $this->Call($Url, $Params))) {
	    parse_str($Result,$Args);
	    return $Args;
	}
	return false;
    }
    
    protected function CallJson($Url,$Params) {
	if(($Result = $this->Call($Url, $Params))) {
	    return json_decode($Result,true);
	}
	return false;
    }
    
    protected function CallPostJson($Url,$Params) {
	if(($Result = $this->CallPost($Url, $Params))) {
	    return json_decode($Result,true);
	}
	return false;
    }
    
    protected function Credential($Data=NULL) {
        $OAuthClass = get_called_class();
        if(!is_null($Data)) {
            $_SESSION["@$OAuthClass"] = $Data;
        }
        return isset($_SESSION["@$OAuthClass"]) ? $_SESSION["@$OAuthClass"] : NULL;
    }


    /**
     * @return \Auth\Facebook
     */
    static public function Provider() {
        static $Providers;
        $OAuthClass = get_called_class();
        !isset($Providers[$OAuthClass]) && ($Providers[$OAuthClass] = new $OAuthClass );
        return $Providers[$OAuthClass];
    }
}
