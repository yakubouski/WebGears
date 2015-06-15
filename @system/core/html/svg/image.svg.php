<?php
class SvgImage extends \Html\Widget 
{
    public function Begin() {}
    public function Complete() {
	$this->End('');
    }
    public function End($InnerHTML) {
	$Width = $this->arg('width');
	$Height = $this->arg('height');
	$WidthScale = $this->arg('width-scale',$Width);
#	while (ob_get_level()) ob_end_flush();
#	header('Content-type: image/svg+xml');
echo <<< "XML"
<?xml version="1.0" standalone="no"?>
    <svg version="1.1" width="$WidthScale" height="$Height" viewBox="0 0 $Width $Height" baseProfile="full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ev="http://www.w3.org/2001/xml-events" preserveAspectRatio="none">
	$InnerHTML
    </svg>
XML;
    }
}
