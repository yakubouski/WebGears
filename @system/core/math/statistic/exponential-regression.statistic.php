<?php

namespace Math\Statistic;

class ExponentialRegression {

    /**
     * Вычисленоне значение НАКЛОН @see(Slope)
     * @var float 
     */
    public $a;

    /**
     * Вычисленоне значение ОТРЕЗОК @see(Intercept)
     * @var float 
     */
    public $b;

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

    private function _a($b) {
	$lnY = 0.0; $X = 0.0; $n = count($this->SerieY);
	foreach ($this->SerieY as $i=>$y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);
	    
	    $lnY += log($y);
	    $X += $x;
	}
	
	return (1/$n) * $lnY - ($b/$n) * $X;
    }
    private function _b() {
	$Xi_lnYi = 0.0; $n = count($this->SerieY);
	$X = 0.0; $X2 = 0.0;
	$lnY = 0.0;
	foreach ($this->SerieY as $i=>$y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);
	    
	    $Xi_lnYi += $x * log($y);
	    $X += $x;
	    $lnY += log($y);
	    $X2 += pow($x,2);
	}
	return (($n * $Xi_lnYi) - ($X * $lnY) ) / ($n * $X2 - pow($X,2));
    }


    public function __construct(array $SerieY, array $SerieX) {
	$this->SerieY = &$SerieY;
	$this->SerieX = &$SerieX;
	$this->b = $this->_b();
	$this->a = $this->_a($this->b);
    }

    /**
     * Вычисляет значение y (аппроксимирует, для значения x)
     * @param float $x извесное значение X
     */
    public function y($x) {
	/* y = exp(a + b*x) */
	return exp($this->a + $this->b * $x);
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
    public function F($m=1.) {
	$r2 = $this->R2();
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

return '\Math\Statistic\ExponentialRegression';
