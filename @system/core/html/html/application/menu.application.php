<?php
class ApplicationMenu extends \Html\Widget {
    public function Begin() {
        
    }
    private function Fetch($Tpl,$Args) {
        return \Html::Template($Tpl, $Args);
    }
    
    public function Complete() {
        $Template = $this->arg('template',$this->arg('tpl',false));
        $This = $this->arg('this',$this);
        $this->End(!empty($Template) ? $This->Fetch($Template,$this->args()):'');
    }

    public function End($InnerHTML) {
        print ($InnerHTML);
    }
}
