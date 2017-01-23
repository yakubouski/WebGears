<?php
class Thread
{
    /**
     * @ignore
     */
    private static function link($Url)
    {
        exec(escapeshellcmd("whereis -b wget"), $crons, $return);
        if (preg_match('/[^:]*:\s*(.+)/', $crons[0],$l))
        {
            return "{$l[1]} --no-check-certificate -q -nv $Url";
        }

        exec(escapeshellcmd("whereis -b curl"), $crons, $return);
        if (preg_match('/[^:]*:\s*(.+)/', $crons[0],$l))
        {
            return "{$l[1]} $Url > /dev/null 2>&1";
        }
        return false;
    }

    public static function Lock($objectID,$EnableLog=true,$TimeOut = 3600,$GuardIp=false)
    {
        \File::MkDir('/.threads/', true);
        if($GuardIp && $_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) {
            $EnableLog && file_put_contents(\File::FullPath('/.threads')."/.thread-{$objectID}.log", '['.getmypid()."]\t".date('Y-m-d H:i:s')." IP GUARD \n",FILE_APPEND);
            exit;
        }
        
        if(($locker = fopen(\File::FullPath('/.threads')."/.thread-$objectID.lock",'w+')))
        {
            if(!flock($locker, LOCK_EX | LOCK_NB))
            {
                fclose($locker); 
                $EnableLog && file_put_contents(\File::FullPath('/.threads')."/.thread-{$objectID}.log", PHP_EOL.'['.getmypid()."]\t".date('Y-m-d H:i:s',intval($_SERVER['REQUEST_TIME']))."\t".date('Y-m-d H:i:s')." locked",FILE_APPEND);
                return false;
            }
            return new Thread($locker,$objectID,$EnableLog, $TimeOut);
        }
        $EnableLog && file_put_contents(\File::FullPath('/.threads')."/.thread-{$objectID}.log", PHP_EOL.'['.getmypid()."]\t".date('Y-m-d H:i:s')." open-fail \n",FILE_APPEND);
    }

    public function  __construct($objectLocker,$objectID,$EnableLog,$objectTimeout)
    {
        if($EnableLog) { ob_start();}
        ignore_user_abort(1);
        register_shutdown_function('thread::terminate',$this);
        set_time_limit ($objectTimeout);
        $this->startTime = date('Y-m-d H:i:s');
        $this->objectID = $objectID;
        $this->enableLog = $EnableLog;
        $this->objectLock = $objectLocker;
        fwrite($this->objectLock,getmypid());
    }
    /**
     * @ignore
     */
    static public function terminate(Thread $This) { $This->UnLock(); }
    /**
     * @ignore
     */
    public function  __destruct() { $this->UnLock(); }
    public function UnLock()
    {
        if(is_resource($this->objectLock))
        {
            if($this->enableLog) {
                $buffer = ob_get_clean();
                $buffer = '['.getmypid()."]\t".$this->startTime.PHP_EOL."{\n\t".implode("\n\t",  explode(PHP_EOL, $buffer)).PHP_EOL."}\n".'['.getmypid()."]\t".date('Y-m-d H:i:s');
                file_put_contents(\File::FullPath('/.threads')."/.thread-{$this->objectID}.log", PHP_EOL.$buffer,FILE_APPEND);
            }
            flock($this->objectLock, LOCK_UN);
            fclose($this->objectLock);
            $this->objectLock = null;
            flush();
        }
        
    }

    /**
     * Запустить асинхронное задание
     * @param string $Url
     */
    static public function Begin($Url)
    {
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
    
    /**
    * Возвращает абсолютный адрес URL
    * @param array $ArgsList
    * @param string $Path
    * @param string $Domain
    * @param bool $UseForwardedHost
    * @return string
    */
   static public function Url($ArgsList=false,$Path=false,$Domain=false,$UseForwardedHost=false){
       $ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
       $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
       $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
       $port     = $_SERVER['SERVER_PORT'];
       $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
       $host     = ( $UseForwardedHost && isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null );
       $host     = isset( $host ) ? $host : $_SERVER['SERVER_NAME'] . $port;
       return $protocol . '://' . (!empty($Domain)?$Domain:$host) . (!empty($Path) ? ($Path.(!empty($ArgsList)?('?'.http_build_query($ArgsList)):'')) : $_SERVER['REQUEST_URI']).(!empty($ArgsList)?('?'.http_build_query($ArgsList)):'');
   }
}