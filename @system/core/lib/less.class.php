<?php
namespace Lib;
include_once (__DIR__.'/3th/lessphp-0.4.0/lessphp/lessc.inc.php');
define('LESS_COMPILE_DIRECTORY','.complie/.less/');
define('LESS_IMPORT_DIRECTORY',APP_SYSTEM_DIRECTORY.'css/');

class Less {
    static public function Compile($srcLessFile) {
	
	\File::MkDir(LESS_COMPILE_DIRECTORY, true);
	$compiledLessFile = LESS_COMPILE_DIRECTORY.str_replace(['/','\\'], '_', $srcLessFile).'.css';
	
	if(!\File::Exist($compiledLessFile) || \File::Time($srcLessFile) > \File::Time($compiledLessFile)) {
	    try {
		$less = new \lessc;
		$less->setImportDir([LESS_IMPORT_DIRECTORY]);
		$less->compileFile(\File::FullPath($srcLessFile), \File::FullPath($compiledLessFile));
	    }  catch (Exception $e) {
		\Console::Error($e);
	    }
	}
	\File::DownloadFile($compiledLessFile, 'text/css',[],true);
    }
}
