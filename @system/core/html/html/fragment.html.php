<?php
class HtmlFragment extends \Html\Widget {
    
    private function __content(&$innerHtml) {
	if($this->arg('include',false) !== false) {
	    $tpl = new \Html\Template($this->arg('include',''));
	    return $tpl->Fetch();
	}
	return $innerHtml;
    }


    public function End($innerHtml) {
	$paramDefault = $this->arg('default','false') !== 'false';
	$paramState = Html::WidgetState('HtmlFragment', '#Fragment');
	$paramId = $this->arg('id',false);
	if(isset($_GET['#fragment'])) {
	    if($_GET['#fragment'] === $paramId) {
		\Std::ReturnHtml($this->__content($innerHtml));
	    }
	}
	else {
	    if(($paramDefault && empty($paramState)) || (!is_null($paramState) && $paramState == $paramId)) {
		(!is_null($paramState) && $paramState == $paramId) && Html::WidgetStateUnset('HtmlFragment', '#Fragment');
		print $this->__content($innerHtml);
	    }
	}
    }
    public function Begin() {}
    public function Complete() {  $this->End(''); }
}