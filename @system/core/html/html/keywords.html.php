<?php
class HtmlKeywords extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('META_KEYWORDS',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}