<?php
namespace Math;
class Combinatoric extends \Object\Iterator
{
    /**
     * Количество комбинации N из k
     * @param int $k
     * @param int $N
     * @return int 
     */
    static public function CountCombinations($k,$N) {
	return \Math\Algo::Factorial($N) / (\Math\Algo::Factorial($N-$k) * \Math\Algo::Factorial($k));
    }
    /**
     * Количество перестановок
     * @param int $N
     * @return int
     */
    static public function CountPermutations($N) {
	return \Math\Algo::Factorial($N);
    }
    /**
     * Количество размещений из N по k
     * @param int $N
     * @return int
     */
    static public function CountPlacements($k,$N) {
	return \Math\Algo::Factorial($N) / \Math\Algo::Factorial($N-$k);
    }

    static public function Combinations($k,$N) {
	$Num = $N - $k;
	
	$Combinations = [];
	$Combination = [];
	for($i=0;$i<$k;$i++) { $Combination[] = $i; }
	do {
	    $skip = false;
	    for($i=1;$i<$k;$i++) {
		$skip = $Combination[$i]<=$Combination[$i-1];
		if($skip) break;
	    }
	    
	    !$skip && $Combinations[] = $Combination;
	    
	    $i = $k-1;
	    do {
		$Combination[$i] += 1;
		$Carry = $Combination[$i] >= $N;
		$Carry && $Combination[$i] = $Combination[$i-1]+1;
	    } while ($Carry && $i--);
	} while ($Combination[0]<=$Num);
	return $Combinations;
    }
}
