<?php
class DocumentFinance extends \Html\Widget
{
    static $onSet = array('pdf','print','html','doc');
    public function Complete(){}

    public function print_field($Name,$Value,$br='<br>',$U=false) {
	$uo=$uc='';
	if($U) {$uo = '<u>';$uc = '</u>';}
	echo !empty($Value) ? ($Name.$uo.$Value.$uc.$br):'';
    }
    
    public function footerComplete($Args) {
	$Type = $this->arg('type','default',$Args);
	echo $Type == 'seolab' ? '{{{DOCUMENT.FOOTER.SEOLAB}}}' : '{{{DOCUMENT.FOOTER}}}';
    }
    
    public function shortfooterComplete($Args) {
	$Type = $this->arg('type','default',$Args);
	echo $Type == 'seolab' ? '{{{DOCUMENT.SHORTFOOTER.SEOLAB}}}' : '{{{DOCUMENT.SHORTFOOTER}}}';
    }
    
    public function footerEnd($Args,$innerHTML='') {
	$this->documentElements[] = trim($innerHTML);
    }
    private function printFooter(Contract $Contract,$ShortFooter=false) {
	ob_start();
?>
<table class="requisites">
    <?if(!$ShortFooter){?>
    <tr>
        <th align="center" width="50%" style="border:0; border-right: 1px solid #000; vertical-align: top;">
	    Заказчик:<br>
	    <?=$Contract->Заказчик->ФормаСобственности?><br><?=$Contract->Заказчик->Имя?>
	</th>
        <th align="center" width="50%" style="border:0; border-left: 1px solid #000; padding-left: 5px; vertical-align: top;">
	    Исполнитель:<br>
	    <?=$Contract->Исполнитель->ФормаСобственности?><br><?=$Contract->Исполнитель->Имя?>
	</th>
    </tr>
    <tr>
        <td valign="top" style="border-right: 1px solid #000; text-align: left; vertical-align: top;">
            <?
		$this->print_field('Юридический адрес: ',$Contract->Заказчик->ЮридическийАдрес);
		$this->print_field('Почтовый адрес: ',$Contract->Заказчик->ПочтовыйАдрес);
		$this->print_field('Р/с: ',$Contract->Заказчик->РасчетныйСчет,'');
		$this->print_field(' в ',$Contract->Заказчик->Банк);
		$this->print_field('Адрес банка: ',$Contract->Заказчик->БанкАдрес);
		$this->print_field('К/счет: ',$Contract->Заказчик->КорСчет);
		$this->print_field('УНП: ',$Contract->Заказчик->УНП);
		$this->print_field('ИНН: ',$Contract->Заказчик->ИНН);
		$this->print_field('КПП: ',$Contract->Заказчик->КПП);
		$this->print_field('ОКПО: ',$Contract->Заказчик->ОКПО);
		$this->print_field('БИК: ',$Contract->Заказчик->БИК);
		$this->print_field('ОГРН: ',$Contract->Заказчик->ОГРН);
		$this->print_field('Паспорт: ',$Contract->Заказчик->Паспорт);
		$this->print_field('Личный номер: ',$Contract->Заказчик->ЛичныйНомер);
		$this->print_field('Телефон: ',$Contract->Заказчик->Телефон,'');
		$this->print_field(', Факс: ',$Contract->Заказчик->Факс);
	    ?>
        </td>
        <td align="left" valign="top" style="border-left: 1px solid #000;  text-align: left; vertical-align: top;">
	    <?
		$this->print_field('Юридический адрес: ',$Contract->Исполнитель->ЮридическийАдрес);
		$this->print_field('Почтовый адрес: ',$Contract->Исполнитель->ПочтовыйАдрес);
		$this->print_field('Р/с: ',$Contract->Исполнитель->РасчетныйСчет,'');
		$this->print_field(' в ',$Contract->Исполнитель->Банк);
		$this->print_field('Адрес банка: ',$Contract->Исполнитель->БанкАдрес);
		$this->print_field('К/счет: ',$Contract->Исполнитель->КорСчет);
		$this->print_field('УНП: ',$Contract->Исполнитель->УНП);
		$this->print_field('ИНН: ',$Contract->Исполнитель->ИНН);
		$this->print_field('КПП: ',$Contract->Исполнитель->КПП);
		$this->print_field('ОКПО: ',$Contract->Исполнитель->ОКПО);
		$this->print_field('БИК: ',$Contract->Исполнитель->БИК);
		$this->print_field('ОГРН: ',$Contract->Исполнитель->ОГРН);
		$this->print_field('Паспорт: ',$Contract->Исполнитель->Паспорт);
		$this->print_field('Личный номер: ',$Contract->Исполнитель->ЛичныйНомер);
		$this->print_field('Телефон: ',$Contract->Исполнитель->Телефон,'');
		$this->print_field(', Факс: ',$Contract->Исполнитель->Факс);
	    ?>
        </td>
        </tr>
        <tr>
	    <td width="50%" align="right" style="vertical-align: top;">
		<br><br><br>
            <div style="font-weight: bold; text-align: right;"><?=$Contract->Заказчик->Подписывает?>&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div style="text-align: left;"><sup>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;М.П.</sup></div>
	    <br><br><br><br>
        </td>
        <td width="50%" align="right" style="vertical-align: top;"><br><br><br>
            <div style="font-weight: bold; text-align: right;"><?=$Contract->Исполнитель->Подписывает?>&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div style="text-align: left;"><sup>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;М.П.</sup></div>
	    <?if(0){?>
            <img src="<?=$Contract['Исполнитель.Печать']?>" border="0" style="position: relative; width: 5.5cm; float: right; top: -60px; left: -20px;" >
	    <?}?>

        </td>
        </tr>
    <?}else{?>
        <tr>
	    <td width="50%" align="center" style="vertical-align: top;">
		<br>
            <div style="font-weight: bold; text-align: left;"><?=str_repeat('&nbsp;', 10)?>Заказчик:<u><?=str_repeat('&nbsp;', 40)?></u>&nbsp;/&nbsp;&nbsp;&nbsp;</div>
	    <br>
	    <div style="text-align: center;"><sup>&nbsp;М.П.</sup></div>
	    <br>
        </td>
        <td width="50%" style="vertical-align: top;"><br>
            <div style="font-weight: bold; text-align: right;"><?=str_repeat('&nbsp;', 10)?>Исполнитель:<u><?=str_repeat('&nbsp;', 40)?></u>&nbsp;/&nbsp;<?=$Contract->Исполнитель->Подписывает?>&nbsp;&nbsp;&nbsp;</div>
	    <br>
            <div style="text-align: center;"><sup>&nbsp;М.П.</sup></div>
	    <?if(0){?>
            <img src="<?=$Contract['Исполнитель.Печать']?>" border="0" style="position: relative; width: 5.5cm; float: right; top: -60px; left: -20px;" >
	    <?}?>

        </td>
        </tr>
    <?}?>
</table>
<?
	return ob_get_clean();
    }
    
