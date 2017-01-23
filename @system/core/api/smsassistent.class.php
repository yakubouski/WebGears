<?php
namespace Api;
class SmsAssistent 
{
    const UrlPlain = 'https://userarea.sms-assistent.by/api/v1/send_sms/plain?';
    const UrlXml = 'https://userarea.sms-assistent.by/api/v1/xml';
    
    static public $Errors = [
        0 =>'сервис не доступен',
        -1=>'недостаточно средств',
        -2=>'неправильный логин или пароль (ошибка при аутентификации)',
        -3=>'отсутствует текст сообщения',
        -4=>'некорректное значение номера получателя',
        -5=>'некорректное значение отправителя сообщения',
        -6=>'отсутствует логин',
        -7=>'отсутствует пароль',
        -10=>'сервис временно недоступен',
        -11=>'некорректное значение ID сообщения',
        -12=>'другая ошибка',
        -13=>'заблокировано',
        -14=>'запрос не укладывается в ограничения по времени на отправку SMS'
    ];
    
    static public function Send($Sender,$User,$Password,$Recipient,$Message) {
        $Result = file_get_contents(self::UrlPlain.http_build_query(
            ['user'=>$User,'password'=>$Password,'recipient'=>preg_replace('/\D+/', '', $Recipient),'message'=>trim($Message),'sender'=>$Sender]));

        $Result = intval($Result);
        $Result <= 0 && \Exception(self::$Errors[$Result],$Result);
        
        return $Result;
    }
    /**
     * Отправить пакетное сообщение нескольким получателем. Если задана $MessageTransformFunctoin функция, то для формирования сообщения она будет вызвана и длжна вернуть сообщение, которое реально будет отправлено получателю. Необходимо для вставки динамических параметров
     * @param type $Sender
     * @param type $User
     * @param type $Password
     * @param type $Recipients
     * @param type $Message
     * @param type $MessageTransformFunction Прототип функции function Transform($Message,$Recipient) { return $Message; // Если вернуть false или пусто, то получатель будет исключен}
     * @return boolean
     */
    static public function Bulk($Sender,$User,$Password,$Recipients,$Message,$MessageTransformFunction=false) {
        
        if(empty($Recipients) || empty($Sender) || empty($User) || empty($Password) || empty($Message)) {
            return false;
        }
        if(!is_callable($MessageTransformFunction)) {
            $ToList = '<msg recipient="'.implode('"/>'.PHP_EOL.'<msg recipient="',$Recipients).'"/>';
            $postdata =<<<"XML"
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE package SYSTEM "send_sms.dtd">
<package login="$User" password="$Password">
    <message>
        <default sender="$Sender"><![CDATA[$Message]]></default>
        $ToList
    </message>
</package>
XML;
        }
        else {
            $ToList = [];
            foreach($Recipients as $r) {
                $Result = $MessageTransformFunction($Message,$r);
                if(!empty($Result)) {
                    $ToList[]='<msg recipient="'.$r.'"><![CDATA['.$Result.']]></msg>';
                }
            }
            if(empty($ToList)) {
                return false;
            }
            $ToList = implode(PHP_EOL,$ToList);
            $postdata =<<<"XML"
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE package SYSTEM "send_sms.dtd">
<package login="$User" password="$Password">
    <message>
        <default sender="$Sender"></default>
        $ToList
    </message>
</package>
XML;
        }
        
        $ch = curl_init( self::UrlXml );
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)"); // useragent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            "Content-Type: text/xml",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            "Keep-Alive: 300",
            "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
            "Accept-Language: en-us,en;q=0.5",
            "Pragma: "
        ]);
        
        $Result = curl_exec( $ch );
        curl_close( $ch );
        return $Result;
    }
}
