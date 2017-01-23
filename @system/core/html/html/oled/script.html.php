<?php
class HtmlScript extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('JS_SCRIPTS')[] = "<script src=\"".$this->arg('src')."\" type=\"text/javascript\" charset=\"utf-8\"></script>";
    }
    public function Begin() {	}
    public function Complete() { $this->End('');}
}