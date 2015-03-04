<?php
class HtmlIcon extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('HTML_ICON',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}