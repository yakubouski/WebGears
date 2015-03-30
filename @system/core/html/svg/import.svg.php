<?php
class SvgImport extends \Html\Widget 
{
    public function Begin() {}
    public function Complete() {
	$this->End('');
    }
    public function End($InnerHTML) {
	echo file_get_contents(APP_BASE_DIRECTORY.$this->arg('src'));
    }
}