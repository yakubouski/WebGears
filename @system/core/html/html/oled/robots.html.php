<?php
class HtmlRobots extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('META_ROBOTS',$innerHtml);
    }
    public function Begin() {	}
    public function Complete() { $this->End($this->arg('content'));}
}