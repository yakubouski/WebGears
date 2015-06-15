<?php

class svg {

    /**
     * Отрисовать текст
     * @param float $x
     * @param float $y
     * @param string $text
     * @param color $fill
     */
    static function text($x, $y, $text, $fill = false, $fontSize = false, $fontName = false) {
	self::element('text', [
	    'x' => $x, 'y' => $y, 'fill' => $fill, 'font-size' => $fontSize, 'font-name' => $fontName,'vector_effect'=>'non-scaling-stroke'
		], $text);
    }

    /**
     * Отрисовать прямоугльник
     * @param float $l
     * @param float $t
     * @param float $r
     * @param float $b
     * @param color $fill
     * @param color $stroke
     * @param float $stroke_width
     */
    static function rect($l, $t, $r, $b, $fill = false, $stroke = false, $stroke_width = false) {
	self::element('rect', [
	    'x'=>min($l, $r),'y'=>min($t, $b),
	    'width'=>max($l, $r) - min($l, $r),'height'=>max($t, $b) - min($t, $b),
	    'fill'=>$fill,
	    'stroke'=>$stroke,
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    /**
     * Отрисовать прямоугольник со сукругленными углами
     * @param float $l
     * @param float $t
     * @param float $r
     * @param float $b
     * @param float $rx
     * @param float $ry
     * @param color $fill
     * @param color $stroke
     * @param float $stroke_width
     */
    static function roundrect($l, $t, $r, $b, $rx, $ry, $fill = false, $stroke = false, $stroke_width = false) {
	self::element('rect', [
	    'x'=>(min($l, $r)),
	    'y'=>(min($t, $b)),
	    'width'=>(max($l, $r) - min($l, $r)),
	    'height'=>(max($t, $b) - min($t, $b)),
	    'rx'=>($rx),
	    'ry'=>($ry),
	    'fill'=>($fill),
	    'stroke'=>($stroke),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    static function line($x1, $y1, $x2, $y2, $stroke, $stroke_width = 1, $dotted = false) {
	self::element('line', [
	    'x1'=>($x1),
	    'y1'=>($y1),
	    'x2'=>($x2),
	    'y2'=>($y2),
	    'stroke'=>($stroke),
	    'stroke-dasharray'=>($dotted ? '4,4' : false),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    static function polyline($points, $stroke, $stroke_width = 1, $dotted = false) {
	self::element('polyline', [
	    'points'=>(implode(' ', array_map(function($p) {
					return implode(',', $p);
				    }, $points))),
	    'stroke'=>($stroke),
	    'stroke-dasharray'=>($dotted ? '4,4' : false),
	    'fill'=>('transparent'),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    static function polygon($points, $fill = false, $stroke = false, $stroke_width = false) {
	self::element('polygon', [
	    'points'=>(implode(' ', array_map(function($p) {
					return implode(',', $p);
				    }, $points))),
	    'stroke'=>($stroke),
	    'fill'=>($fill),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    static function path($points, $fill = false, $stroke = false, $stroke_width = false,$vector_effect='non-scaling-stroke') {
	self::element('path', [
	    'd'=>(implode(' ', array_filter(array_map(function($p) {
		    if (is_string($p)) {
			switch ($p) {
			    case 'M': case'm': //moveto
			    case 'L': case 'l': //lineto
			    case 'H': case 'h': //horizontal lineto
			    case 'V': case 'v': //vertical lineto
			    case 'C': case 'c': //curveto
			    case 'S': case 's': //smooth curveto
			    case 'Q': case 'Q': //quadratic Bézier curve
			    case 'T': case 't': //smooth quadratic Bézier curveto
			    case 'A': case 'a': //elliptical Arc
			    case 'Z': //closepath
				return $p;
			}
		    }
		    return is_array($p) ? implode(',', $p) : '';
		}, $points)))),
	    'stroke'=>($stroke),
	    'fill'=>($fill),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>$vector_effect]);
    }

    static function circle($x, $y, $r, $stroke, $fill = false, $stroke_width = 1) {
	self::element('circle',[
	    'cx'=>$x,'cy'=>$y,'r'=>$r,'stroke'=>$stroke,'fill'=>$fill,
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }

    static function ellipse($x, $y, $rx, $ry, $stroke, $fill = false, $stroke_width = 1) {
	self::element('ellipse', [
	    'cx'=>($x),
	    'cy'=>($y),
	    'rx'=>($rx), attr::ry($ry),
	    'stroke'=>($stroke),
	    'fill'=>($fill),
	    'stroke-width'=>($stroke_width),
	    'vector-effect'=>'non-scaling-stroke']);
    }
    
    static function hgradient($id,$color,$opacity='1.') {
	echo <<< "XML"
<linearGradient id="$id" x1="0" y1="0" x2="0" y2="100%">
    <stop style="stop-color: $color;stop-opacity:$opacity;" offset="0" />
    <stop style="stop-color: $color;stop-opacity:0;" offset="1" />
</linearGradient>
XML;
    }

    static private function element($node, $attrs, $content = false) {
	$alist = [];
	foreach ($attrs as $k => $v) {
	    if (empty($v))
		continue;
	    $alist[] = "$k=\"$v\"";
	}
	echo empty($content) ? "<$node " . implode(' ', $alist) . "/>" : "<$node " . implode(' ', $alist) . ">$content</$node>";
    }
    
    static public function range($Pices,$From=0.,$To=360.) {
	$Range = [];
	for ($i = 0; $i < $Pices; $i++) {
	    $d = ($From / 360 + (($i * (($To - $From) / $Pices)) / 360)) * (2 * M_PI);
	    $Range[$i] = [$d, cos($d), sin($d), ($From + (($i * (($To - $From) / $Pices)) ))];
	}
	return $Range;
    }

}
