<?php
class HtmlDescription extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('META_DESCRIPTION',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}