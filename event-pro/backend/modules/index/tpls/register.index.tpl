<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />  
	<meta http-equiv="x-ua-compatible" content="IE=10">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="cleartype" content="on">
	<meta name="mobile-web-app-capable" content="yes">
        <title><?=APP_NAME?> &raquo; Регистрация организатора</title>
        <link href="/css/event-pro.css" rel="stylesheet">
        <link href="/css/event-pro/register.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/@js/ui.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <header>
            <a class="button" href="/" style="margin-left: 2px;"><img src="/img/event-pro/logo.png" align="absmiddle" width="36"></a>
            <h1 title="Регистрация организатора">Новый организатор</h1>
        </header>
        <main>
            <form id="form-register" action="?add" method="post">
                <table class="maximize">
                    <tbody>
                    <tr>
                        <th>URL размещения</th><td class="content-box t-right">http://</td><td><input type="text" name="e[url]" required="" placeholder="домен организации"></td><td  class="content-box">.<?=DOMAIN?></td>
                    </tr>
                    </tbody>
                    <tbody>
                        <tr class="caption"><td colspan="4">Контактная информация</td></tr>
                        <tr>
                            <th>Контактное лицо</th><td colspan="3"><input type="text" required="" name="e[contact]" placeholder="Фамилия Имя контактного лица"></td>
                        </tr>
                        <tr>
                            <th>Эл.почта</th><td colspan="3"><input type="text" required="" name="e[email]" pattern="[\w-\.]+@[\w-\.]+\.\w+" placeholder="Адрес электронной почты"></td>
                        </tr>
                        <tr>
                            <th>Контактный телефон</th><td colspan="3"><input type="text" required="" name="e[phone]" pattern="\+?(?:375|80?).*?\d{2,3}.*?\d+.*?\d+.*?\d+" placeholder="Телефон в международном формате (+375 17 123 45 67)"></td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr class="caption"><td colspan="4">Организатор мероприятия</td></tr>
                        <tr>
                            <th></th>
                            <td colspan="3">
                                <label><input type="radio" name="o[type]" data-ui-switch='.o-fields-legal' value="legal" data-ui-switch-hide=".o-fields-individual,.o-fields-personal" checked=""> Юридическое лицо</label>
                                <label><input type="radio" name="o[type]" data-ui-switch='.o-fields-individual' value="individual" data-ui-switch-hide=".o-fields-legal,.o-fields-personal"> Индивидуальный предприниматель</label>
                                <label><input type="radio" name="o[type]" data-ui-switch='.o-fields-personal' value="personal" data-ui-switch-hide=".o-fields-legal,.o-fields-individual"> Физическое лицо</label>
                            </td>
                        </tr>
                        <tr class="o-fields-legal">
                            <th>Наименование организации</th><td colspan="3"><input type="text" name="l[name]" data-required="" placeholder="ООО &laquo;АБРАДОКС&raquo;"></td>
                        </tr>
                        <tr class="o-fields-legal">
                            <th>Полное наименование</th><td colspan="3"><input type="text" name="l[full]" placeholder="Общество с ограниченной ответственностью &laquo;АБРАДОКС&raquo;"></td>
                        </tr>
                        <tr class="o-fields-individual ">
                            <th>Фамилия Имя Отчество</th><td colspan="3"><input type="text" data-required="" name="i[name]" placeholder="ФИО полностью"></td>
                        </tr>
                        <tr class="o-fields-personal ">
                            <th>Фамилия Имя Отчество</th><td colspan="3"><input type="text" name="p[name]" data-required="" placeholder="ФИО полностью"></td>
                        </tr>
                        <tr class="o-fields-personal">
                            <th rowspan="4" style="vertical-align: top;">Паспортные<br>данные</th>
                            <td class="content-box t-right">№ паспорта</td> 
                            <td colspan="2"><input type="text" name="p[no]" data-required="" pattern="\w\w\d{9}" placeholder="Серия и номер паспорта (MP100000000)"></td>
                        </tr>
                        <tr class="o-fields-personal">
                            <td class="content-box t-right">Выдан</td> 
                            <td colspan="2"><input type="text" name="p[date]" data-required="" pattern="\d{1,2}.\d{1,2}.\d{2}\d{2}" placeholder="Дата выдачи (01.01.1980)"></td>
                        </tr>
                        <tr class="o-fields-personal">
                            <td class="content-box t-right">Кем выдан</td> 
                            <td colspan="2"><input type="text" name="p[who]" data-required="" placeholder="Кем выдано, название (Первомайское РУВД, г.Минск) "></td>
                        </tr>
                        <tr class="o-fields-personal">
                            <td class="content-box t-right">Личный<br>номер</td> 
                            <td colspan="2"><input type="text" name="p[pin]" data-required="" pattern="\w{15,20}" placeholder="Личные идентификационный номер"></td>
                        </tr>
                        <tr class="o-fields-individual">
                            <th rowspan="3" style="vertical-align: top;">Свидетельство<br>гос. регистрации</th>
                            <td class="content-box t-right">№</td> 
                            <td colspan="2"><input type="text" name="i[no]" data-required="" placeholder="№ свидетельства"></td>
                        </tr>
                        <tr class="o-fields-individual">
                            <td class="content-box t-right">Дата</td> 
                            <td colspan="2"><input type="text" name="i[date]" data-required="" pattern="\d{1,2}.\d{1,2}.\d{2}\d{2}" placeholder="Дата выдачи (01.01.2000)"></td>
                        </tr>
                        <tr class="o-fields-individual">
                            <td class="content-box t-right">Кем</td> 
                            <td colspan="2"><input type="text" name="i[who]" data-required="" placeholder="Кем выдано"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <th>УНН</th><td colspan="3"><input type="text" name="o[unn]" pattern="\d{9}" data-required="" placeholder="Учетный номер налогоплательщика (9 цифр)"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <th>ОКПО</th><td colspan="3"><input type="text" name="o[okpo]" pattern="\d{10,15}" placeholder="Код общегосударственного классификатора предприятий и организаций"></td>
                        </tr>
                        <tr>
                            <th>Почтовый адрес</th><td colspan="3"><input type="text" name="o[post]" data-required="" placeholder="Беларусь, 220000, пр-т. Независимости 21 оф. 750"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <th>Юридический адрес</th><td colspan="3"><input type="text" name="o[address]" data-required="" placeholder="Беларусь, пр-т. Независимости 21 оф. 750"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <th rowspan="5" style="vertical-align: top;">Банковские<br>реквизиты</th>
                            <td class="content-box t-right">Р/С</td> 
                            <td colspan="2"><input type="text" pattern="\d{13}" name="b[account]" placeholder="Расчетный счет (13 цифр)"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <td class="content-box t-right">Код банка</td> 
                            <td colspan="2"><input type="text"  id="bank-code" name="b[code]" list="banks-code" placeholder="Код банка (3 цифры)"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <td class="content-box t-right">МФО</td> 
                            <td colspan="2"><input type="text" id="bank-mfo" name="b[mfo]" list="banks-mfo" placeholder="Филиал (9 цифры)"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <td class="content-box t-right">Банк</td> 
                            <td colspan="2"><input type="text" id="bank-name" name="b[name]" placeholder="ОАО &quot;Технобанк&quot; (Наименование банка, сокращенное с аббревиатурой)"></td>
                        </tr>
                        <tr class="o-fields-individual o-fields-legal">
                            <td class="content-box t-right">Адрес</td> 
                            <td colspan="2"><input type="text" id="bank-address" name="b[address]" placeholder="г.Минск, ул. Мельникайте, 8"></td>
                        </tr>
                        
                    </tbody>
                    <tbody>
                        <tr>
                            <th></th>
                            <td colspan="10" style="padding-top: 24px; ">
                                
                                <label><input type="checkbox" required=""> Вы ознакомились и согласны</label> с условиями предоставления услуг на основании договора <a href="#contract">Публичной оферты</a>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td colspan="10" style="padding: 24px; " class="t-center">
                                <button class="ui-button-blue">Зарегистрировать мероприятие</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </main>
        <datalist id="banks-mfo">
            <?foreach(Consts::Banks() as $b){?>
            <option data-address="<?=_h($b['address'],ENT_COMPAT)?>" data-code="<?=_h($b['code'])?>" data-mfo="<?=_h($b['code'])?>" data-bank="<?=_h($b['bank'],ENT_COMPAT)?>" value="<?=_h($b['mfo'])?>"><?=_h($b['mfo'])?></option>
            <?}?>
        </datalist>
        <datalist id="banks-code">
            <?foreach(Consts::Banks() as $b){?>
            <option data-address="<?=_h($b['address'],ENT_COMPAT)?>" data-mfo="<?=_h($b['mfo'])?>" data-code="<?=_h($b['code'])?>" data-bank="<?=_h($b['bank'],ENT_COMPAT)?>" value="<?=_h($b['code'])?>"><?=_h($b['code'])?></option>
            <?}?>
        </datalist>
        <script>
            $('input[list]').change(function(){
                var opts = $('#'+$(this).attr('list')+'>option[value="'+$(this).val()+'"]');
                if($(opts).length) {
                    $('#bank-address').val() === '' && $('#bank-address').val($(opts).data('address'));
                    $('#bank-name').val() === '' && $('#bank-name').val($(opts).data('bank'));
                    $('#bank-code').val() === '' && $('#bank-code').val($(opts).data('code'));
                    $('#bank-mfo').val() === '' && $('#bank-mfo').val($(opts).data('mfo'));
                }
            });
            $('#form-register :radio').change(function(){
                $('#form-register input[data-required],select[data-required]').prop('required',false);
                $('#form-register '+$(this).data('ui-switch')+' input[data-required],select[data-required]').prop('required',true);
            });
            
        </script>
    </body>
</html>
