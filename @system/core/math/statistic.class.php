<?php

/*
 * http://mathhelpplanet.com/static.php?p=onlayn-mnk-i-regressionniy-analiz
 */

namespace Math {

    class Statistic {

	/**
	 * Линейный регрессионный анализ. (Excel ЛИНЕЙН/LINEST). Рассчитывает статистику для ряда по методу наименьших квадратов, чтобы вычислить прямую линию, наилучшим образом аппроксимирующую имеющиеся данные
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 * @return \Math\Statistic\LinearRegression 
	 */
	static public function LinearRegression(array $SerieY, array $SerieX) {
	    $className = include_once('statistic/linear-regression.statistic.php');
	    return new $className($SerieY,$SerieX);
	}
	
	/**
	 * Экспоненциальный регрессионный анализ. (Excel ЛИНЕЙН/EXP). 
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 * @return \Math\Statistic\ExponentialRegression 
	 */
	static public function ExponentialRegression(array $SerieY, array $SerieX) {
	    $className = include_once('statistic/exponential-regression.statistic.php');
	    return new $className($SerieY,$SerieX);
	}
	
	/**
	 * Логарифмический регрессионный анализ.
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 * @return \Math\Statistic\LogarithmicRegression 
	 */
	static public function LogarithmicRegression(array $SerieY, array $SerieX) {
	    $className = include_once('statistic/logarithmic-regression.statistic.php');
	    return new $className($SerieY,$SerieX);
	}
	
	/**
	 * Полиномиальный регрессионный анализ.
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 * @param int $Polynome степень полнинома
	 * @return \Math\Statistic\PolynomialRegression 
	 */
	static public function PolynomialRegression(array $SerieY, array $SerieX,$Polynome=2) {
	    $className = include_once('statistic/polynomial-regression.statistic.php');
	    return new $className($SerieY,$SerieX,$Polynome);
	}
	
	
	

	/**
	 * Вычисление наклона. (@see Excel НАКЛОН/SLOPE). Возвращает наклон линии линейной регрессии для точек данных в аргументах "известные значения Y ($SerieY)" и "известные значения X ($SerieX)". Наклон определяется как частное от деления расстояния по вертикали на расстояние по горизонтали между двумя любыми точками прямой; иными словами, наклон — это скорость изменения значений вдоль прямой.
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 * @return float 
	 */
	static public function Slope(array $SerieY, array $SerieX) {
	    $AvgX = self::Avg($SerieX);
	    $AvgY = self::Avg($SerieY);
	    $Sum1 = 0.0;
	    $Sum2 = 0.0;
	    foreach ($SerieY as $i => $y) {
		$x = floatval($SerieX[$i]);
		$y = floatval($y);
		$Sum1 += ($x - $AvgX) * ($y - $AvgY);
		$Sum2 += ($x - $AvgX) * ($x - $AvgX);
	    }

	    return $Sum2 ? $Sum1 / $Sum2 : 0.0;
	}

	/**
	 * Вычисляет точку пересечения линии с осью y, используя значения аргументов "известные значения Y ($SerieY)" и "известные значения X ($SerieX)". (@see Excel ОТРЕЗОК/INTERCEPT).
	 * @param array $SerieY известные значения Y
	 * @param array $SerieX известные значения X
	 */
	static public function Intercept(array $SerieY, array $SerieX) {
	    return self::Avg($SerieY) - self::Slope($SerieY, $SerieX) * self::Avg($SerieX);
	}

	/**
	 * Вычисление среднего значения. 
	 * @param array $Serie
	 * @return float среднее значение
	 */
	static public function Avg(array $Serie) {
	    $Sum = 0;
	    $Count = count($Serie);
	    foreach ($Serie as $v) {
		$Sum += floatval($v);
	    }

	    return $Count ? $Sum / $Count : 0;
	}

	/**
	 * Вычисление суммы ряда. 
	 * @param array $Serie
	 * @return float сумма значений ряда
	 */
	static public function Sum(array $Serie) {
	    $Sum = 0.0;
	    foreach ($Serie as $v) {
		$Sum += floatval($v);
	    }
	    return $Sum;
	}
    }
}
