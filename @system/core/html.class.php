<?php
class Html {

    /**
     * Создает объект шаблонизатора
     * @param string $tplLocation
     * @param array $tplArgs
     * @return \Html\Template
     */
    static public function Template($tplLocation, $tplArgs = []) {
	require_once 'html/template.class.php';
	return new \Html\Template($tplLocation, $tplArgs);
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
 * Преобразует bb-коды в html сущности, а так-же заменяет \n на <br>
 * @param string $Text поддерживаются b,i,u,red,blue,gray,yellow, а так-же ссылки начианющиеся с http://, https:// ftp://
 * @return string
 */
function _bb($Text) {
    return nl2br(preg_replace(
		    ['%\[(b|i|u)(?:\s(red|blue|gray|yellow))?[^\]]*\](.*?)\[/\1\]%i',
	'%(?:http|https|ftp)://([^/\s]*)([^\s]{0,32})([^\s]*)%si',
	'%\[a\s+([^]]+)\s*\](.+?)\[/a\]%',
	'/\[quote(?:\s+\w+="([^"]+)")?(?:\s+\w+="([^"]+)")?\s*\]/i',
	'/\[forward(?:\s+\w+="([^"]+)")?(?:\s+\w+="([^"]+)")?\s*\]/i',
	'%\[/quote\]%',
	'/\[forward(?:\s+from_id=([^"]+))?\]/i',
	'%\[/forward\]%',
		    ], ['<\1 class=\'bb-\2\'>\3</\1>',
	'<a href=\'\0\' title=\'\1\2\3\' target=\'_blank\'>\1\2</a>',
	'<a href=\'\1\'>\2</a>',
	'<blockquote><div class="quote-title">\1 \2</div><div class="quote-text">',
	'<blockquote class="forward"><div class="quote-title">\1 \2</div><div class="quote-text">',
	'</blockquote>',
	'<blockquote class="forward"><div class="quote-title">\1</div><div class="quote-text">',
	'</blockquote>',
		    ], _h($Text, ENT_NOQUOTES)));
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
		$OptList[] = '<option value="' . self::_h($it[$AssocKey]) . '" ' . $Selected . ' >' . self::_h($it[$AssocValue]) . '</option>';
	    }
	}
    } else {
	foreach ($Values as $AssocKey => $AssocValue) {
	    $Selected = $Default == $AssocKey ? 'selected' : '';
	    $OptList[] = '<option value="' . self::_h($AssocKey) . '" ' . $Selected . ' >' . self::_h($AssocValue) . '</option>';
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

function _if_path($Url, $Success, $Index = 0) {
    static $PATH;
    is_null($PATH) && ($PATH = explode('/', !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'));
    $Url = explode('/', $Url);
    return isset($PATH[$Index]) && isset($Url[$Index]) && $PATH[$Index] == $Url[$Index] ? $Success : '';
}
