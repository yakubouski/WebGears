<?php
class HtmlPage extends \Html\Widget {
    static public $HTML;
    
    private $htmlHead=[
	'HTML_TITLE'=>'',
	'HTML_ICON'=>'',
	'META_DESCRIPTION'=>'',
	'META_ROBOTS'=>'',
	'META_KEYWORDS'=>'',
	'META_COPYRIGHT'=>'',
	'HTML_CANONICAL'=>'',
	'HTML_AUTHOR'=>'',
	'HTML_PUBLISHER'=>'',
	'FB_TITLE'=>'',
	'FB_IMAGE'=>'',
	'FB_URL'=>'',
	'FB_DESCRIPTION'=>'',
	'FB_ADMINS'=>'',
	'TW_TITLE'=>'',
	'TW_IMAGE'=>'',
	'TW_URL'=>'',
	'TW_DESCRIPTION'=>'',
	'TW_CARD'=>'',
	'JS_SCRIPTS'=>[],
	'CSS_SCRIPTS'=>[],
	'HTML_BODY'=>'',
	'CACHE_MANIFEST'=>'',
    ];
    
    public function &Property($PARAM,$VALUE=NULL) {
	!is_null($VALUE) && $this->htmlHead[$PARAM] = $VALUE;
	return $this->htmlHead[$PARAM];
    }
    
    public function End($innerHtml) {
	$Class = $this->arg('class','default');
	$this->htmlHead['HTML_BODY'] = $innerHtml;
	if(file_exists(__DIR__."/tpls/$Class.html.tpl")) {
	    !empty($this->htmlHead) && extract($this->htmlHead,EXTR_REFS);
	    ob_start();
	    include(__DIR__."/tpls/$Class.html.tpl");
	    ob_end_flush();
	}
	else {
	    echo 'ERROR html:page template not found';
	}
    }

    public function Begin() { 
	self::$HTML = $this; 
	$this->htmlHead['HTML_TITLE'] = $this->arg('title');
	$this->htmlHead['META_DESCRIPTION'] = $this->arg('description');
	$this->htmlHead['HTML_ICON'] = $this->arg('icon');
	$this->htmlHead['META_ROBOTS'] = $this->arg('robots');
	$this->htmlHead['META_KEYWORDS'] = $this->arg('keywords');
	$this->htmlHead['META_COPYRIGHT'] = $this->arg('copyright');
	$this->htmlHead['HTML_CANONICAL'] = $this->arg('canonical');
	$this->htmlHead['HTML_AUTHOR'] = $this->arg('author');
	$this->htmlHead['HTML_PUBLISHER'] = $this->arg('publisher');
	$this->htmlHead['CACHE_MANIFEST'] = $this->arg('offline');
    }
    public function Complete() { self::$HTML = $this; $this->Begin(); $this->End(''); }
}