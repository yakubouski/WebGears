<?php
namespace Object;
class Iterator implements \Iterator,  \Countable
{
    private $IteratorIndex = 0;
    private $IteratorArray = null;
    private $ArrayNumItems = 0;
    private $IteratorKeyVariable = null;
    
    public function Iterator($KeyVariableName=null) {
	$this->IteratorIndex = 0;
	$this->IteratorKeyVariable = $KeyVariableName;
	return $this;
    }
    public function __construct(&$Array,$KeyVariableName=null) {
	$this->IteratorIndex = 0;
	$this->IteratorArray =& $Array;
	$this->ArrayNumItems = count($Array);
	$this->IteratorKeyVariable = $KeyVariableName;
    }
    public function current() {
	return $this->IteratorArray[$this->IteratorIndex];
    }

    public function key() {
	return empty($this->IteratorKeyVariable) ? $this->IteratorIndex : 
    (is_object($this->IteratorArray[$this->IteratorIndex]) ? $this->IteratorArray[$this->IteratorIndex]->{$this->IteratorKeyVariable} : $this->IteratorArray[$this->IteratorIndex][$this->IteratorKeyVariable]);
    }

    public function next() {
	++$this->IteratorIndex;
    }

    public function rewind() {
	$this->IteratorIndex = 0;
    }

    public function valid() {
	return $this->IteratorIndex < $this->ArrayNumItems;
    }

    public function count($mode = 'COUNT_NORMAL') {
	return $this->ArrayNumItems;
    }
}
