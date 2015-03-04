<?php
define(DEBUG, 1);
include_once ('./@system/core/system.php');
//
//echo dirname('.dddd/dwedwerfwe').PHP_EOL;
//$nf = NumberFormatter::create('ru_RU', NumberFormatter::CURRENCY );
//echo $nf->formatCurrency(500.34, 'RUB');

//printf('Hello %2$s b %2$s %1$s','one','two');
//echo I18N::LC()->MoneyToText(50343465.43,'RUB',FALSE,TRUE);
echo I18N::StrDate('%d %F, %Y', '2015-04-01');
