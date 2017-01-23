<?php
class I18NLocale  {
    
    /**
     * Get string for money (RUB)
     * @param float $amount Amount of money
     * @param bool $zeroForKopeck If false, then zero kopecks ignored
     * @return string   In-words representation of money's amount
     * @throws \InvalidArgumentException
     */
    function StrMoney($Amount,$Currency, $StripPenny=false,$WithDigits=false)
    {
        $words = array();
        $isNegative = $Amount < 0;
	$Amount = abs($Amount);
        $Amount = round($Amount, $this->CURRENCY[$Currency]['Round']);

	
	if($WithDigits) {
	    $words[] = $this->Number($Amount, $StripPenny ? 0:$this->CURRENCY[$Currency]['Round'] );
	    if(!empty($StripPenny)) {
		$remainder = $this->_getFloatRemainder($Amount, $this->CURRENCY[$Currency]['Round']);
		if ($remainder < 10 && strlen($remainder) == 1) $remainder *= 10;
		$words[] = trim('('.$this->StrNumber((int)$Amount,I18N::MALE,$this->CURRENCY[$Currency]['DollarNames']) . ' '.$this->StrNumber($remainder, I18N::FEMALE, $this->CURRENCY[$Currency]['PennyNames']).')');
	    }
	    else {
		$words[] = '('.$this->StrNumber((int)$Amount,I18N::MALE).')';
	    }
	}
	else {
	    $words[] = $this->StrNumber((int)$Amount, I18N::MALE,$this->CURRENCY[$Currency]['DollarNames']);
	    if(!empty($StripPenny)) {
		$remainder = $this->_getFloatRemainder($Amount, $this->CURRENCY[$Currency]['Round']);

		if ($remainder < 10 && strlen($remainder) == 1) $remainder *= 10;

		$words[] = $this->StrNumber($remainder, I18N::FEMALE, $this->CURRENCY[$Currency]['PennyNames']);
	    }
	}
        return trim(implode(' ', $words));
    }
    
    /**
     * Форматирование числа
     * @param double $Number
     * @param int $Decimals
     * @return string
     */
    public function Number($Number,$Decimals=0) {
	return number_format($Number, $Decimals, $this->DecPoint , $this->ThousandsSep);
    }
    
    /**
     * Получить сумму прописью
     * @param type $Sum
     * @param type $Gender
     * @param array $Variants
     * @return string
     */
    public function StrNumber($Sum,$Gender = I18N::MALE, array $Variants=null) {
	($Variants === null) && ($Variants = ['','','']);
        (sizeof($Variants) < 3) && trigger_error(__METHOD__.': Incorrect items length (must be 3)');
        
	$Neagtive = ($Sum < 0);
	
        if ($Sum == 0) return  trim($this->NUMBER_ONES[0][0].' '.$Variants[2]);

        $result = '';
        $tmpVal = abs(round($Sum));

        //ones
        list($result, $tmpVal) = $this->_sumStringOneOrder($result, $tmpVal, $Gender, $Variants);
        //thousands
        list($result, $tmpVal) = $this->_sumStringOneOrder($result, $tmpVal,  I18N::FEMALE, $this->NUMBER_DIGITS[0]);
        //millions
        list($result, $tmpVal) = $this->_sumStringOneOrder($result, $tmpVal,  I18N::MALE, $this->NUMBER_DIGITS[1]);
        //billions
        list($result,) = $this->_sumStringOneOrder($result, $tmpVal, I18N::MALE, $this->NUMBER_DIGITS[2]);
	
        return ($Neagtive?($this->NEGATIVE.' '):'').trim($result);
    }
    
    /**
     * Get remainder of float, i.e. 2.05 -> '05'
     * @param float $value
     * @param int $signs
     * @return string
     */
    private function _getFloatRemainder($value, $signs=9)
    {
        if ($value == (int)$value) return '0';
        $signs = min($signs, sizeof($this->NUMBER_FRACTIONS));
        $value = number_format($value, $signs, '.', '');
        list(, $remainder) = explode('.', $value);
        $remainder = preg_replace('/0+$/', '', $remainder);
	return !$remainder ? '0':$remainder;
    }
    
    private function _sumStringOneOrder($prevResult, $tmpVal, $gender, array $variants)
    {
        $words = array();
        $fiveItems = $variants[2];
        $rest = $tmpVal%1000;

	if ($tmpVal == 0 || $rest < 0)
            return array($prevResult, $tmpVal);

        $tmpVal = intval($tmpVal/1000);

        //check last digits are 0
        if ($rest == 0) {
            if (!$prevResult)
                $prevResult = $fiveItems.' ';
            return array($prevResult, $tmpVal);
        }

        //hundreds
        $words[] = $this->NUMBER_HUNDREDS[intval($rest/100)];

        //tens
        $rest %= 100;
        $rest1 = intval($rest/10);
        $words[] = ($rest1 == 1) ?$this->NUMBER_TENS[$rest] : $this->NUMBER_TENS[$rest1];

        //ones
        if ($rest1 == 1) {
            $endWord = $fiveItems;
        }
        else {
            $amount = $rest%10;
	    if($amount) $words[] = $this->NUMBER_ONES[$amount][$gender-1];
            $endWord = I18N::Plural($amount, $variants);
        }
        $words[] = $endWord;

        $words[] = $prevResult;

	$result = trim(implode(' ', array_filter($words)));
        return array($result, $tmpVal);
    }
    
