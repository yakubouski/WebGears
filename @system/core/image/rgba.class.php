<?php

/**
 * Description of rgba
 *
 * @author Andrei Yakubouski
 */
class rgba 
{
    static public $Colors = array(
	array('#444E5A','#6D7179','#9DA1A6','#BBBFC3','#DADDE0','#EBECED','#F5F6F7'),
	array('#1B2D83','#4A6CB3','#5F8FCB','#C5D9F0','','',''),
	array('#004188','#0071BC','#0094D5','#B3DDF5','','',''),
	array('#00662F','#009754','#80C398','#ABD7BC','','',''),
	array('#539316','#63B01F','#7FBB56','#B4D597','','',''),
	array('#55127B','#7967AB','#A49ECD','#BFB8D8','','',''),
	array('#7B0C82','#9B579F','#BA90C0','#D1B0D2','','',''),
	array('#522020','#9F5F00','#D9AE7E','#F3DFC6','','',''),
	array('#F9BA07','#FFDC00','#FFE944','#FFF592','','',''),
	array('#B5002F','#E62300','#F0A4B9','#F6CFDD','','',''),
    );
    /**
     * Форматирование цвета в rgba сущность
     * @param string $color
     * @param float $alpha
     * @return string
     */
    static public function color($color,$alpha=1.) {
	return $alpha >= 1. ? $color : (preg_match('/#?([0-9A-F]{1,2})([0-9A-F]{1,2})([0-9A-F]{1,2})/i', $color, $m) ? sprintf('rgba(%d,%d,%d,%.2F)', hexdec($m[1]), hexdec($m[2]), hexdec($m[2]), $alpha) : 'rgba(0,0,0,1.)');
    }
    /**
     * Предопределенная палитра цветов
     * @param int $r - строка [0-9]
     * @param int $c - столбец [0-3]
     * @param float $alpha, прозрачность
     * @return string
     */
    static public function palette($r,$c,$alpha=1.) {
	return self::color(@self::$Colors[$r][$c],$alpha);
    }
}

?>
