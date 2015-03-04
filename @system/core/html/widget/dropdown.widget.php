<?php
class widgetDropDown extends \Html\Widget
{
    public function Begin() {}
    public function Complete() {}
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id=$this->arg('id','dropdown-'.rand(10, 4000).'-'.time());
	$width=$this->arg('width','auto');
	$style = $this->arg('style');
	$icon = $this->arg('icon');
	$title = $this->arg('title');
?>
<div class="ui-dropdown <?=$class?>" id="<?=$id?>" style="width:<?=$width?>" >
    <i class="select" ><?if($icon){?><i class="<?=$icon?>" style="vertical-align: top;"></i> <?}?><?=$title?><span class="grip"></span></i>
    <div style="clear: both;"></div>
    <div class="container" style="<?=$style?>">
        <?=$InnerHTML?>
    </div>
</div>
<script type="text/javascript">UI.Widget('DropDown','#<?=$id?>',{})</script>
<?
    }
}
