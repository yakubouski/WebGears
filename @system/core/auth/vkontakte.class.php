<?php
namespace Auth;
require_once 'oauth.class.php';

class VKontakte extends OAuth
{
    const UrlOAuth = 'https://oauth.vk.com/authorize';
    const UrlAccessToken = 'https://oauth.vk.com/access_token';
    const UrlGraph = 'https://api.vk.com/method/';
    const DefaultScope = 'email,offline';
    
    /**
     * @var \Auth\Credential 
     */
    private $Credential;

    public function __construct($CredentialName,$Global=false) {
        $this->Credential = Credential::Get('vkontakte', $CredentialName, $Global);
    }

    /**
     * @param string $CredentialName
     * @param bool $Global
     * @return \Auth\VKontakte
     */
    static public function Provider($CredentialName='',$Global=false) {
        return new VKontakte($CredentialName, $Global);
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
                    'response_type'=>'code',
                    'scope'=>$Scope,
                    'display'=>'page',
                    'redirect_uri'=>$RedirectUrl,
                    'state'=>$State
                        ]);
            }
        }
        return false;
    }
    
    public function Get($Token,$Fields,$Graph='users.get') {
        if(!empty($Token) && !@empty($Token['access_token'])) {
            return $this->CallJson(self::UrlGraph.$Graph,['access_token'=>$Token['access_token'],'v'=>'5.52','user_id'=>$Token['user_id'],'fields'=>implode(',',$Fields)]);
        }
        return false;
    }

}