    public function StrDate($Format,$Date,&$ObjectDate=NULL) {
	if(!is_a($Date,'DateTime')) {
	    if(is_string($Date)) {
		$datetime = date_parse_from_format('Y-m-d H:i:s', $Date);
		$ObjectDate = new DateTime();
		$ObjectDate->setDate($datetime['year'], $datetime['month'], $datetime['day'])->
			setTime($datetime['hour'],$datetime['minute'],$datetime['second']);
	    }
	    elseif(is_null($Date)) {
		$ObjectDate = new DateTime();
	    }
	    else {
		$ObjectDate = DateTime::createFromFormat('U', intval($Date));
	    }
	}
	else {
	    $ObjectDate = $Date;
	}
	if(empty($ObjectDate)) {trigger_error(__METHOD__.': Invalid datetime object'); return null;}
	
	$Format = preg_replace_callback('/%[aAMF]/', function($_) use($ObjectDate) {
               switch($_[0]) {
		    case '%A': return $this->WEEKDAYS_FULL[intval($ObjectDate->format('N'))]; 
                    case '%a': return $this->WEEKDAYS_SHORT[intval($ObjectDate->format('N'))]; 
                    case '%b':  return $this->MONTHS_SHORT[intval($ObjectDate->format('m'))]; 
		    case '%B':  return $this->MONTHS_FULL[intval($ObjectDate->format('m'))]; 
		    case '%F': return $this->MONTHS_GENITIVE[intval($ObjectDate->format('m'))];
                }
            }, $Format);
	    return strftime($Format, $ObjectDate->getTimestamp());
    }
}

class ru_RU extends I18NLocale {
    
    public $NEGATIVE = 'минус';
    
    public $NUMBER_FRACTIONS = array(
        array('десятая', 'десятых', 'десятых'),
        array('сотая', 'сотых', 'сотых'),
        array('тысячная', 'тысячных', 'тысячных'),
        array('десятитысячная', 'десятитысячных', 'десятитысячных'),
        array('стотысячная', 'стотысячных', 'стотысячных'),
        array('миллионная', 'милллионных', 'милллионных'),
        array('десятимиллионная', 'десятимилллионных', 'десятимиллионных'),
        array('стомиллионная', 'стомилллионных', 'стомиллионных'),
        array('миллиардная', 'миллиардных', 'миллиардных'),
    ); //Forms (1, 2, 5) for fractions

    public $NUMBER_ONES = array(
        array('ноль', 'ноль', 'ноль'),
        array('один', 'одна', 'одно'),
        array('два', 'две', 'два'),
        array('три', 'три', 'три'),
        array('четыре', 'четыре', 'четыре'),
        array('пять', 'пять', 'пять'),
        array('шесть', 'шесть', 'шесть'),
        array('семь', 'семь', 'семь'),
        array('восемь', 'восемь', 'восемь'),
        array('девять', 'девять', 'девять'),
    ); //Forms (MALE, FEMALE, NEUTER) for ones

    public $NUMBER_TENS = array(
        0 => '',
        //1 - special variant
        10 => 'десять',
        11 => 'одиннадцать',
        12 => 'двенадцать',
        13 => 'тринадцать',
        14 => 'четырнадцать',
        15 => 'пятнадцать',
        16 => 'шестнадцать',
        17 => 'семнадцать',
        18 => 'восемнадцать',
        19 => 'девятнадцать',
        2 => 'двадцать',
        3 => 'тридцать',
        4 => 'сорок',
        5 => 'пятьдесят',
        6 => 'шестьдесят',
        7 => 'семьдесят',
        8 => 'восемьдесят',
        9 => 'девяносто',
    ); //Tens

    public $NUMBER_HUNDREDS = array(
        0 => '',
        1 => 'сто',
        2 => 'двести',
        3 => 'триста',
        4 => 'четыреста',
        5 => 'пятьсот',
        6 => 'шестьсот',
        7 => 'семьсот',
        8 => 'восемьсот',
        9 => 'девятьсот',
    ); //Hundreds
    
    public $ThousandsSep = ' ';
    public $DecPoint = '.';
    
    public $NUMBER_DIGITS = array(
	array('тысяча', 'тысячи', 'тысяч'),
	array('миллион', 'миллиона', 'миллионов'),
	array('миллиард', 'миллиарда', 'миллиардов')
    );
    
