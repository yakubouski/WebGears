<?php
class Math 
{
    static public function Percent($Value,$Max,$Scale=100.,$Round=0) {
	return round($Max!=0.0 ? $Scale*$Value/$Max : 0,$Round);
    }
    
    static public function __Test() {
	\Test\TestCases::Run(__DIR__.DIRECTORY_SEPARATOR.'math/tests/*.test.php');
    }
    
}
