<?php
namespace Auth;
require_once 'oauth.class.php';

class Instagram extends OAuth
{
    const UrlOAuth = 'https://api.instagram.com/oauth/authorize/';
    const UrlAccessToken = 'https://api.instagram.com/oauth/access_token';
    
    /**
     * @var \Auth\Credential 
     */
    private $Credential;

    public function __construct($CredentialName,$Global=false) {
        $this->Credential = Credential::Get('instagram', $CredentialName, $Global);
    }

    /**
     * @param string $CredentialName
     * @param bool $Global
     * @return \Auth\Instagram
     */
    static public function Provider($CredentialName='',$Global=false) {
        return new Instagram($CredentialName, $Global);
    }
    
    public function Authorize($RedirectUrl,$Scope='',$State='',$ReNew=false) {
        if(isset($this->Credential->CLIENT_ID) && ($ReNew || !isset($this->Credential->TOKEN)) || !isset($this->Credential->TOKEN['access_token'])) {
            if(isset($_GET['code'])) {
                $this->Credential->TOKEN = $this->CallJson(self::UrlAccessToken, [
                    'client_id'=>$this->Credential->CLIENT_ID,
                    'client_secret'=>$this->Credential->CLIENT_SECRET,
                    'grant_type'=>'authorization_code',
                    'redirect_uri'=>$RedirectUrl,
                    'code'=>filter_input(INPUT_GET,'code',FILTER_SANITIZE_STRING),
                ]);
            }
            else {
                $this->Location(self::UrlOAuth, [
                    'client_id'=>$this->Credential->CLIENT_ID,
                    'redirect_uri'=>$RedirectUrl,
                    'response_type'=>'code',
                    'scope'=>$Scope,'state'=>$State]);
            }
        }
    }
}
