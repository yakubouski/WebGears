<?php
namespace Math;

class MatrixAccess implements \ArrayAccess,  \Iterator,  \Countable {

    protected $Data;
    private $rowIndex,$numCols,$DefaultValue;
    protected $It;

    public function __construct(&$Data=[],$Row=0,$NumCols=0,$Default=0.0) {
	$this->Data =& $Data;
	$this->rowIndex = $Row*$NumCols;
	$this->numCols = $NumCols;
	$this->It = 0;
	$this->DefaultValue = $Default;
    }
    
    public function offsetExists($offset) {
	return $offset>=0 && $offset < $this->numCols;
    }

    public function offsetGet($offset) {
	return $offset>=0 && $offset < $this->numCols ? (isset($this->Data[$this->rowIndex + $offset])?$this->Data[$this->rowIndex + $offset]:$this->DefaultValue) : NULL;
    }

    public function offsetSet($offset, $value) {
	($offset>=0 && $offset < $this->numCols) && $this->Data[$this->rowIndex + $offset] = $value;
    }

    public function offsetUnset($offset) {}

    public function count($mode = 'COUNT_NORMAL') {
	return $this->numCols;
    }

    public function current() {
	return isset($this->Data[$this->rowIndex + $this->It]) ? $this->Data[$this->rowIndex + $this->It] : $this->DefaultValue;
    }
    public function key() {
	return $this->It;
    }
    public function next() {
	++$this->It;
    }
    public function rewind() {
	$this->It = 0;
    }
    public function valid() {
	return $this->It<$this->numCols;
    }
}

class Matrix extends MatrixAccess
{
    const BC_SCALE = 10;
    const BC_EPS = 1E-10;
    private $numRows,$numCols;
    private $IsSquare,$Rows,$DefaultValue;
    public function __construct($numRows,$numColumns=NULL,$Data=[],$default=0.0) {
	$this->numRows = $numRows;
	$this->numCols = $numColumns ? $numColumns : $numRows;
	$this->IsSquare = $this->numRows == $this->numCols;
	$this->DefaultValue = $default;
	$this->Data = [];
	!empty($Data) && array_walk($Data,function(&$v,$k){
	    $this->Data[$k] = $v;
	});
	$this->Rows = [];
    }
    static public function Matrix($numRows,$Data=[],$numColumns=NULL,$default=0.0) {
	return new Matrix($numRows, $numColumns, $Data, $default);
    }
    
    /**
     * Решение системы линейных уравнений (Ax = b) методом Крамера. Система не может быть решена, если детерминант матрицы A равен 0
     *     | a11 a12 ... a1n |      | x1  |      | b1 |
     * A = | a21 a22 ... a2n |, x = | x2  |, b = | b2 |
     *     |         ...     |      | ..  |      | .. |
     *     | am1 am2 ... amn |      | xm  |      | bm |
     * @param array $b
     * @return array
     */
    public function SlaeCramerSolve(array $b) {
	if(count($b) != $this->numRows) return NULL;

	$D = $this->Determinant(); if($D == 0.0) return NULL;

	$Di = new Matrix($this->numRows);
	$x = array_fill(0,  $this->numRows,0.0);
	
	for($d = 0;$d<$this->numCols;$d++) {
	    $Di->Data = $this->Data;
	    for($r=0;$r<$this->numRows;$r++) {
		$Di->Data[$r * $this->numCols + $d] = $b[$r];
	    }
	    $x[$d] = $Di->Determinant()/$D;
	}
	return $x;
    }
    
    public function SlaeGaussSolve(array $b) {

    // number of rows
    $N  = count($b);
    $A = $this;
    
    // forward elimination
    for ($p=0; $p<$N; $p++) {

      // find pivot row and swap
      $max = $p;
      for ($i = $p+1; $i < $N; $i++) { if (abs($A[$i][$p]) > abs($A[$max][$p])) { $max = $i; } }
      
      $temp = $A[$p]; $A[$p] = $A[$max]; $A[$max] = $temp;
      $t    = $b[$p]; $b[$p] = $b[$max]; $b[$max] = $t;

      // check if matrix is singular
      if (abs($A[$p][$p]) <= self::BC_EPS) return 0.0;

      // pivot within A and b
      for ($i = $p+1; $i < $N; $i++) {
        $alpha = $A[$i][$p] / $A[$p][$p];
        $b[$i] -= $alpha * $b[$p];
        for ($j = $p; $j < $N; $j++)
          $A[$i][$j] -= $alpha * $A[$p][$j];
      }
    }

    // zero the solution vector
    $x = array_fill(0, $N-1, 0);

    // back substitution
    for ($i = $N - 1; $i >= 0; $i--) {
      $sum = 0.0;
      for ($j = $i + 1; $j < $N; $j++) {
        $sum += $A[$i][$j] * $x[$j];
      }
      $x[$i] = ($b[$i] - $sum) / $A[$i][$i];
    }

    return $x;
    }

