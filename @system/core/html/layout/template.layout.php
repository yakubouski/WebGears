<?php
class LayoutTemplate extends \Html\Widget {
    static private $Template;
    private $Properties = [];
    public function End($innerHtml) {
	self::prop('LAYOUT:BODY',$innerHtml);
	\Html::Template()->Display($this->arg('src'), $this->Properties);
    }

    static public function prop($prop,$value=null) {
	if(is_null($value)) { unset(self::$Template->Properties[$prop]); } else { self::$Template->Properties[$prop] = $value;}
    }
    
    public function Begin() { 
	self::$Template = $this; 
	
    }
    public function Complete() { self::$Template = $this; }
}