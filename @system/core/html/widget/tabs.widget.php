<?php
class widgetTabs extends \Html\Widget
{
    private $tabItems;
    public function TabEnd($Args,$InnerHTML) {
	$this->tabItems[] = ['Args'=>$Args,'Html'=>$InnerHTML];
    }
    
    public function TabComplete($Args) {
	$this->tabItems[] = ['Args'=>$Args,'Html'=>''];
    }
    
    public function Begin() {}
    public function Complete() {}
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id=$this->arg('id','tabctrl-'.rand(10, 4000).'-'.time());
	$style = $this->arg('style');
	$icon = $this->arg('icon');
	$selected = $this->arg('selected',false);
	$caption = $this->arg('caption',false);
?>
<div class="ui-tabctrl <?= $class ?>" id="<?= $id ?>" >
    <ul class="ctrl-items"  style="<?=$style?>" >
	<?if($caption !== false){?><li><?=$caption?></li><?}?>
	<?$tabItems = [];foreach($this->tabItems as $i=>$tab){ 
	    $name = $this->arg('name', "tab-$i", $tab['Args']); $selected == false && ($selected = $name);
	    $href = $this->arg('href', '', $tab['Args']);
	    $icon = $this->arg('icon', false, $tab['Args']);
	    $tclass = $this->arg('class', false, $tab['Args']);
	    $tabItems[] = "<div tab-name='$name' class='content-hide'>".$tab['Html']."</div>";
	    $title = $this->arg('title', '', $tab['Args']);
	    
	    $badgeCaption = $this->arg('badge-caption', false, $tab['Args']);
	    $badgeClass = $this->arg('badge-class', 'ui-badge-default', $tab['Args']);
	    ?>
	<li tab-name="<?=$name?>" class="<?=$tclass?>"><a href="<?=$href?>"><?if($badgeCaption){?><i class="<?=$badgeClass?>" style="margin: 0; padding: 2px; font-weight: normal;"><?=$badgeCaption?></i><?}?><?if($icon !== false){?><i class="<?=$icon?>"></i><?=!empty($title) ? ' ' : ''?><?}?><?=$title?></a></li>
	<?}?>
    </ul>
    <div class="ctrl-tabs-content">
	<?=implode(PHP_EOL,$tabItems)?>
    </div>
</div>
<script type="text/javascript">UI.Widget('TabCtrl','#<?=$id?>',{selected:'<?=$selected?>'})</script>
<?
    }
}