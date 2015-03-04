<?php
/**
 * Всплывающее слева меню поверх содержимого layout-а
 */
class LayoutSideBar extends \Html\Widget {
    public function End($innerHtml) {
	!empty($this->arg('src')) && LayoutTemplate::prop('LAYOUT:SIDEBAR:SRC',$this->arg('src'));
	!empty(trim($innerHtml)) &&  LayoutTemplate::prop('LAYOUT:SIDEBAR:INNER',$innerHtml);
	!empty($this->arg('selected')) && LayoutTemplate::prop('LAYOUT:SIDEBAR:SELECTED',$this->arg('selected'));
	$this->arg('show',false) !== false && LayoutTemplate::prop('LAYOUT:SIDEBAR:SHOW',$this->arg('show')!=='false');
    }
    public function Begin() {}
    public function Complete() { $this->End(''); }
}