<?php
class LayoutHeader extends \Html\Widget 
{
    public function Begin() {}
    public function Complete() {
	$this->End('');
    }
    public function End($InnerHTML) {
	HtmlLayout::$MAIN->HeaderEnd($this->args(),$InnerHTML);
    }
}