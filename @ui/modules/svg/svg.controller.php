<?php
class svgController extends Controller
{
    public function OnDefault() {
	$Zip = new ZipArchive();
	$Zip->open(__DIR__.DIRECTORY_SEPARATOR.'images.zip');
	$SvgFolders = [];
	for($i=0;$i<$Zip->numFiles;$i++) {
	    $stat = $Zip->statIndex($i);
	    @list($Folder,,$Filename) = @explode('/',$stat['name']);
	    !empty($Filename) && $SvgFolders[$Folder][] = $Filename;
	}
	$this->tpl('svg/tpls/default.svg.tpl',['Folders'=>$SvgFolders])->Display();
    }
    public function OnImage() {
	$Zip = new ZipArchive();
	$Zip->open(__DIR__.DIRECTORY_SEPARATOR.'images.zip');
	$this->ReturnHTML($Zip->getFromName($_GET['image']));
    }
    
    private function newSymbol($Symbol) {
	$Paths = [];
	foreach ($Symbol['paths'] as $p) {
	    $attrs = [];
	    foreach ($p[1] as $a=>$av) {
		$attrs[] = $a.'="'.preg_replace('/\s{2,}/m', '', $av).'"';
	    }
	    $attrs = implode(' ', $attrs);
	    
	    $Paths[] =<<<"XML"
	    <{$p[0]} $attrs/>
XML;
	}
	$Paths = implode(PHP_EOL,$Paths);
	return <<<"XML"
	<symbol viewBox="{$Symbol['viewbox']}" catalog="{$Symbol['catalog']}" id="{$Symbol['id']}">
	$Paths
	</symbol>
XML;
    }
    
    private function rebuildSVG($Content,$Name) {
	$xml = simplexml_load_string(preg_replace('%</?g.*?>%sm', '', $Content));
	$ViewBox = (string)$xml->attributes()['viewBox'];
	@list($Folder,,$Filename) = @explode('/',$Name);
	$nxml = ['viewbox'=>empty($ViewBox)?'0 0 1024 1024':$ViewBox,'catalog'=>strtolower($Folder),'id'=>strtolower(pathinfo($Name,PATHINFO_FILENAME)),'paths'=>[]];
	foreach($xml->children() as $c) {
	    $p = [$c->getName(),[]];
	    foreach ($c->attributes() as $n=>$v) {
		$p[1][$n] = $v;
	    }
	    $nxml['paths'][] = $p;
	}
	return $this->newSymbol($nxml);
    }


    public function OnIconList() {
	$Zip = new ZipArchive();
	$Zip->open(__DIR__.DIRECTORY_SEPARATOR.'images.zip');
	$Symbols = [];
	$Ul = [];
	if(!empty($_POST['svg'])){
	foreach ($_POST['svg'] as $f) {
	    $Symbols[] = $this->rebuildSVG($Zip->getFromName($f),$f);
	    $Filename = strtolower(pathinfo($f,PATHINFO_FILENAME));
	    $Ul[] = "<li><div><svg ><use xlink:href=\"#$Filename\"></use></svg></div><label>$Filename</label></li>";
	}
	}
$Symbols = implode(PHP_EOL,$Symbols);
$Ul = implode($Ul);
	$IL =<<<"XML"
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="display:none;">
    $Symbols
</svg>
XML;
	$this->ReturnHTML(
		'<ul class="ilist2">'.$Ul.'</ul>'.'<div style="clear:both;padding: 4px; margin-bottom: 4px; border: 1px solid #D0D0D0; background-color: white;">'.nl2br(htmlspecialchars($IL)).'</div>'.$IL
	);
    }
}
