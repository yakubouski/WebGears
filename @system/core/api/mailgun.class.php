<?php
namespace Api;
//include_once __DIR__.'/3th/mailgun-php/src/Mailgun/Mailgun.php';
//use Mailgun;
class Mailgun
{
    const BASEURL = 'https://api.mailgun.net/';
    private $ApiUrl = '';
    private $ApiCredential = '';
    public function __construct($ApiCredential,$ApiDomain=false,$ApiVersion='v3') {
        $this->ApiUrl = self::BASEURL.$ApiVersion.(!empty($ApiDomain) ? ('/'.$ApiDomain):'').'/';
        $this->ApiCredential = $ApiCredential;
    }
    private function call($Method,$Data) {
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $this->ApiUrl.$Method);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "api:{$this->ApiCredential}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,$Data);
	$result = curl_exec($ch);
	curl_close($ch);
        return !empty($result) ? json_decode($result,true) : false;
    }

    public function SendMessage($From,$To,$Subject,$Text) {
        
        return $this->call('messages', [
            'from' => $From,
            'to' => $To,
            'subject' => $Subject,
            'html' => $Text
        ]);
    }
    
    public function BulkMessage($From,$To,$Subject,$Text) {
        $id = 1;
        foreach($To as $e=>&$n) { $n = ['first'=>$n,'id'=>$id++]; }
        return $this->call('messages', [
            'from' => $From,
            'to' => implode(',',array_keys($To)),
            'subject' => $Subject,
            'html' => $Text,
            'recipient-variables'=>  json_encode($To)
        ]);
    }
}
