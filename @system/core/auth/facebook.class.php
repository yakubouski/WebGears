<?php
namespace Auth;
require_once 'oauth.class.php';

class Facebook extends OAuth
{
    const UrlOAuth = 'https://www.facebook.com/dialog/oauth';
    const UrlAccessToken = 'https://graph.facebook.com/oauth/access_token';
    const UrlGraph = 'https://graph.facebook.com/v2.4/';
    const DefaultScope = 'email,user_about_me,user_birthday,user_events,public_profile';
    
    /**
     * @var \Auth\Credential 
     */
    private $Credential;

    public function __construct($CredentialName,$Global=false) {
        $this->Credential = Credential::Get('facebook', $CredentialName, $Global);
    }

    /**
     * @param string $CredentialName
     * @param bool $Global
     * @return \Auth\Facebook
     */
    static public function Provider($CredentialName='',$Global=false) {
        return new Facebook($CredentialName, $Global);
    }
    
    public function Authorize($RedirectUrl,$Scope=self::DefaultScope,$State='',$ReNew=false) {
        if(isset($this->Credential->APP_ID)) {
            if(isset($_GET['code'])) {
                if(($Token = $this->CallGet(self::UrlAccessToken, [
                    'client_id' => $this->Credential->APP_ID,
                    'client_secret' => $this->Credential->APP_SECRET,
                    'redirect_uri' => $RedirectUrl,
                    'code' => trim($_GET['code']),
                ]))) { 
                    return $Token; 
                }
                else {
                    return false;
                }
            }
            else {
                $this->Location(self::UrlOAuth, [
                    'client_id'=>$this->Credential->APP_ID,
                    'redirect_uri'=>$RedirectUrl,
                    'response_type'=>'code',
                    'scope'=>$Scope,
                    'display'=>'popup',
                    'state'=>$State]);
            }
        }
        return false;
    }
    
    public function Get($Token,$Fields,$FacebookId='me') {
        if(!empty($Token) && !@empty($Token['access_token'])) {
            return $this->CallJson(self::UrlGraph.$FacebookId,['access_token'=>$Token['access_token'],'fields'=>implode(',',$Fields)]);
        }
        return false;
    }

}
