<?php
class WidgetPanel extends \Html\Widget {

    public function DynamicEnd($Args,$InnerHtml) {
	
    }

    public function End($innerHtml) {
	$class = $this->arg('class');
	$id=$this->arg('id','tabctrl-'.rand(10, 4000).'-'.time());
	$style = $this->arg('style');

?>
<div class="ui-panel <?=$class?>" style="<?=$style?>" id="<?=$id?>" >
    <?=$innerHtml?>
</div>
<?
    }

    public function Begin() {}
    public function Complete() { $this->End(''); }
}
