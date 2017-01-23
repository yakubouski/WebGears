<?php
class HtmlFavIcon extends \Html\Widget {
    public function End($innerHtml) {
	$this->Complete();
    }
    private function makFavIcon(\Image\Image $Image,$Sizes,&$FavIcons) {
	foreach ($Sizes as $sz) {
	    list($W,$H) = $sz;
	    $FileName = "favicon-{$W}x{$H}.png";
	    $NewImage = $Image->CloneImage($W, $H);
	    if($NewImage->SaveImage(APP_BASE_DIRECTORY.$FileName)) {
		$FavIcons[] = "<link rel=\"icon\" type=\"image/png\" sizes=\"{$W}x{$H}\" href=\"/{$FileName}\">";
		($W == 16) && $FavIcons[] = "<link rel=\"manifest\" sizes=\"16x16\" href=\"/{$FileName}\">";
	    }
	}
    }
    private function makeAppleFavIcon(\Image\Image $Image,$Sizes,&$FavIcons) {
	foreach ($Sizes as $sz) {
	    list($W,$H) = $sz;
	    $FileName = "apple-touch-icon-{$W}x{$H}.png";
	    $NewImage = $Image->CloneImage($W, $H);
	    if($NewImage->SaveImage(APP_BASE_DIRECTORY.$FileName)) {
		$FavIcons[] = "<link rel=\"apple-touch-icon\" sizes=\"{$W}x{$H}\" href=\"/{$FileName}\">";
	    }
	}
    }
    private function makeAndroidFavIcon(\Image\Image $Image,$Sizes,&$FavIcons,$AppName='') {
	static $density = [36=>'0.75',48=>'1.0',72=>'1.5',96=>'2.0',144=>'3.0',192=>'4.0'];
	$Mainfest = [
	    'name'=>$AppName,
	    'icons'=>[]
	];
	
	foreach ($Sizes as $sz) {
	    list($W,$H) = $sz;
	    $FileName = "android-chrome-{$W}x{$H}.png";
	    $NewImage = $Image->CloneImage($W, $H);
	    if($NewImage->SaveImage(APP_BASE_DIRECTORY.$FileName)) {
		$FavIcons[] = "<link rel=\"icon\" type=\"image/png\" sizes=\"{$W}x{$H}\" href=\"/{$FileName}\">";
		isset($density[$W]) && 
		    $Mainfest['icons'][] = ['src'=>"/{$FileName}",'sizes'=>"{$W}x{$H}",'type'=>'image/png','density'=>$density[$W]];
	    }
	}
	file_put_contents(APP_BASE_DIRECTORY.'manifest.json', json_encode($Mainfest,JSON_PRETTY_PRINT)) && $FavIcons[] = "<link rel=\"manifest\" href=\"/manifest.json\">";
	
    }
    private function makeMsFavIcon(\Image\Image $Image,$Sizes,&$FavIcons,$TileColor='') {
	$Squares = [];
	foreach ($Sizes as $sz) {
	    list($W,$H) = $sz;
	    $FileName = "mstile-icon-{$W}x{$H}.png";
	    $NewImage = $Image->CloneImage($W, $H);
	    if($NewImage->SaveImage(APP_BASE_DIRECTORY.$FileName)) {
		$Squares[] = ($W != $H ) ? "<wide{$W}x{$H}logo src=\"$FileName\"/>" : "<square{$W}x{$H}logo src=\"$FileName\"/>";
		if($W == 144) {
		    !empty($TileColor) && $FavIcons[] = "<meta name=\"msapplication-TileColor\" content=\"{$TileColor}\">";
		    $FavIcons[] = "<meta name=\"msapplication-TileImage\" content=\"{$FileName}\">";
		}
	    }
	}
	!empty($TileColor) && $Squares[] = "<TileColor>{$TileColor}</TileColor>";
	$Squares = implode(PHP_EOL, $Squares);
	file_put_contents(APP_BASE_DIRECTORY.'browserconfig.xml', <<<"XML"
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
  <msapplication>
    <tile>
	{$Squares}
    </tile>
  </msapplication>
</browserconfig>
XML
);
    }
    public function Begin() {	}
    public function Complete() { 
	$FavIcons = [];
	if(!empty($this->arg('src'))) {
	    $Img = new \Image\Image();
	    $Img->LoadImage(APP_BASE_DIRECTORY.ltrim($this->arg('src'),'\\/'));
	    
	    $this->makeAppleFavIcon($Img, [[57,57],[60,60],[72,72],[76,76],[114,114],[120,120],[152,152],[180,180]], $FavIcons);
	    $this->makeAndroidFavIcon($Img, [[36,36],[48,48],[72,72],[96,96],[114,114],[192,192]], $FavIcons,  $this->arg('title'));
	    $this->makeMsFavIcon($Img, [[144,144],[70,70],[150,150],[310,310],[310,150]], $FavIcons,  $this->arg('tile-color'));
	    $this->makFavIcon($Img, [[16,16],[32,32]], $FavIcons);
	}
	echo implode(PHP_EOL, $FavIcons);
    }
}