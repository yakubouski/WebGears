<?php
namespace Data;

/**
 * @property DateTable $dataTable Description
 * @property int $dataIterator Description
 */
class TableIterator implements \Iterator {
    private $dataTable; 
    private $dataIterator = 0;
    private $dataColumn = false;
    
    protected function __construct(Table &$dataTable,$ColumnIterator=false) {
	$this->dataTable = $dataTable;
	$this->dataColumn = $ColumnIterator;
    }
    public function current() {
	return array_combine(array_keys($this->dataTable->tableColumns), $this->dataTable->tableRows[$this->dataIterator]);
    }

    public function key() {
	return $this->dataColumn !== false ? 
		$this->dataTable->tableRows[$this->dataIterator][$this->dataTable->tableColumns[$this->dataColumn]] : 
		$this->dataIterator;
    }

    public function next() {
	++$this->dataIterator;
    }

    public function rewind() {
	$this->dataIterator = 0;
    }

    public function valid() {
	return isset($this->dataTable->tableRows[$this->dataIterator]);
    }

    public function Avg($Column,$FilterColumn=false,$FilterHayStack=[]) {
	$ColumnIndex = $this->dataTable->tableColumns[$Column];
	$Count = 0;
	$Sum = 0;
	if(empty($FilterColumn)) {
	    foreach($this->dataTable->tableRows as $r) {
		$Count ++;
		$Sum += $r[$ColumnIndex];
	    }
	}
	else {
	    $FilterColumnIndex = $this->dataTable->tableColumns[$FilterColumn];
	    foreach($this->dataTable->tableRows as $r) { if(!in_array($r[$FilterColumnIndex], $FilterHayStack)) continue;
		$Count ++;
		$Sum += $r[$ColumnIndex];
	    }
	}
	return $Count ? 0 : ($Sum/$Count);
    }
    public function Min($Column,$HayStack=[]) {
	
    }
    public function Max($Column,$HayStack=[]) {
	
    }
    public function Sum($Column,$HayStack=[]) {
	
    }
}
class Table extends TableIterator implements \ArrayAccess,  \Countable
{
    protected $tableColumns,$tableRows;
    public function __construct($Columns=[],$Rows=[]) {
	$this->tableColumns = $Columns;
	$this->tableRows = $Rows;
	parent::__construct($this);
    }
    public function count($mode = 'COUNT_NORMAL') {
	return count($this->tableRows,$mode);
    }
    public function offsetExists($offset) { return isset($this->tableColumns[$offset]); }
    public function offsetGet($offset) { return new TableIterator($this,$offset); }
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    
    public function __isset($offset) {
	return isset($this->tableColumns[$offset]);
    }
    
    public function __get($offset) {
	return $this->tableRows[0][($this->tableColumns[$offset])];
    }
}
