<?php
class SvgUse extends \Html\Widget 
{
    public function Begin() {}
    public function Complete() {
	$this->End('');
    }
    public function End($InnerHTML) {
	$class = $this->arg('class');
	$id = $this->arg('id');
	$width = $this->arg('width',24);
	$height = $this->arg('height',$width);
	echo <<<SVG
<svg class="$class" width="$width" height="$height" ><use xlink:href="#$id"></use></svg>
SVG;
    }
}