<?php
namespace Auth;
require_once 'oauth.class.php';

class Google extends OAuth
{
    const UrlOAuth = 'https://accounts.google.com/o/oauth2/auth';
    const UrlAccessToken = 'https://accounts.google.com/o/oauth2/token';
    const UrlGraph = 'https://www.googleapis.com/oauth2/v2/';
    const DefaultScope = 'email';
    
    /**
     * @var \Auth\Credential 
     */
    private $Credential;

    public function __construct($CredentialName,$Global=false) {
        $this->Credential = Credential::Get('google', $CredentialName, $Global);
    }

    /**
     * @param string $CredentialName
     * @param bool $Global
     * @return \Auth\Google
     */
    static public function Provider($CredentialName='',$Global=false) {
        return new Google($CredentialName, $Global);
    }
    
    public function Authorize($RedirectUrl,$Scope=self::DefaultScope,$State='',$ReNew=false) {
        if(isset($this->Credential->client_id)) {
            if(isset($_GET['code'])) {
                if(($Token = $this->CallPostJson(self::UrlAccessToken, [
                    'client_id' => $this->Credential->client_id,
                    'client_secret' => $this->Credential->client_secret,
                    'redirect_uri' => $RedirectUrl,
                    'code' => trim($_GET['code']),
                    'grant_type' => 'authorization_code'
                ]))) { 
                    return $Token; 
                }
                else {
                    return false;
                }
            }
            else {
                $this->Location(self::UrlOAuth, [
                    'client_id'=>$this->Credential->client_id,
                    'redirect_uri'=>$RedirectUrl,
                    'response_type'=>'code',
                    'scope'=>$Scope,
                    'display'=>'popup',
                    'state'=>$State]);
            }
        }
        return false;
    }
    
    public function Get($Token,$Fields,$Graph='userinfo') {
        if(!empty($Token) && !@empty($Token['access_token'])) {
            return $this->CallJson(self::UrlGraph.$Graph,['access_token'=>$Token['access_token'],'fields'=>implode(',',$Fields)]);
        }
        return false;
    }
}
