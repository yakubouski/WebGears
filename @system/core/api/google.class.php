<?php
namespace Api;
require __DIR__.'/./../auth/oauth.class.php';

// Authorization: Bearer 1/fFBGRNJru1FQd44AzqT3Zg

class Google extends \Auth\OAuth
{
    const DefaultScope = 'https://www.googleapis.com/auth/analytics.readonly';

    /**
     * @var \Auth\Credential 
     */
    private $Credential;

    public function __construct($CredentialName,$Global=false) {
        $this->Credential = \Auth\Credential::Get('google', $CredentialName, $Global);
    }

    public function Authorize($RedirectUrl,$Scope='',$State='',$ReNew=false) {
        if(isset($this->Credential->client_id)) {
            if(isset($_GET['code'])) {
                if(($Token = $this->CallPostJson($this->Credential->token_uri, [
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
                $this->Location($this->Credential->auth_uri, [
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
    
    static public function Analytics($CredentialName,$Global=false) {
        require_once 'google/googleanalytics.class.php';
        return new GoogleAnalytics($CredentialName, $Global);
    }
}
