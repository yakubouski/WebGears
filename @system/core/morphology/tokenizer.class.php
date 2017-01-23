<?php
namespace Morphology; 
require_once 'stopwords.php';
/**
 * @property string $Token
 * @property int $Position
 * @property int $Type
 * @property string $Lowercase
 * @property string $Stem
 */
class Token {
    const Email	    = 1;
    const Url	    = 2;
    const Abbr	    = 3;
    const Domain    = 4;
    const Fullname  = 5;
    const DateTime	    = 6;
    const Phone	    = 7;
    const NumRange  = 8;
    const Number    = 9;
    const Word	    = 10;
    const StopWord  = 11;
    const Unknown   = 1000;
    static public $Meta = [
	Token::Email=>	    ['Name'=>'Эл.почта'],
	Token::Url=>	    ['Name'=>'URL'],
	Token::Abbr=>	    ['Name'=>'Аббревиатура, сокращение'],
	Token::Domain=>	    ['Name'=>'Домен'],
	Token::Fullname=>   ['Name'=>'Фамилия И.О.'],
	Token::DateTime =>  ['Name'=>'Дата/Время'],
	Token::Phone=>	    ['Name'=>'Тел. номер'],
	Token::NumRange=>   ['Name'=>'Диапазон чисел'],
	Token::Number=>	    ['Name'=>'Число'],
	Token::Word=>	    ['Name'=>'Слово'],
	Token::StopWord=>   ['Name'=>'Стоп-слово'],
	Token::Unknown=>    ['Name'=>'Не определено'],
    ];
    
    public $Token,$Type,$Position,$Lowercase,$Stem;


    public function __construct($token,$type,$position) {
	$this->Token = trim($token);
	$this->Position = $position;
	$this->Stem = $this->Lowercase = 
		($type == Token::Abbr || $type == Token::Fullname || $type == Token::Word ? mb_strtolower($this->Token) : $this->Token);
	$this->Type = ($type == Token::Word && isset(StopWord::$List[$this->Lowercase]) ? Token::StopWord:$type);
	$this->Type == Token::Word && ($this->Stem = Singleton('Morphology\PorterStemmer')->Stem($this->Lowercase));
    }
    
    public function __toString() {
	return $this->Token;
    }
}
class Tokenizer 
{
    static private $Patterns = [
	Token::Email=>'([\w.-]+@[\w+.]+)',
	Token::Url=>'(\w+://[^\s]+)',
	Token::Abbr=>'(\b[а-яa-z]{1,2}\.\s*[а-яa-z]\.)|(\b[А-ЯЁA-Z]+\s*-\s*[А-ЯЁA-Z\d-]+)|(\b[А-ЯA-Z][А-ЯA-Z.]+)',
	Token::Domain=>'((?:www\.)?[\w.]+\.(?i)[а-яa-z]+(?-i)(?:/[^\s]+)?)',
	Token::Fullname=>'(\b(?:[A-ZА-Я]{1}[a-zа-я]{1,}\s{1}[A-ZА-Я]{1}[.]{1}\s?[AZА-Я]{1}[.]{1}|[A-ZА-Я]{1}[.]))',
	Token::DateTime => '((?:\d{1,2}[./-]\d{2}[./-]\d{2,4})|(?:\d{4}[./-]\d{2}[./-]\d{1,2})|(?:(?i)\d{1,2}\s+[а-яa-z]+\s+\d{2,4})(?-i)|(?:\d{1,2}:\d{2}:?\d{0,2})|(?:\d{4}(?:\s*-[a-zа-я])?\s*[a-zа-я]\.))',
	Token::Phone=>'(\+\d{1,3}[\s(-.]?\d{1,3}[\s)-.]?[\d-.]+)',
	Token::NumRange=>'(\d+\s*-\s*\d+)',
	Token::Number=>'([+-]?\d+[\d.,]+)',
	Token::Word=>'(\b(?i)[a-zа-яё]+)',
	Token::Unknown=>'(\b[\w-+]+)',
    ];
    
    static public function Tokenize($Text) {
	$Tokens = [];
	if(preg_match_all('%'.implode('|',  array_values(self::$Patterns)).'%u', $Text,$Result,PREG_SET_ORDER)) {
	    foreach($Result as $i=>$w) {
		if(isset($w[1]) && !empty($w[1])) { $Tokens[] = new Token($w[1],  Token::Email,$i); }
		elseif(isset($w[2]) && !empty($w[2])) { $Tokens[] = new Token($w[2],  Token::Url,$i); }
		elseif(isset($w[3]) && !empty($w[3])) { $Tokens[] = new Token($w[3],  Token::Abbr,$i); }
		elseif(isset($w[4]) && !empty($w[4])) { $Tokens[] = new Token($w[4],  Token::Abbr,$i); }
		elseif(isset($w[5]) && !empty($w[5])) { $Tokens[] = new Token($w[5],  Token::Abbr,$i); }
		elseif(isset($w[6]) && !empty($w[6])) { $Tokens[] = new Token($w[6],  Token::Domain,$i); }
		elseif(isset($w[7]) && !empty($w[7])) { $Tokens[] = new Token($w[7],  Token::Fullname,$i); }
		elseif(isset($w[8]) && !empty($w[8])) { $Tokens[] = new Token($w[8],  Token::DateTime,$i); }
		elseif(isset($w[9]) && !empty($w[9])) { $Tokens[] = new Token($w[9],  Token::Phone,$i); }
		elseif(isset($w[10]) && !empty($w[10])) { $Tokens[] = new Token($w[10],  Token::NumRange,$i); }
		elseif(isset($w[11]) && !empty($w[11])) { $Tokens[] = new Token($w[11],  Token::Number,$i); }
		elseif(isset($w[12]) && !empty($w[12])) { $Tokens[] = new Token($w[12],  Token::Word,$i); }
		elseif(isset($w[13]) && !empty($w[13])) { $Tokens[] = new Token($w[13],  Token::Unknown,$i); }
	    }
	}
	return $Tokens;
    }
}