    private function printFooterSEOLAB($Contract) {
	ob_start();
?>
<table class="requisites">
    <tr>
	<td width="20%" style="border:0; width: 33%; text-align: left; vertical-align: top;"><b>Исполнитель</b></td>
	<td width="60%" style="border:0; width: 33%; text-align: left; vertical-align: top;"><b>Заказчик</b></td>
	<td width="20%" style="border:0; width: 33%; text-align: left; vertical-align: top;"><b>Плательщик</b></td>
    </tr>
    <tr>
        <td width="20%" style="border:0; width: 33%; border-right: 1px solid #000; text-align: left; vertical-align: top;">
	    <?=$Contract->Исполнитель->ФормаСобственности?><br><?=$Contract->Исполнитель->Имя?><br>
	    <?
		$this->print_field('Юридический адрес: ',$Contract->Исполнитель->ЮридическийАдрес);
		$this->print_field('Почтовый адрес: ',$Contract->Исполнитель->ПочтовыйАдрес);
		$this->print_field('Р/с: ',$Contract->Исполнитель->РасчетныйСчет,'');
		$this->print_field(' в ',$Contract->Исполнитель->Банк);
		$this->print_field('Адрес банка: ',$Contract->Исполнитель->БанкАдрес);
		$this->print_field('К/счет: ',$Contract->Исполнитель->КорСчет);
		$this->print_field('УНП: ',$Contract->Исполнитель->УНП);
		$this->print_field('ИНН: ',$Contract->Исполнитель->ИНН);
		$this->print_field('КПП: ',$Contract->Исполнитель->КПП);
		$this->print_field('ОКПО: ',$Contract->Исполнитель->ОКПО);
		$this->print_field('БИК: ',$Contract->Исполнитель->БИК);
		$this->print_field('ОГРН: ',$Contract->Исполнитель->ОГРН);
		$this->print_field('Паспорт: ',$Contract->Исполнитель->Паспорт);
		$this->print_field('Личный номер: ',$Contract->Исполнитель->ЛичныйНомер);
		$this->print_field('Телефон: ',$Contract->Исполнитель->Телефон,'');
		$this->print_field(', Факс: ',$Contract->Исполнитель->Факс);
	    ?>
	</td>
        <td width="60%" style="border:0; width: 33%; border-right: 1px solid #000;  text-align: left; vertical-align: top;">
	    <?
	    $this->print_field('ФИО: ',empty($Contract['fields']['z.fio'])? (str_repeat('&nbsp;', 40).'<br>'.str_repeat('&nbsp;', 70)):$Contract['fields']['z.fio'],'<br><br>',1);
	    $this->print_field('Адрес: ',empty($Contract['fields']['z.address'])? (str_repeat('&nbsp;', 37).'<br>'.str_repeat('&nbsp;', 70).'<br>'.str_repeat('&nbsp;', 70)):@$Contract['fields']['z.address'],'<br><br>',1);
	    $this->print_field('Документ, удостоверяющий личность: ',empty($Contract['fields']['z.document'])? ('<br>'.str_repeat('&nbsp;', 70).'<br>'.str_repeat('&nbsp;', 70)):@$Contract['fields']['z.document'],'<br><br>',1);
	    $this->print_field('серия: ','<u>'.str_repeat('&nbsp;', 7).'</u>',' ');
	    $this->print_field('номер: ','<u>'.str_repeat('&nbsp;', 30).'</u>','<br><br>');
	    $this->print_field('дата выдачи: ','<u>'.str_repeat('&nbsp;', 40).'</u>','<br><br>');
	    $this->print_field('кем выдан: ','<u>'.str_repeat('&nbsp;', 60).'<br>'.str_repeat('&nbsp;', 60).'</u>','<br><br>');
	    $this->print_field('Контактный телефон ',empty($Contract['fields']['z.phone'])? (str_repeat('&nbsp;', 14).'<br>'.str_repeat('&nbsp;', 70)):@$Contract['fields']['z.phone'],'<br><br>',1);
	    $this->print_field('Идентификационный номер ',empty($Contract['fields']['z.in'])? (str_repeat('&nbsp;', 45)):@$Contract['fields']['z.in'],'<br><br>',1);
	    ?>
	</td>
	<td width="20%" style="border:0; width: 33%; border-left: 1px solid #000;  text-align: left; vertical-align: top;">
	    <?=$Contract->Заказчик->ФормаСобственности?><br><?=$Contract->Заказчик->Имя?><br>
	    <?
		$this->print_field('Юридический адрес: ',$Contract->Заказчик->ЮридическийАдрес);
		$this->print_field('Почтовый адрес: ',$Contract->Заказчик->ПочтовыйАдрес);
		$this->print_field('Р/с: ',$Contract->Заказчик->РасчетныйСчет,'');
		$this->print_field(' в ',$Contract->Заказчик->Банк);
		$this->print_field('Адрес банка: ',$Contract->Заказчик->БанкАдрес);
		$this->print_field('К/счет: ',$Contract->Заказчик->КорСчет);
		$this->print_field('УНП: ',$Contract->Заказчик->УНП);
		$this->print_field('ИНН: ',$Contract->Заказчик->ИНН);
		$this->print_field('КПП: ',$Contract->Заказчик->КПП);
		$this->print_field('ОКПО: ',$Contract->Заказчик->ОКПО);
		$this->print_field('БИК: ',$Contract->Заказчик->БИК);
		$this->print_field('ОГРН: ',$Contract->Заказчик->ОГРН);
		$this->print_field('Паспорт: ',$Contract->Заказчик->Паспорт);
		$this->print_field('Личный номер: ',$Contract->Заказчик->ЛичныйНомер);
		$this->print_field('Телефон: ',$Contract->Заказчик->Телефон,'');
		$this->print_field(', Факс: ',$Contract->Заказчик->Факс);
	    ?>
	</td>
    </tr>
    <tr>
	<td align="right" style="vertical-align: top;border-right: 1px solid #000;">
		<br>
		<div style="font-weight: bold; text-align: left;">Подпись:<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></div><br>
            <div style="font-weight: bold; text-align: right;"><?=$Contract->Исполнитель->Подписывает?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div style="text-align: left;"><sup>&nbsp;&nbsp;М.П.</sup></div>
        </td>
	<td align="right" style="vertical-align: top;border-right: 1px solid #000;">
		<br>
		<div style="font-weight: bold; text-align: left;">Подпись:<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></div>
        </td>
        <td align="right" style="vertical-align: top;">
	    <br>
	    <div style="font-weight: bold; text-align: left;">Подпись:<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></div><br>
            <div style="font-weight: bold; text-align: right;"><?=$Contract->Заказчик->Подписывает?>&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div style="text-align: left;"><sup>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;М.П.</sup></div>
        </td>
    </tr>
</table>
<?
	return ob_get_clean();
    }
    
