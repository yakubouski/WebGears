<?php
class inputCalendar extends \Html\Widget
{
    static private $DayTypes = ['freeday','holiday','workday','default'];

    private $daysList = [];
    private $eventsList = [];
    
    public function DayEnd($Args,$InnerHtml) {
	$this->DayComplete($Args);
    }
    public function DayComplete($Args) {
	$date = Form::Date($this->arg('date', false, $Args),false);
	
	!empty($date) && $this->daysList[$date][] = ['title'=>$this->arg('title', '', $Args),'type'=>$this->arg('type', 'default', $Args),];
    }
    
    public function EventEnd($Args,$InnerHtml) {
	$this->EventComplete($Args);
    }
    public function EventComplete($Args) {
	$date = Form::Date($this->arg('date', false, $Args),false);
	$from = Form::Date($this->arg('from', false, $Args),false);
	$to = Form::Date($this->arg('to', false, $Args),false);
	
	if(!empty($from) && !empty($to)) {
	    
	    $week = [
		1=>$this->arg('mon','true',$Args)=='true',
		2=>$this->arg('tue','true',$Args)=='true',
		3=>$this->arg('wed','true',$Args)=='true',
		4=>$this->arg('thu','true',$Args)=='true',
		5=>$this->arg('fri','true',$Args)=='true',
		6=>$this->arg('sat','true',$Args)=='true',
		7=>$this->arg('sun','true',$Args)=='true',
	    ];
	    
	    foreach (new DatePeriod(new DateTime($from), new DateInterval('P1D'), new DateTime($to)) as $dt) {
		$week[intval($dt->format('N'))] && $this->eventsList[$dt->format('Y-m-d')][] = ['title'=>$this->arg('title', '', $Args)];
	    }
	}
	elseif(!empty($date)) {
	    $this->eventsList[$date][] = ['title'=>$this->arg('title', '', $Args),'type'=>$this->arg('type', 'default', $Args),];
	}
    }
    
    public function Begin() {}
    public function Complete() {$this->End('');}
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id = $this->arg('id','id-calendar-'.rand(10, 4000).'-'.time());
	$name = $this->arg('name');
	$style = $this->arg('style');
	$value = TimeStamp::Now($this->arg('value',date('Y-m-d H:i:s')));
?>
<div id="<?=$id?>" class="ui-datetime" style="<?=$style?>">
    <input type="hidden" name="<?=$name?>" class="field-value" value="<?=$value->format('Y-m-d H:i:s')?>">
    <table class="ui-calendar <?=$class?>">
	<tr>
	    <th style="text-align: center;"><a class="nav month-left"><i class="icon-arrow-left4 f16"></i></a></th>
	    <th style="text-align: center;" colspan="5"><a class="nav month-name">месяц, год</a></th>
	    <th style="text-align: center;"><a class="nav month-right"><i class="icon-arrow-right4 f16"></i></a></th>
	</tr>
	<tr>
	    <th>пн</th><th>вт</th><th>ср</th><th>чт</th><th>пт</th><th>сб</th><th>вс</th>
	</tr>
	<tr>
	    <?
		for($i=0;$i<42;$i++) {  ($i>0 && $i%7 == 0) && (print '</tr><tr>');
		?>
	    <td class="d" date=""><div><?=$i?></div></td>
		<?
		}
	    ?>
	</tr>
	<tr>
	    <td colspan="7" style="padding: 3px 0;"><a class="nav month-today" date="<?=TimeStamp::Now()->format('Y-m-d')?>">Сегодня</a></td>
	</tr>
    </table>
</div>
<script type="text/javascript">UI.Widget('Calendar','#<?=$id?>',{
    datetime: '<?=$value->format('Y-m-d H:i:s')?>',
    months: <?=json_encode(I18N::Format()->Months['full'],JSON_UNESCAPED_UNICODE)?>,
    days: <?=json_encode($this->daysList,JSON_UNESCAPED_UNICODE)?>,
    events: <?=json_encode($this->eventsList,JSON_UNESCAPED_UNICODE)?>
})</script>
<?
    }
}