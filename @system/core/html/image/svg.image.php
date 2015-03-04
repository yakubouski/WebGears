<?php
class ImageSvg extends \Html\Widget 
{
    const IMAGE_LIBRARY="ui/default/images/%s/svg/%s";
    private $SvgElements = [];
    private function _node($xpath,$svg) {
	if (preg_match('/([\w:]+)(\[(\d+)\])?/', $xpath,$ns)) {
	    $Node = $svg;
	    foreach (explode(':', $ns[1]) as $n) {
		if(!isset($Node->$n)) return false;
		$Node = $Node->{$n};
	    }
	    return (isset($ns[3]) ? (isset($Node[intval($ns[3])]) ? $Node[intval($ns[3])] : false) : $Node);
	}
    }
    private function svg($folder,$file) {
	$svg = @simplexml_load_file(sprintf(BASE_DIRECTORY.self::IMAGE_LIBRARY,$folder,$file), null, LIBXML_COMPACT | LIBXML_NOBLANKS);
	if($svg) {
	    !empty($this->arg('class',false)) && $svg['class'] = 'image-list '.$this->arg('class');
	    if(!empty($this->arg('id',false))) { $svg['id'] = $this->arg('id'); } else { unset($svg['id']); }

	    foreach($this->args(['class','id','src','svg']) as $key => $value) 
		$svg->addAttribute($key,$value);

	    foreach ($this->SvgElements as $path=>$attrs) {
		$Node = $this->_node($path, $svg);
		if($Node !== false) {
		    foreach ($attrs as $key => $value) {
			$Node->addAttribute($key,$value);
		    }
		}
	    }

	    return $svg;
	}
	return false;
    }
    public function Begin() {}
    public function Complete() {
	$this->End('');
    }
    public function ElementComplete($Args) {
	($this->arg('xpath',false,$Args)) && $this->SvgElements[$this->arg('xpath',false,$Args)] = $this->args(['path'],$Args);
    }
    public function End($InnerHTML) {
	$class = $this->arg('class','');
	if(!empty($this->arg('src',false))){ 
	    list($iFolder,$iFile) = explode('/',$this->arg('src','/'));
	echo<<<IMG
<img src="/ui/default/images/$iFolder/svg/$iFile" class="image-list $class" />
IMG;
	}
	if($this->arg('svg',false)){ 
	    @list($iFolder,$iFile) = @explode('/',$this->arg('svg','/'));
	    $SvgImage = $this->svg($iFolder,$iFile);
	    $SvgImage!==false ? print($this->svg($iFolder,$iFile)->asXML()) : '';
	}
    }
}