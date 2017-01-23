<?php
namespace Lib;
include_once __DIR__.'/3th/PHPMailer/PHPMailerAutoload.php';

class Mail extends \PHPMailer
{
    static public function GMail() {
        return new GoogleMailProvider();
    }
    /**
     * @param type $FromName
     * @param type $FromEMail
     * @param type $Pwd
     * @return \Lib\YandexMailProvider
     */
    static public function YandexMail($FromName,$FromEMail,$Pwd) {
        return new YandexMailProvider($FromName,$FromEMail,$Pwd);
    }
    /**
     * Отправить почтовое сообщение
     * @param string $ToName Имя
     * @param string $ToEMail EMail
     * @param string $Subject Тема сообщения
     * @param string $HtmlBody Тело сообщения в html
     * @param string $AltBody Альтернотивное тело сообщения
     * @param array $Attachments массив вложенных файлов, пусть к файлу или если каждый элемент это массив, то в формате ['path'=>$path,['name'=>$name,['encoding'=>$encoding, ['mime'=>$type, ['disposition'=>$disposition)]]]]]]
     * @param string $Error
     */
    public function SendMail($ToName,$ToEMail,$Subject, $HtmlBody,$AltBody='',$Bcc=[],$Attachments=[],&$Error='') {
        
        $this->AddCustomHeader("List-Unsubscribe",'<admin@netsolution.pro>, <http://netsolution.pro/?email='.$ToEMail.'>');
        
        $this->addAddress($ToEMail, $ToName);
        if(!empty($Bcc)){
            foreach($Bcc as $b) {
                $this->addBCC($b);
            }
        }
        $this->Subject = $Subject;

        !empty($HtmlBody) && $this->msgHTML($HtmlBody, rtrim(APP_BASE_DIRECTORY,'\\/'));
        !empty($AltBody) && $this->AltBody = $AltBody;

        if(!empty($Attachments)) {
            foreach($Attachments as $a) {
                if(is_string($a)) {
                    $this->addAttachment($a);
                }
                elseif(is_array($a) && isset($a['path'])) {
                    $this->addAttachment($a['path'],isset($a['name'])?$a['name']:null,isset($a['encoding'])?$a['encoding']:null,isset($a['type'])?$a['type']:null,isset($a['disposition'])?$a['disposition']:null);
                }
            }
        }
        if (!$this->send()) {
            $Error = $this->ErrorInfo;
            return false;
        }
        return true;
    }
    
    protected function SetSmtpCredential($FromName,$FromEMail,$Pwd,$Host,$Port,$SmtpSecure='tls') {
        $this->isSMTP();
        $this->CharSet = 'utf-8';
        $this->SMTPDebug = 0;
        $this->Host = $Host;
        $this->Port = $Port;
        $this->SMTPSecure = $SmtpSecure;
        $this->SMTPAuth = true;
        $this->Username = $FromEMail;
        $this->Password = $Pwd;
        $this->setFrom($FromEMail,$FromName);
    }
}
class YandexMailProvider extends Mail {
    public function __construct($FromName,$FromEMail,$Pwd) {
        #$this->SetSmtpCredential($FromName, $FromEMail, $Pwd, 'smtp.yandex.ru', 465, 'ssl');
        $this->SetSmtpCredential($FromName, $FromEMail, $Pwd, 'smtp.yandex.ru', 587);
    }
}


class GoogleMailProvider extends Mail {
}
