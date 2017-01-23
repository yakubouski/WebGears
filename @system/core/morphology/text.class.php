<?php
namespace Morphology;
class Text 
{
    public $SourceText;
    public $Tokens;
    public function __construct($Text) {
	$this->SourceText = $Text;
	$this->Tokens = [];
    }
    /**
     * Получить список токенов
     * @return \Object\Iterator
     */
    public function Tokens() {
	empty($this->Tokens) && !empty($this->SourceText) && $this->Tokens = \Morphology\Tokenizer::Tokenize($this->SourceText);
	return new \Object\Iterator($this->Tokens);
    }
    
    public function __toString() {
	return $this->SourceText;
    }
}
