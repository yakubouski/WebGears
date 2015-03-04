<?php
class HtmlTitle extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('HTML_TITLE',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}