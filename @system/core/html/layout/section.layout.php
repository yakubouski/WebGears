<?php
/**
 * Основное содержимле
 */
class LayoutSection extends \Html\Widget {
    
    private function toolbar_encode($text) {
	if(preg_match_all('/\{(.*?)\}/m', $text, $result)) {
	    $tb = [];
	    foreach ($result[1] as $r) {
		if(preg_match_all('/(href|title|icon)\s*:\s*("|\')(.*?)\2/', $r, $bs, PREG_SET_ORDER)) {
		    $btn = [];
		    foreach($bs as $b) {
			$btn[$b[1]] = $b[3];
		    }
		    $tb[] = $btn;
		}
	    }
	    return $tb;
	}
	return [];
    }


    public function End($innerHtml) {
	!empty($this->arg('back')) && LayoutTemplate::prop('LAYOUT:SECTION:BACK_BUTTON',$this->arg('back'));
	!empty($this->arg('title')) && LayoutTemplate::prop('LAYOUT:SECTION:TITLE',$this->arg('title'));
	!empty($this->arg('toolbar')) && LayoutTemplate::prop('LAYOUT:SECTION:TOOLBAR',  $this->toolbar_encode($this->arg('toolbar'),true));
	!empty($this->arg('sidebar')) && LayoutTemplate::prop('LAYOUT:SECTION:SIDEBAR',$this->arg('sidebar'));
	$this->arg('sidebar_show',false) !== false && LayoutTemplate::prop('LAYOUT:SECTION:SIDEBAR_SHOW',$this->arg('sidebar_show')!=='false');
	!empty($innerHtml) && LayoutTemplate::prop('LAYOUT:SECTION:INNER',$innerHtml);
    }
    public function Begin() {}
    public function Complete() { $this->End(''); }
}