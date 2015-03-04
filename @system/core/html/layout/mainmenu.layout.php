<?php
/**
 * Главное меню страницы
 */
class LayoutMainMenu extends \Html\Widget {
    public function End($innerHtml) {
	!empty($this->arg('src')) && LayoutTemplate::prop('LAYOUT:MAINMENU:SRC',$this->arg('src'));
	!empty($this->arg('selected')) && LayoutTemplate::prop('LAYOUT:MAINMENU:SELECTED',$this->arg('selected'));
    }
    public function Begin() {}
    public function Complete() { $this->End(''); }
}