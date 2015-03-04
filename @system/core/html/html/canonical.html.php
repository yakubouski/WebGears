<?php
class HtmlCanonical extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('HTML_CANONICAL',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}