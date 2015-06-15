<?php
class Html {

    /**
     * Создает объект шаблонизатора
     * @param string $tplLocation
     * @param array $tplArgs
     * @return \Html\Template
     */
    static public function Template($tplLocation, $tplArgs = [],$RawPath=false) {
	require_once 'html/template.class.php';
	return new \Html\Template($tplLocation, $tplArgs,$RawPath);
    }
    
    static public function WidgetState($widget,$param,$state=null) {
	if(!is_null($state)) {
	    $_SESSION["@WIDGET:$widget:$param"] = $state;
	}
	else {
	    return isset($_SESSION["@WIDGET:$widget:$param"]) ? $_SESSION["@WIDGET:$widget:$param"] : null;
	}
    }
    static public function WidgetStateUnset($widget,$param) {
	if(isset($_SESSION["@WIDGET:$widget:$param"])) unset($_SESSION["@WIDGET:$widget:$param"]);
    }
}

/**
 * Экранирует и преобразует спец символы в html сущности
 * @param string $text
 * @return string
 */
function _h($text, $Flags = 0) {
    return htmlspecialchars($text, $Flags);
}

/**
 * Форматирует байты в Кб,Мб,Гб,Тб
 */
function _bytes($size) {
    $size = intval($size);
    if(!$size) {
	return 'пустой';
    }
    elseif($size < (100*1024)) {
	return _num($size/1024,1) . '&nbsp;Кб';
    }
    elseif($size < (1024*1024)) {
	return _num($size/1024,1) . '&nbsp;Кб';
    }
    elseif($size < (1024*1024*1024)) {
	return _num($size/1024*1024,1) . '&nbsp;Мб';
    }
    elseif($size < (1024*1024*1024*1024)) {
	return _num($size/1024*1024*1024,1) . '&nbsp;Гб';
    }
}

/**
 * Преобразует bb-коды в html сущности, а так-же заменяет \n на <br>
 * @param string $Text поддерживаются b,i,u,red,blue,gray,yellow, а так-же ссылки начианющиеся с http://, https:// ftp://
 * @return string
 */
function _bb($Text,$Escape=false,$nl2br=true) {
    static $bbCodes = [
	'%\[s\]%'=>'<span style="text-decoration: line-through;">','%\[/s\]%'=>'</span>',
	'%\[(/)?b\]%'=>'<\1strong>',
	'%\[(/)?i\]%'=>'<\1em>',
	'%\[(/)?u\]%'=>'<\1u>',
	'%\[(/)?code\]%'=>'<\1code>',
	'%\[(/)?quote\]%'=>'<\1blockquote>',
	'%\[(/)?(sup|sub)\]%'=>'<\1\2>',
	'/\[url=(.+?)\]/'=>'<a href="\1" target="_blank">','%\[/url\]%'=>'</a>',
	'%\[img\](.*?)\[/img\]%'=>'<img src="\1" style="max-width: 10%;">',
	'%\[(/)?\*\]%'=>'<\1li>',
	'/\[color=(.+?)\]/'=>'<span style="color: \1;">','%\[/color\]%'=>'</span>',
    ];
    $listsStack = [];
    $Escape && $Text = _h($Text);
    $nl2br && $Text = nl2br($Text);
    $Text = preg_replace_callback('%\[(/?)list=?(\d*)\]%', function($m)use(&$listsStack){
	if($m[1]=='/') {
	    return array_shift($listsStack);
	}
	else {
	    if(isset($m[2]) && intval($m[2])) {
		array_unshift($listsStack, '</ol>');
		return '<ol start="'.intval($m[2]).'">';
	    }
	    else {
		array_unshift($listsStack, '</ul>');
		return '<ul>';
	    }
	}
    }, $Text);
    return preg_replace(array_keys($bbCodes),array_values($bbCodes),$Text);
}

/**
 * Возвращает форматированное значение если оно не пустое, после значения могут передоваться дополнительные параметры используемые для подстановки в форматированную строку (форматирование производится при помощи функции @see vsprintf )
 * @param string $Format
 * @param mixed $Value
 */
function _print($Format, $Value) {
    return empty($Value) ? '' : vsprintf($Format, array_slice(func_get_args(), 1));
}

/**
 * Безопасное деление числа
 * @param float $v Делимое
 * @param float $d Делитель
 * @param int $decimals округление
 * @return float
 */
function _div($v, $d, $decimals = 2) {
    return round($d ? $v / $d : 0, $decimals);
}

/**
 * Получение процента от числа
 * @param float $v
 * @param float $max
 * @param int $decimals
 * @return float
 */
function _percent($v, $max, $decimals = 0) {
    return round($max ? (($v / $max) * 100) : 0, $decimals);
}

/**
 * Форматирует число
 * @param float $number
 * @param int $decimals
 * @param string $dec_point
 * @param string $thousands_sep
 * @return string
 */
function _num($number, $decimals = 0, $dec_point = ',', $thousands_sep = '&nbsp;') {
    return number_format($number, $decimals, $dec_point, $thousands_sep);
}

/**
 * Форматирует значение согласно формату телефонного номера
 * @param string $value
 * @return string
 */
function _phone($value) {
    return preg_replace('/(\d+)(\d{2})(\d{7})$/', '+\1&nbsp;\2&nbsp;\3', preg_replace('/\D/', '', $value));
}

function _options($Values, $Default, $AssocKey = null, $AssocValue = null) {
    $OptList = [];
    if (!is_null($AssocKey) && !is_null($AssocValue)) {
	foreach ($Values as $it) {
	    if (isset($it[$AssocKey]) && isset($it[$AssocValue])) {
		$Selected = $Default == $it[$AssocKey] ? 'selected' : '';
		$OptList[] = '<option value="' . _h($it[$AssocKey]) . '" ' . $Selected . ' >' . _h($it[$AssocValue]) . '</option>';
	    }
	}
    } else {
	foreach ($Values as $AssocKey => $AssocValue) {
	    $Selected = $Default == $AssocKey ? 'selected' : '';
	    $OptList[] = '<option value="' . _h($AssocKey) . '" ' . $Selected . ' >' . _h($AssocValue) . '</option>';
	}
    }

    return implode(PHP_EOL, $OptList);
}

/**
 * Форматирование суммы денег прописью
 * @param float $Sum Description
 * @param string $Currency Description
 * @param bool $PrintSum Выводить в формате число (сумма прописью)
 * @return string
 */
function _money($Sum, $Currency = 'BYR', $PrintSum = false) {
    return I18N::StrMoney($Sum, $Currency, false, $PrintSum);
}

/**
 * Форматирвоание даты времени в текущей локали
 * @param string $Format 
 * @see strftime
 * @param DateTime|string|int|null $Date
 * @param DateTIme $DateTimeObject
 * @return string
 */
function _date($Format,$Date,&$DateTimeObject=NULL) {
    return I18N::StrDate($Format, $Date,$DateTimeObject);
}

/**
 * Проверяет значение 
 * @param mixed $assert !empty($assert)
 * @param mixed $true
 * @param mixed $false
 * @return mixed
 */
function _is($assert,$true,$false='') {
    return !empty($assert) ? $true : $false;
}

function _if_path($Url, $Success, $Index = 1) {
    static $PATH;
    is_null($PATH) && ($PATH = explode('/', !empty($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'],'/\\') : '/'));
    $Url = explode('/', trim($Url,'/\\'));
    return implode('/', array_slice($PATH,0, $Index)) ==  implode('/', array_slice($Url,0, $Index)) ? $Success : '';
}
