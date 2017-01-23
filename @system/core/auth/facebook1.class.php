<?php
namespace Auth;
require_once 'oauth.class.php';

class Facebook extends OAuth
{
    const UrlOAuth = 'https://www.facebook.com/dialog/oauth';
    const UrlAccessToken = 'https://graph.facebook.com/oauth/access_token';
    const UrlGraphMe = 'https://graph.facebook.com/v2.4/me';
    
    const DefaultScope = 'email,user_about_me,user_birthday,user_events';


    public function Validate() {
	$userAuth = $this->getSession(self::AuthID);
	return $userAuth;
    }
    
    /**
     * @param string $RedirectUrl
     * @param string $AuthState
     * @param string $AuthScope - read_stream, friends_likes,email,user_about_me
     * @return bool
     */
    public function Authorize($AuthAppId,$AuthSecret,$RedirectUrl='',$AuthState='',$AuthScope=self::DefaultScope) {
	session_regenerate_id(true);
        /*
	$this->setSession(Auth::AuthTag, Auth::Facebook);
	
	if(($userAuthToken = $this->getCookie(self::AuthID, self::$authAppSecret))) {
	    $AuthData = new Object();
	    $this->Me($userAuthToken,$AuthData);
	    $this->setSession(self::AuthID, $AuthData);
	    
	    return $this->OnAuthorize($AuthData,$this);
	}
	else {*/
	    $this->Location(self::UrlOAuth, [
		'client_id' =>  $AuthAppId,
		'redirect_uri' => $this->ServerUrl($RedirectUrl),
		'state' => $AuthState,
		'scope' => $AuthScope,
	    ]);
	#}
    }
    
    private function Me($AccessToken,&$Me) {
	if(($Data = $this->CallJson(self::UrlGraphMe, [
	    'access_token' => $AccessToken,
	]))) {
            $Me = array_merge($Me,$Data);
	    return TRUE;
	}
	return FALSE;
    }
    public function Api($Fields) {
        if(($Credential = $this->Credential())) {
            return $this->CallJson(self::UrlGraphMe,['access_token'=>$Credential['access_token'],'fields'=>implode(',',$Fields)]);
        }
        return false;
    }

    /**
     * Завершить процесс авторизации
     * @param array $Params
     * @return boolean
     */
    public function Processing($AuthAppId,$AuthSecret,$CompleteUrl,$ContinueUrl=null) {
	if(isset($_GET['error_code']) || isset($_GET['error'])) {
	    \Exception('Facebook authorization error: '.$_GET['error_message']);
	    return false;
	}
	elseif(isset($_GET['code']) && preg_match('/[\w-]+/i',$_GET['code'])) {
	    if(($Result = $this->CallGet(self::UrlAccessToken, [
		'client_id' => $AuthAppId,
		'client_secret' => $AuthSecret,
		'redirect_uri' => $this->ServerUrl($CompleteUrl),
		'code' => trim($_GET['code'])
	    ]))) {
                $Credential = $Result;
                if($this->Me($Credential['access_token'],$Credential)) {
                    $this->Credential($Credential);
                    !is_null($ContinueUrl) && $this->Location($ContinueUrl);
                    return true;
                }
	    }
	    \Exception('Facebook invalid code');
	}
    }
}
