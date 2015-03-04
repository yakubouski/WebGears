<?php
class widgetAccordion extends \Html\Widget
{
    private $accordionItems;
    public function PanelEnd($Args,$InnerHTML) {
	$this->accordionItems[] = ['Args'=>$Args,'Html'=>$InnerHTML];
	echo 2;
    }
    
    public function PanelComplete($Args) {
	$this->accordionItems[] = ['Args'=>$Args,'Html'=>''];
	echo 1;
    }
    
    public function Begin() {}
    
    public function Complete() {}
    
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id=$this->arg('id','accordion-'.rand(10, 4000).'-'.time());
	$style = $this->arg('style');
	$icon = $this->arg('icon');
	$multiple = boolval($this->arg('multiple',false));
	$selected = $this->arg('selected',false);
?>
<div class="ui-accordion <?= $class ?>" id="<?= $id ?>" >
    <ul style="<?=$style?>" >
	<?foreach($this->accordionItems as $i=>$tab){ 
	    $name = $this->arg('name', "tab-$i", $tab['Args']); 
	    $href = $this->arg('href', '', $tab['Args']);
	    $icon = $this->arg('icon', false, $tab['Args']);
	    $title = $this->arg('title', '', $tab['Args']);
	    $badgeCaption = $this->arg('badge-caption', false, $tab['Args']);
	    $badgeClass = $this->arg('badge-class', 'ui-badge-default', $tab['Args']);
	    $subCaption  = $this->arg('sub-caption', false, $tab['Args']);
	    ?>
	<li panel-name="<?=$name?>" ><a href="<?=$href?>"><?if($icon !== false){?><i class="<?=$icon?>"></i><?=!empty($title) ? ' ' : ''?><?}?><?=$title?><?if($badgeCaption){?><i class="<?=$badgeClass?>"><?=$badgeCaption?></i><?}?></a>
	    <?if($subCaption !== false){?><div class="panel-subcaption"><?=$subCaption?></div><?}?>
	</li>
	<li class="panel-content hide"><?=$tab['Html']?></li>
	<?}?>
    </ul>
</div>
<script type="text/javascript">UI.Widget('Accordion','#<?=$id?>',{selected:'<?=$selected?>',multiple: <?=$multiple?>})</script>
<?
    }
}
