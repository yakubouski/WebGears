<?php

namespace Math\Statistic;

class LinearRegression {

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

    public function __construct(array $SerieY, array $SerieX) {
	$this->a = \Math\Statistic::Slope($SerieY, $SerieX);
	$this->b = \Math\Statistic::Intercept($SerieY, $SerieX);
	$this->SerieY = &$SerieY;
	$this->SerieX = &$SerieX;
    }

    /**
     * Вычисляет значение y (аппроксимирует, для значения x)
     * @param float $x извесное значение X
     */
    public function y($x) {
	/* y = a * x + b */
	return $this->a * $x + $this->b;
    }

    /**
     * Вычисление коэффициента линейной парной корреляции.
     * @return float Коэффициент линейной парной корреляции
     */
    public function rxy() {
	$n = count($this->SerieY);
	$XiYi = 0.;
	$X = 0.;
	$Y = 0.;
	$X2 = 0.0;
	$Y2 = 0.0;
	foreach ($this->SerieY as $i => $y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);
	    $XiYi += $x * $y;
	    $X += $x;
	    $Y += $y;
	    $X2 += pow($x, 2);
	    $Y2 += pow($y, 2);
	}
	return (($n * $XiYi) - ($X * $Y)) / sqrt(($n * $X2 - pow($X, 2)) * ($n * $Y2 - pow($Y, 2)));
    }

    /**
     * Вычисление коэффициента детерминированности. Сравниваются фактические значения y и значения, получаемые из уравнения прямой; по результатам сравнения вычисляется коэффициент детерминированности, нормированный от 0 до 1. Если он равен 1, то имеет место полная корреляция с моделью, т. е. различий между фактическим и оценочным значениями y нет. В противоположном случае, если коэффициент детерминированности равен 0, использовать уравнение регрессии для предсказания значений y не имеет смысла. 
     * @return float Коэффициент детерминированности
     */
    public function r2() {
	return pow($this->rxy(), 2);
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
     * @return float F-критерии Фишера
     */
    public function F() {
	$r2 = $this->r2();
	return ($r2 / (1. - $r2)) * (count($this->SerieY) - 2);
    }

    /**
     * Вычисление случайной ошибки параметра $x
     * @return float Случайная ошибка параметра $x
     */
    public function mi($i) {
	return floatval($this->SerieY[$i]) - $this->y(floatval($this->SerieX[$i]));
    }

    /**
     * Вычисление случайной ошибки параметра a
     * @return float Случайная ошибка параметра a
     */
    public function ma() {
	$AvgX = \Math\Statistic::Avg($this->SerieX);
	$X = 0.0;
	$Y = 0.0;
	$n = count($this->SerieY);
	foreach ($this->SerieY as $i => $y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);

	    $X += pow($x - $AvgX, 2);
	    $Y += pow($y - $this->y($x), 2);
	}

	return sqrt((1 / $X) * ($Y / ($n - 2)));
    }

    /**
     * Вычисление случайной ошибки параметра b
     * @return float Случайная ошибка параметра b
     */
    public function mb() {
	$AvgX = \Math\Statistic::Avg($this->SerieX);
	$X = 0.0;
	$Y = 0.0;
	$X2 = 0.;
	$n = count($this->SerieY);
	foreach ($this->SerieY as $i => $y) {
	    $x = floatval($this->SerieX[$i]);
	    $y = floatval($y);

	    $X += pow($x - $AvgX, 2);
	    $X2 += pow($x, 2);
	    $Y += pow($y - $this->y($x), 2);
	}

	return sqrt(($Y / ($n - 2)) * ($X2 / ($n * $X)));
    }

    /**
     * Вычисление случайной ошибки параметра rxy
     * @return float Случайная ошибка параметра rxy
     */
    public function mrxy() {
	$r2 = $this->r2();
	return sqrt((1 - $r2) / (count($this->SerieY) - 2));
    }

    /**
     * Вычисление t-статистики Стьюдента для параметра a
     * @return float t-статистики Стьюдента параметра a
     */
    public function ta() {
	return $this->a / $this->ma();
    }

    /**
     * Вычисление t-статистики Стьюдента для параметра b
     * @return float t-статистики Стьюдента параметра b
     */
    public function tb() {
	return $this->b / $this->mb();
    }

    /**
     * Вычисление t-статистики Стьюдента для параметра rxy
     * @return float t-статистики Стьюдента параметра rxy
     */
    public function trxy() {
	return $this->rxy() / $this->mrxy();
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

return '\Math\Statistic\LinearRegression';
