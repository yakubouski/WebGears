<?php
class HtmlPublisher extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('HTML_PUBLISHER',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}