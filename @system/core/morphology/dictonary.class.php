<?php
namespace Morphology;
class DictonaryTerm {
    public $Frequency;
    public $Positions;
    public $Type;
    public $Stem;
    public $Dictonary;
    public $TF,$IDF,$TFIDF;
    public function __construct(\Morphology\Token $Token,  \Morphology\Dictonary &$Dic) {
	$this->Stem = $Token->Stem;
	$this->Frequency = 0;
	$this->Positions = [];
	$this->Type = $Token->Type;
	$this->Dictonary =& $Dic;
	$this->TF = NULL;
	$this->IDF = NULL;
	$this->TFIDF = NULL;
    }
    public function TF() {
	is_null($this->TF) && ($this->TF = $this->Dictonary->CountTokens ? ($this->Frequency / $this->Dictonary->CountTokens) : 0.0);
	return $this->TF;
    }
    public function IDF() {
	is_null($this->IDF) && ($this->IDF = log(count($this->Dictonary->Texts) / count(array_unique(array_column($this->Positions, 'Text')))));
	return $this->IDF;
    }
    public function TFIDF() {
	is_null($this->TFIDF) && ($this->TFIDF = $this->TF() * $this->IDF());
	return $this->TFIDF;
    }
}
class Dictonary implements \ArrayAccess,  \Countable
{
    public $Texts = [];
    public $Dictonary = [];
    public $CountTokens = 0;
    public $CountStopTokens = 0;
    public $Vectors;
    public function __construct() {
	$this->Texts = [];
    }
    /**
     * Добавить текст к словарю
     * @param string $Text
     */
    public function Add($Text) {
	$SrcText = is_a($Text, '\Morphology\Text') ? $Text : new \Morphology\Text($Text);
	$this->Texts[] = $SrcText;
	$NumText = count($this->Texts) - 1;
	foreach($SrcText->Tokens()->Iterator('Stem') as $Stem=>$Token) {
	    !isset($this->Dictonary[$Stem]) && $this->Dictonary[$Stem] = new DictonaryTerm($Token,$this);
	    $this->Dictonary[$Stem]->Frequency += 1;
	    $this->Dictonary[$Stem]->Positions[] = ['Text'=>$NumText,'Position'=>$Token->Position];
	    $this->CountTokens += 1;
	    $this->CountStopTokens += $Token->Type == \Morphology\Token::StopWord;
	}
    }
    /**
     * Возвращает количество текстов
     * @return int
     */
    public function CountTexts() {
	return count($this->Texts);
    }
    /**
     * Врзвращает токенов
     * @return int
     */
    public function CountTokens() {
	return $this->CountTokens;
    }
    /**
     * Возвращает количество стоп слов/токенов
     * @return int
     */
    public function CountStopTokens() {
	return $this->CountStopTokens;
    }
    /**
     * Возвращает размер словаря, количество уникальных токенов
     * @return int
     */
    public function Length() {
	return count($this->Dictonary);
    }
    
    /**
     * Получить текст по его номеру
     * @param int $TextNumber
     * @return \Morphology\Text
     */
    public function Text($TextNumber) {
	return $this->Texts[$TextNumber];
    }
    /**
     * Получить вектор текста относительно словаря
     * @param int $TextNumber
     * @return array
     */
    public function Vector($TextNumber) {
	if(!isset($this->Vectors[$TextNumber])) {
	    $this->Vectors[$TextNumber] = array_fill_keys(array_keys($this->Dictonary), 0);
	    foreach($this->Texts[$TextNumber]->Tokens() as $Token) {
		$this->Vectors[$TextNumber][$Token->Stem] = $Token->Type == \Morphology\Token::StopWord ? 0.1:1;
	    }
	}
	return $this->Vectors[$TextNumber];
    }
    
    /**
     * Косинусная мера сходства между двумя текстами. Строится на основании словаря без учет позиции терма в тексте
     * @param int $TextLeftIndex
     * @param int $TextRightIndex
     * @param float $TFIDF Значение меры сходства с использованием TFIDF словаря
     */
    public function CosineSimilarityMeasure($TextLeftIndex,$TextRightIndex,$IgnoreStopWords=false,&$TFIDF=0.0) {
	$TextLeft = $this->Vector($TextLeftIndex);
	$TextRight = $this->Vector($TextRightIndex);
	$SumVector = $SumTFIDFVector = 0.0;
	$SumEuclidLeft = $SumEuclidTFIDFLeft = 0.0;
	$SumEuclidRight = $SumEuclidTFIDFRight = 0.0;
	$TFIDF = 0.0;
	
	foreach ($this->Dictonary as $Stem=>$Term) { if($IgnoreStopWords && $Term->Type == \Morphology\Token::StopWord) continue;
	    $SumVector += $TextLeft[$Stem] * $TextRight[$Stem];
	    $SumEuclidLeft += ($TextLeft[$Stem] *  $TextLeft[$Stem]);
	    $SumEuclidRight += ($TextRight[$Stem] *  $TextRight[$Stem]);

	    $SumTFIDFVector += $TextLeft[$Stem] * $TextRight[$Stem] * $this->Dictonary[$Stem]->TFIDF();
	    $SumEuclidTFIDFLeft += (($TextLeft[$Stem] * $this->Dictonary[$Stem]->TFIDF) * ($TextLeft[$Stem] * $this->Dictonary[$Stem]->TFIDF));
	    $SumEuclidTFIDFRight += (($TextRight[$Stem] * $this->Dictonary[$Stem]->TFIDF) * ($TextRight[$Stem] * $this->Dictonary[$Stem]->TFIDF));
	}
	$TFIDF = $SumTFIDFVector / (sqrt($SumEuclidTFIDFLeft) * sqrt($SumEuclidTFIDFRight));
	return $SumVector / (sqrt($SumEuclidLeft) * sqrt($SumEuclidRight));
    }

    public function offsetExists($offset) {
	return isset($this->Dictonary[$offset]);
    }

    public function offsetGet($offset) {
	return $this->Dictonary[$offset];
    }

    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}

    public function count($mode = 'COUNT_NORMAL') {
	return $mode == 'COUNT_NORMAL' ? $this->CountTokens : ($this->CountTokens - $this->CountStopTokens);
    }

}
