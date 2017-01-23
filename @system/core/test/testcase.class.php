<?php
namespace Test;

class TestCase 
{
    private $Timers = [];
    protected $Sections = [];
    protected function TestTimerStart() {
	array_push($this->Timers, microtime(true));
    }
    protected function TestTimerEnd($IterationsCount=NULL,&$AvgIterationTime=NULL) {
	$End = microtime(true);
	$Start = array_pop($this->Timers);
	!is_null($IterationsCount) && $AvgIterationTime = $IterationsCount / ($End - $Start);
	return $End - $Start;
    }
    protected function TestTimerEndPrint($Title='',$IterationsCount=NULL) {
	$Elapsed = $this->timerEnd($IterationsCount, $AvgIterationTime);
    }
    protected function TestSectionStart($Package,$Name,$File) {
	ob_start();
	$this->Sections[] = ['Asserts'=>[],'Html'=>'','Tests'=>[],'Package'=>$Package,'Name'=>$Name,'File'=>basename($File)];
    }
    protected function TestSample($SampleName,$SampleData,$SampleTrueResult,$TestCallback) {
	$SampleResult = ['Data'=>$SampleData,'Name'=>$SampleName,'True'=>$SampleTrueResult,'ResultAssert'=>NULL,'Result'=>NULL,'Html'=>''];
	ob_start();
	$SampleResult['Result'] = $TestCallback($SampleData);
	$SampleResult['Html'] = ob_get_clean();
	$SampleResult['ResultAssert'] = $this->TestAssert($SampleResult['Result'],$SampleResult['True']);
	$this->Sections[count($this->Sections)-1]['Tests'][] = $SampleResult;
    }
    protected function TestSectionEnd() {
	$this->Sections[count($this->Sections)-1]['Html'] = ob_get_clean();
    }
    protected function TestAssert($TestValue,$TrueValue) {
	if(is_scalar($TestValue) && is_scalar($TrueValue)) {
	    return $TestValue == $TrueValue;
	}
	if(is_array($TestValue) && is_array($TrueValue)) {
	    if(count($TestValue) != count($TrueValue)) return FALSE;
	    foreach ($TrueValue as $i=>$v) {
		if(!isset($TestValue[$i])) return FALSE;
		if((is_scalar($v) && ($TestValue[$i] != $v)) || (is_array($v) && !$this->TestAssert($TestValue[$i], $v))) return FALSE;
	    }
	    return TRUE;
	}
	return $TestValue == $TrueValue;
    }
    /**
     * @return \Test\TestCase
     */
    static protected function Object() {
	$className = get_called_class();
	return new $className;
    }
    static public function Run() {
	self::Object()->OnTest();
    }
    protected function OnTest() {printf("You must overload function %s",__METHOD__);}
    
    static private $InlineTimer = 0;

    static public function InlineStart() {
	self::$InlineTimer = microtime(true);
    }
    static public function InlineEnd($NumSamplings,$Print=false) {
	$Start=self::$InlineTimer;
	$End = microtime(true);
	self::$InlineTimer = 0;
	$Print && printf('Итераций: %d. Общее время: %.2f  мс. Avg: %.4f мс',$NumSamplings,($End-$Start) * 1000,($End-$Start)/$NumSamplings*1000);	
	return [$NumSamplings,($End-$Start) * 1000,($End-$Start)/$NumSamplings * 1000];
    }
}