    public $CURRENCY = array(
	'BYR'=>array(
	    'Round'=>0,'Peny'=>false,'Sign'=>'бел. руб.',
	    'DollarNames'=>array('белорусский рубль','белорусских рублей','белорусских рублей'),
	    'PennyNames'=>array('','','')),
	'RUB'=>array(
	    'Round'=>2,'Peny'=>true,'Sign'=>'руб.',
	    'DollarNames'=>array('рубль','рубля','рублей'),
	    'PennyNames'=>array('копейка', 'копейки', 'копеек')),
	'USD'=>array(
	    'Round'=>2,'Peny'=>true,'Sign'=>'&dollar;',
	    'DollarNames'=>array('&dollar;','&dollar;','&dollar;'),
	    'PennyNames'=>array('&cent;', '&cent;', '&cent;')),
	'EUR'=>array(
	    'Round'=>2,'Peny'=>true,'Sign'=>'&euro;',
	    'DollarNames'=>array('&euro;','&euro;','&euro;'),
	    'PennyNames'=>array('&cent;', '&cent;', '&cent;')),
    );
    
    public $WEEKDAYS_SHORT = [1=>'пн',2=>'вт',3=>'ср',4=>'чт',5=>'пт',6=>'сб',7=>'вс'];
    public $WEEKDAYS_FULL = [1=>'Понедельник',2=>'Втоник',3=>'Среда',4=>'Четверг',5=>'Пятница',6=>'Суббота',7=>'Воскресенье'];
    public $WEEKDAYS_SHORT3 = [1=>'пнд',2=>'втр',3=>'срд',4=>'чтв',5=>'птн',6=>'сбт',7=>'вск'];
    
    public $MONTHS_SHORT = [1=>'янв',2=>'фев',3=>'мар',4=>'апр',5=>'май',6=>'июн',7=>'июл',8=>'авг',9=>'сен',10=>'окт',11=>'ноя',12=>'дек'];
    public $MONTHS_FULL = [1=>'Январь',2=>'Февраль',3=>'Март',4=>'Апрель',5=>'Май',6=>'Июнь',7=>'Июль',8=>'Август',9=>'Сентябрь',10=>'Октябрь',11=>'Ноябрь',12=>'Декабрь'];
    public $MONTHS_GENITIVE = [1=>'Января',2=>'Февраля',3=>'Марта',4=>'Апреля',5=>'Мая',6=>'Июня',7=>'Июля',8=>'Августа',9=>'Сентября',10=>'Октября',11=>'Ноября',12=>'Декабря'];
}

class I18N
{
    const MALE = 1; //sex - male
    const FEMALE = 2; //sex - female
    const NEUTER = 3; //sex - neuter

    /**
     * @param string $Locale
     * @return \ru_RU
     */
    static public function LC($Locale=APP_LOCALE) {
	static $Locales = []; !isset($Locales[$Locale]) && $Locales[$Locale] = new $Locale;
	return $Locales[$Locale];
    }

    static public function StrNumber($Sum) {
	return self::LC()->StrNumber($Sum);
    }

    /**
     * Форматирование даты в текущей локали
     * @param string $Format
     * @param DateTime|string|int $Date
     * @return string
     */
    static public function StrDate($Format,$Date=NULL,&$ObjectDate=NULL) {
	return self::LC()->StrDate($Format, $Date,$ObjectDate);
    }
    
    /**
     * Число с форматированием
     * @param float $Number
     * @param int $Decimals
     * @return string
     */
    static public function Number($Number,$Decimals=0) {
	return self::LC()->Number($Number, $Decimals);
    }
    
    /**
     * Деньги порписью
     * @param float $Amount
     * @param string $Currency Валюта, BYR|RUB|USD|EUR
     * @param bool $StripPenny Не ввыводить копейки
     * @param bool $WithDigits Выводить в формате %.2f (%s)
     * @return string Description
     */
    static public function StrMoney($Amount, $Currency, $StripPenny=false, $WithDigits=false) {
	return self::LC()->StrMoney($Amount, $Currency, $StripPenny, $WithDigits);
    }
    
    /**
     * Выбор склонения числа
     * @param int $Value
     * @param array $Variants варианты сколения. Должно быть три варианта: array('object', 'objects', 'objects')
     * @return string
     */
    static public function Plural($Value,$Variants) {
	if (sizeof($Variants) < 3) ($Variants = implode($Variants,array_fill(0, 3, '')));
	
        $Value = abs($Value);
        $mod10 = $Value%10;
        $mod100 = $Value%100;
	return ($mod10 == 1 && $mod100 != 11) ? $Variants[0] : ( ($mod10 >= 2 && $mod10 <= 4 && !($mod100 > 10 && $mod100 < 20)) ? $Variants[1] : $Variants[2]);
    }
}
