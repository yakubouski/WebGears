<?php
class HtmlCss extends \Html\Widget {
    public function End($innerHtml) {
	HtmlPage::$HTML->Property('CSS_SCRIPTS')[] = "<link href=\"".$this->arg('href',$this->arg('src'))."\" rel=\"stylesheet\" type=\"text/css\"/>";
    }
    public function Begin() {	}
    public function Complete() { $this->End('');}
}