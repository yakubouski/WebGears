<?php
namespace Session;
class Sql 
{
    static public function Initialize($SessionStart=FALSE,$SessionClassHandler=FALSE,$SessionName=NULL,$SessionPath=NULL,$SessionTTL=NULL,$SessionId=NULL) {
	if($SessionStart)
        {
            $SessionPath && (\File::MkDir(empty($SessionPath)?APP_SESSION_PATH:$SessionPath,true,0770) && 
		@session_save_path(\File::Path(empty($SessionPath)?APP_SESSION_PATH:$SessionPath)));
            $SessionName && @session_name($SessionName);
            $SessionTTL && session_set_cookie_params($SessionTTL);
            $SessionId && @session_id($SessionId);
	    $SessionClassHandler && session_set_save_handler(new $SessionClassHandler,true);
            @session_start();
        }
    }
}