    public function placedateComplete($Args) {
	    $Place = $this->arg('Place','г. Минск',$Args);
	    $Date = $this->arg('Date',  TimeStamp::Now()->format('d.m.Y'),$Args);
	    ?>
<table width='100%'>
    <tr>
	<td><p style="text-align: left;"><?=$Place?></p></td><td><p style="text-align: right;"><?=$Date?></p></td>
    </tr>
</table>
	<?
    }
    
    
    public function Begin(){}

    public function End($innerHtml)
    {
	$Document = $this->arg('Document');
	$Contract = $Document->Договор;
	$Print = $this->arg('Print','view');
	$this->documentElements['{{{DOCUMENT.FOOTER}}}'] = $this->printFooter($Contract);
	$this->documentElements['{{{DOCUMENT.FOOTER.SEOLAB}}}'] = $this->printFooterSEOLAB($Contract);
	$this->documentElements['{{{DOCUMENT.SHORTFOOTER}}}'] = $this->printFooter($Contract,true);
	$this->documentElements['{{{DOCUMENT.SHORTFOOTER.SEOLAB}}}'] = $this->printFooterSEOLAB($Contract,true);
	ob_start();
?>
<? if($Print !== 'view'){?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
<?}?>	
<style type="text/css"><?include __DIR__.'/document.css';?></style>
<div class="document <?=$Print?>">
    <table>
	<tr>
	    <td colspan="2">
		<?=str_replace(array_keys($this->documentElements), array_values($this->documentElements), $innerHtml)?>
	    </td>
	</tr>
    </table>
</div>
<?if($Print !== 'view'){?>	
    </body>
</html>
<?}?>
<?
if($Print == 'view') ob_end_flush(); else {
    if($Print=='doc') {
	
	$Name = "{$Document->Name} {$Document->No}.doc";
	
	header("Content-Type: application/vnd.ms-word; charset=utf-8");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=\"$Name\"");
	exit;
    }
    if($Print=='html') {
	
	$Name = "{$Document->Name} {$Document->No}.html";
	
	header("Content-Type: text/html; charset=utf-8");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	exit;
    }
}
?>

<?php  } } ?>