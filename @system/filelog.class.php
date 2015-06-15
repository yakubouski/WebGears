<?php
class FileLog 
{
    const Directory = '.log/';
    static public function Write($Log,$Event) {
	$Text = vsprintf($Event, array_slice(func_get_args(), 2));
	\File::Write(self::Directory.$Log, $Text, FILE_APPEND|FILE_GZIP);
    }
}
