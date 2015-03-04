<?php
class inputMonth extends \Html\Widget
{
    public function Begin() {}
    public function Complete() {$this->End('');}
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id = $this->arg('id','id-month-'.rand(10, 4000).'-'.time());
	$name = $this->arg('name');
	$withDays = $this->arg('day','false') == 'true';
	$format = $withDays ? $this->arg('format','Y-m-d') : $this->arg('format','Y-m-01');
	$value = TimeStamp::Now($this->arg('value','now'));
	$years = $this->arg('years',10);
	$year = intval(date('Y'));
	
?>
<div id="<?=$id?>" class="ui-datetime <?=$class?>">
    <input type="hidden" name="<?=$name?>" class="field-value" value="<?=$value->format($format)?>">
    <div>
	<button class="month-nav-left" type="button"><i class="icon-arrow-left4 f16"></i></button>
	<select class="field-d" <?=!$withDays?'style="display: none;"':''?>>
	    <?for($i=1;$i<=31;$i++){?><option value="<?=$i?>"><?=$i?></option><?}?>
	</select>
    <select class="field-m">
	<?foreach(I18N::Format()->Months[$withDays ?'genitive':'full'] as $m=>$n){?><option value="<?=$m?>"><?=$n?></option><?}?>
    </select>
    <select class="field-y">
	<option value="<?=$year+1?>"><?=$year+1?></option>
	<option value="<?=$year?>"><?=$year?></option>
	<?$year = intval($year); for($i=1;$i<=$years;$i++){?>
	<option value="<?=($year-$i)?>"><?=($year-$i)?></option>
	<?}?>
    </select>
	<button class="month-nav-right" type="button"><i class="icon-arrow-right4 f16"></i></button>
    </div>
</div>
<script type="text/javascript">UI.Widget('Month','#<?=$id?>',{
    datetime: '<?=$value->format($format)?>',
    format: '<?=$format?>'
})</script>
<?
    }
}
