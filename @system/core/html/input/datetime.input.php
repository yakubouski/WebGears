<?php
class inputDateTime extends \Html\Widget
{
    public function Begin() {}
    public function Complete() {$this->End('');}
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id = $this->arg('id','id-date-'.rand(10, 4000).'-'.time());
	$name = $this->arg('name');
	$value = DateTime::createFromFormat('Y-m-d',empty($this->arg('value')) ? date('Y-m-d') :$this->arg('value',date('Y-m-d')) );
        $Ymd = $value->format('Y-m-d');
        list($vY,$vm,$vd) = explode('-',$Ymd);
?>
<div><div class="ui-date <?=$class?>">
        <input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$Ymd?>" ><span class="date-dow">день недели</span>
        <select class="date-day"><?for($d=1;$d<=31;$d++){?><option <?=($d==$vd?'selected':'')?>><?=sprintf('%02d',$d)?></option><?}?></select>
        <select class="date-month"><?for($m=1;$m<=12;$m++){?><option <?=($m==$vm?'selected':'')?>><?=sprintf('%02d',$m)?></option><?}?></select>
        <select class="date-year"><?for($y=date('Y')-5;$y<=date('Y')+1;$y++){?><option <?=($y==$vY?'selected':'')?>><?=$y?></option><?}?></select>
    </div>
</div>
<?
    }
}