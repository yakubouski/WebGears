<?php
class MetaMobileFavIcon extends \Html\Widget {
    public function End($innerHtml) {
	$this->Complete();
    }
    public function Begin() {	}
    public function Complete() { 
	echo <<<"META"
<meta name="viewport" content="width=device-width; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />  
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="format-detection" content="telephone=no">
META;
    }
}