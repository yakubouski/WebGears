<?php
$this->testSectionStart('\Math\Algo::Levenshtein','Нахождение расстояния между строками/массивами методом Levenshtein\'a',__FILE__);
    $Sample1 = [
	'пама мыла рампу',
	'мама мыла раму'
    ];
    $Sample2 = [
	    array ( 0 => 'горк', 1 => 'детск', 2 => 'минск', 3=>'раб'),
	    array ( 0 => 'горк', 1 => 'детск', 2 => 'куп', 3=>'раб'),
	];
    $this->TestSample('Тест выборки: ',$Sample1,  levenshtein($Sample1[0], $Sample1[1]),function($Sample){
	return \Math\Algo::Levenshtein($Sample[0], $Sample[1]);
    });
    $this->TestSample('Тест выборки: ',$Sample2,  1,function($Sample){
	return \Math\Algo::Levenshtein($Sample[0], $Sample[1]);
    });
$this->testSectionEnd();

$this->testSectionStart('\Math\Algo::DamerauLevenshtein','Нахождение расстояния между строками/массивами методом Damerau-Levenshtein\'a',__FILE__);
    $Sample1 = [
	'мама млыа арму',
	'мама мыла раму'
    ];
    $Sample2 = [
	    array ( 0 => 'горк', 1 => 'детск', 2 => 'минск', 3=>'раб'),
	    array ( 1 => 'горк', 0 => 'детск', 2 => 'куп', 3=>'раб'),
	];
    $this->TestSample('Тест выборки: ',$Sample1,  7,function($Sample){
	return \Math\Algo::DamerauLevenshtein($Sample[1], $Sample[0]);
    });
    $this->TestSample('Тест выборки: ',$Sample2,  2,function($Sample){
	return \Math\Algo::DamerauLevenshtein($Sample[1], $Sample[0]);
    });
$this->testSectionEnd();
