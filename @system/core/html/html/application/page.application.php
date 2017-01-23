<?php
class ApplicationPage extends \Html\Widget {
    public function Begin() {
        
    }
    private function Fetch($Tpl,$Args) {
        return \Html::Template($Tpl, $Args);
    }
    
    public function Complete() {
        $this->End('');
    }

    public function End($InnerHTML) {
        $Link = $this->arg('link',null);
        $Location = $this->arg('location',null);
        $Template = $this->arg('template',$this->arg('tpl',false));
        $This = $this->arg('this',$this);
        ($Link == $Location) && print (!empty($Template) ? $This->Fetch($Template,$this->args()):$InnerHTML);
    }
}