    /**
     * Вычисление детерминанта матрицы
     * @return float
     */
    public function Determinant() {
	if(!$this->IsSquare) return 0.;
	if($this->numRows == 1) return $this->Data[0];
	
	$pivot_index = -1;
	$pivot_value = 0;
	$determinant = 1;
	$Matrix = $this->Data;
	$size = $this->numCols;
    
	for($i = 0; $i < $size; $i++)
	{
	    for($j = $i; $j < $size; $j++)
	    {
		if(abs($Matrix[$j*$size+$i]) > $pivot_value)
		{
		    $pivot_index = $j;
		    $pivot_value = abs($Matrix[$j*$size+$i]);
		}
	    }

	    //Если опорный элемент равен нулю (эпсилон для сброса погрешности)
	    if($pivot_value < self::BC_EPS) { return 0; }

	    if($pivot_index != $i)
	    {
		//Обменяем строки местами
		$n1 = $pivot_index * $this->numCols;
		$n2 = $i * $this->numCols;
		for($ii=0;$ii<$this->numCols;$ii++) {
		    $t = $Matrix[$n1 + $ii]; $Matrix[$n1 + $ii] = $Matrix[$n2 + $ii]; $Matrix[$n2 + $ii] = $t;
		}
		$determinant *= -1;
	    }

	    for($j = $i + 1; $j < $size; $j++)
	    {
		if($Matrix[$j*$size+$i] != 0)
		{
		    $multiplier = 1 / $Matrix[$i*$size+$i] * $Matrix[$j*$size+$i];

		    for($k = $i; $k < $size; $k++)
		    {
			$Matrix[$j*$size+$k] -= $Matrix[$i*$size+$k] *$multiplier;
		    }
		}
	    }

	    $determinant *= $Matrix[$i*$size+$i];
	}

	return $determinant;
    }
    
    /**
     * Инициализировать данные массивом
     * @param array $Data массив с данным 
     */
    public function SetData(array $Data) {
	$this->Data = $Data;
    }
    
    /**
     * @ignore
     */
    public function offsetExists($offset) {return $offset>=0 && $offset < $this->numRows;}
    /**
     * @ignore
     */
    public function offsetGet($offset) {return $this->Row($offset);}
    /**
     * @ignore
     */
    public function offsetSet($offset, $value) {}
    /**
     * @ignore
     */
    public function offsetUnset($offset) {}
    
    /**
     * Получить/Присвоить значение для элемента матрицы
     * @param int $row строка
     * @param int $column столбец
     * @param mixed|NULL $value если значение отлично от NULL, то присваивает значение элементу матрицы
     * @return mixed
     */
    public function Value($row,$column,$value=NULL) {
	!is_null($value) && $this->Data[$row*$this->numCols + $column] = $value;
	return $this->Data[$row*$this->numCols + $column];
    }
    /**
     * Получить столбец матрицы
     * @param int $column номер столбца значения которого необходимо получить
     * @return array
     */
    public function Column($column) {
	$ColumnArray = [];
	if($column>=0 && $column<$this->numCols) {
	    for($i=0;$i<$this->numRows;$i++) {
		$ColumnArray[] = isset($this->Data[$i*$this->numCols + $column]) ? $this->Data[$i*$this->numCols + $column] : $this->DefaultValue;
	    }
	}
    }
    /**
     * Получить строку матрицы
     * @param int $row
     * @return MatrixAccess
     */
    public function Row($row) {
	!isset($this->Rows[$row]) && $this->Rows[$row] = new MatrixAccess($this->Data, $row, $this->numCols,$this->DefaultValue);
	return $this->Rows[$row];
    }
    /**
     * @ignore
     */
    public function count($mode = 'COUNT_NORMAL') {return $this->numRows;}
    /**
     * @ignore
     */
    public function current() {return $this->Row($this->It);}
    /**
     * @ignore
     */
    public function valid() {return $this->It<$this->numRows;}
}
