<?php

namespace Math\Statistic;

class PolynomialRegression {

    /**
     * Вычисленоне значение НАКЛОН @see(Slope)
     * @var array 
     */
    public $A;
    /**
     * Степень полинома
     * @var int 
     */
    public $polynome;

    /**
     * Известные значения Y
     * @var array 
     */
    public $SerieY;

    /**
     * Известные значения X
     * @var array 
     */
    public $SerieX;

    private function Xi($Pow,&$Xi) {
	if($Pow == 0) return count($this->SerieX);
	if(!isset($Xi[$Pow])) {
	    $Xi[$Pow] = 0.;
	    foreach ($this->SerieX as $x) {
		$Xi[$Pow] += pow(floatval($x),$Pow);
	    }
	}
	return $Xi[$Pow];
    }
    private function XiYi($Pow) {
	$Xi = 0.;
	foreach ($this->SerieY as $i=>$y) { $x = floatval($this->SerieX[$i]);
	    $Xi += pow(floatval($x),$Pow)*floatval($y);
	}
	return $Xi;
    }
    private function slae() {
	$MxN = $this->polynome + 1;
	$Xi = [];
	$Matrix = [];
	for($i=0;$i<$MxN*$MxN;$i++) {
	    $Pow = floor($i/$MxN) + $MxN - ($i % $MxN) - 1;
	    $Matrix[$i] = $this->Xi($Pow, $Xi);
	}
	$A = \Math\Matrix::Matrix($MxN,$Matrix);
	$b = [];
	for($i=0;$i<$MxN;$i++) {
	    $b[$i] = $this->XiYi($i);
	}
	return $A->SlaeCramerSolve($b);
    }

    /**
     * 
     * @param array $SerieY
     * @param array $SerieX
     * @param int $Polynome степень полинома 2|3
     */
    public function __construct(array $SerieY, array $SerieX,$Polynome=2) {
	$this->SerieY = &$SerieY;
	$this->SerieX = &$SerieX;
	$this->polynome = $Polynome;
	$this->A = $this->slae();
    }

    /**
     * Вычисляет значение y (аппроксимирует, для значения x)
     * @param float $x извесное значение X
     */
    public function y($x) {
	$pow = $this->polynome;
	$y = 0.;
	switch($pow) {
	    case 2:
		$y = $this->A[0] * pow($x,2) + $this->A[1] * $x + $this->A[2];
		break;
	    case 3:
		$y = $this->A[0] * pow($x,3) + $this->A[1] * pow($x,2) + $this->A[2] * $x + $this->A[3];
		break;
	    case 4:
		$y = $this->A[0] * pow($x,4) + $this->A[1] * pow($x,3) + $this->A[2] * pow($x,2) + $this->A[3] * $x + $this->A[4];
		break;
	    case 5:
		$y = $this->A[0] * pow($x,5) + $this->A[1] * pow($x,4) + $this->A[2] * pow($x,3) + $this->A[3] * pow($x,2) + $this->A[4] * $x + $this->A[5];
		break;
	    case 6:
		$y = $this->A[0] * pow($x,6) + $this->A[1] * pow($x,5) + $this->A[2] * pow($x,4) + $this->A[3] * pow($x,3) + $this->A[4] * pow($x,2) + $this->A[5] * $x + $this->A[6];
		break;
	    default:
		do {
		    $y += $this->A[$this->polynome - $pow] * pow($x,$pow);
		} while ($pow--);
		break;
	}
	
	return $y;
    }

    /**
     * Вычисление индекса корреляции.
     * @return float Индекс корреляции
     */
    public function R() {
	$AvgY = \Math\Statistic::Avg($this->SerieY);
	$Y1 = 0.0;
	$Y2 = 0.0;
	foreach ($this->SerieY as $i=>$y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);
	    
	    $Y1 += pow($y - $this->y($x),2);
	    $Y2 += pow($y - $AvgY,2);
	}
	return sqrt( 1.0 - $Y1/$Y2);
    }

    /**
     * Вычисление индекса детерминации.
     * @return float Индекс детерминации
     */
    public function R2() {
	return pow($this->R(), 2);
    }

    /**
     * Вычисление средней ошибки аппроксимации, %
     * @return float Средная ошибка аппроксимации
     */
    public function A() {
	$n = count($this->SerieY);
	$Y = 0.0;
	foreach ($this->SerieY as $i => $y) {
	    $Y += abs($y ? (($y - $this->y($this->SerieX[$i])) / $y) : 0.);
	}
	return ($n ? (1 / $n * $Y) : 0.) * 100.;
    }

    /**
     * Вычисление F-критерия Фишера. F-статистика или F-наблюдаемое значение. F-статистика используется для определения того, является ли случайной наблюдаемая взаимосвязь между зависимой и независимой переменными.
     * @param float $m число параметров при переменных уравнения регрессии.
     * @return float F-критерии Фишера
     */
    public function F() {
	$r2 = $this->R2();
	$m=  $this->polynome;
	return ($r2 / (1. - $r2)) * ((count($this->SerieY) - $m - 1) / $m);
    }
    
    /**
     * Вычисление случайной ошибки параметра $x
     * @return float Случайная ошибка параметра $x
     */
    public function mi($i) {
	return floatval($this->SerieY[$i]) - $this->y(floatval($this->SerieX[$i]));
    }

    /**
     * Вычисляет Критерий Дарбина-Уотсона
     * @return float критерий Дарбина-Уотсона
     */
    public function d() {
	$dE = 0.0;
	$pE = $this->mi(0);
	$E = pow($pE, 2);
	for ($i = 1; $i < count($this->SerieY); $i++) {
	    $cE = $this->mi($i);
	    $dE += pow($cE - $pE, 2);
	    $E += pow($cE, 2);
	    $pE = $cE;
	}
	return $dE / $E;
    }

}

return '\Math\Statistic\PolynomialRegression';
