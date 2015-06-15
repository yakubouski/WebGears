<?php
namespace Chart;

class Line {
    static public function Draw($Width, $Height) {
	\Html::Template(__DIR__.DIRECTORY_SEPARATOR."svg/default.line-chart.tpl",['Width'=>$Width,'Height'=>$Height],true)->Display();
    }
}
