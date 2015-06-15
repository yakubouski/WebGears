<?php
namespace Test;
include_once 'testcase.class.php';
class TestCases extends \Test\TestCase 
{
    static public function Run($PhpTestsSourcesMask=NULL) {
	self::Object()->OnTest($PhpTestsSourcesMask);
    }
    protected function OnTest($PhpTestsSourcesMask=NULL) {
	if(!empty($PhpTestsSourcesMask)) {
	    include 'styles/default.result.cases.tpl';
	}
    }
}
