<?php
namespace Math;

class BaseConvert {
    const BASE62 = 'zZaAqQ12wWsSxXcCdDeE34rRfFvVbBgGtT56yYhHnNmMjJuU78iIkKlLoO90pP';
    const BASE36 = 'zaq12wsxcde34rfvbgt56yhnmju78iklo90p';

    static public function Encode($Val,$Alphabet) {
	$aBase = strlen($Alphabet);
	$Result = '';
	do {
	    $idx = bcmod($Val, $aBase);
	    $Result = substr($Alphabet, $idx,1).$Result;
	    $Val = bcdiv($Val, $aBase);
	} while (!empty($Val));
	return $Result;
    }
    static public function Decode($Val,$Alphabet) {
	$aBase = strlen($Alphabet);
        $nLength = strlen($Val);
        $Result = bcadd('0',strpos($Alphabet,$Val[0]));
        for($i=1;$i<$nLength;$i++) {
            $Result = bcadd(bcmul($Result,$aBase), strpos($Alphabet,$Val[$i]));
        }
	return $Result;
    }
}
