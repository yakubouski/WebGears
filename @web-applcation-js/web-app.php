<?php
class meta {
    static function charset($Charset='UTF-8') { print '<meta charset="'.$Charset.'">'.PHP_EOL;}
    static function viewport($Width='device-width',$DefaultScale=1,$MaxScale=1,$UserScalable=0,$MinimalUi=1) { print '<meta name="viewport" content="width='.$Width.', initial-scale='.$DefaultScale.', maximum-scale='.$MaxScale.', user-scalable='.($UserScalable?'yes':'no').($MinimalUi?', minimal-ui':'').'">'.PHP_EOL;
    }
    static function title($Title,$Description='') { 
        print '<title>'.($Title).'</title>'.PHP_EOL;
        !empty($Description) && print '<meta name="description" content="'.htmlspecialchars($Description).'">'.PHP_EOL;
    }
    static function keywords($Keywords='') { 
        !empty($Keywords) && print '<meta name="keywords" content="'.htmlspecialchars($Keywords).'">'.PHP_EOL;
    }
    static function canonical($Href='') { 
        !empty($Href) && (preg_match('%^https?://%i',$Href) ? print(' <link rel="canonical" href="'.$Href.'"/>'.PHP_EOL) : trigger_error(__METHOD__ . ' Invalid RelCanonical link.') );
    }
    static function author($Author='',$Publisher='',$CopyrightYear=null,$CopyrightText='Copyright %d. All Rights Reserved.') { 
        !empty($Author) && print('<link rel="author" href="'.htmlspecialchars($Author).'"/>'.PHP_EOL);
        !empty($Publisher) && print('<link rel="publisher" href="'.htmlspecialchars($Publisher).'"/>'.PHP_EOL);
        !empty($CopyrightYear) && printf($CopyrightText.PHP_EOL,$CopyrightYear);
    }
    static function robots($Robots='',$Google='',$Yandex='') { 
        !empty($Robots) && print('<meta name="robots" content="'.htmlspecialchars($Robots).'">'.PHP_EOL);
        !empty($Google) && print('<meta name="google" content="'.htmlspecialchars($Google).'">'.PHP_EOL);
        !empty($Yandex) && print('<meta name="yandex" content="'.htmlspecialchars($Yandex).'">'.PHP_EOL);
    }
    /**
     * @param string $FavIcon
     * @param string $Type
     */
    static function favicon($FavIcon,$Type='image/png') {
        !empty($FavIcon) && print('<link rel="shortcut icon" type="'.$Type.'" href="'.$FavIcon.'">'.PHP_EOL);
    }

    
    /**
     * 
     * @param type $Name
     * @param type $ShortName
     * @param array $Icons ['icon-WxH'=>'iconfile.png','apple-icon-WxH'=>'iconfile.png','tile-image'=>'iconfile-144x144.png','WxH'=>'iconfile.png']
     * @param string $Display default, standalone. fullscreen | standalone | minimal-ui | browser
     * @param string $Orientation any | natural | landscape | landscape-primary | landscape-secondary | portrait | portrait-primary | portrait-secondary
     * @param type $StartUrl
     * @param type $BackgroundColor
     * @param type $ThemeColor
     * @param type $MainfestName
     */
    static function manifest($Name,$ShortName,$Icons=[],$Display="standalone",$Orientation='portrait',$StartUrl='/',$BackgroundColor='',$ThemeColor='', $MainfestName='') {
        $FileCacheTime = @filemtime(@debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1)[0]['file']);
        $MainfestName = empty($MainfestName)?($_SERVER['SERVER_NAME'].'.manifest.json'):$MainfestName;
        $ManifestFile = APP_BASE_DIRECTORY.$MainfestName;
        
        $MetaIcons = [];
        $ManifestIcons = [];
        if(!($TimeManifestFile = @filemtime($ManifestFile)) || $TimeManifestFile<$FileCacheTime) {
            $Manifest = ["name"=>$Name,"short_name"=>empty($ShortName)?$Name:$ShortName,'icons'=>[],'start_url'=>$StartUrl,'display'=>$Display,'background_color'=>$BackgroundColor,'theme_color'=>$ThemeColor,'orientation'=>$Orientation];
            foreach($Icons as $in=>$ic) {
                if(preg_match('/^\d+x\d+$/i',$in)) {
                    $ManifestIcons[$in] = ['src'=>$ic,'sizes'=>$in,'type'=>'image/'.pathinfo($ic,PATHINFO_EXTENSION)];
                }
                elseif(preg_match('/^apple-icon-.*?(\d+x\d+)/im', $in,$m)) {
                    $MetaIcons[$m[1]] = '<link rel="apple-touch-icon" type="image/'.pathinfo($ic,PATHINFO_EXTENSION).'" href="'.$ic.'" sizes="'.$m[1].'">';
                    $ManifestIcons[$m[1]] = ['src'=>$ic,'sizes'=>$m[1],'type'=>'image/'.pathinfo($ic,PATHINFO_EXTENSION)];
                }
                elseif(preg_match('/^icon-.*?(\d+x\d+)/im', $in,$m)) {
                    $MetaIcons[$m[1]] = '<link rel="icon" type="image/'.pathinfo($ic,PATHINFO_EXTENSION).'" href="'.$ic.'" sizes="'.$m[1].'">';
                    $ManifestIcons[$m[1]] = ['src'=>$ic,'sizes'=>$m[1],'type'=>'image/'.pathinfo($ic,PATHINFO_EXTENSION)];
                }
                elseif(preg_match('/^tile-image/im', $in,$m)) {
                    $MetaIcons[$m[1]] = '<meta name="msapplication-TileImage" content="'.$ic.'">';
                }
            }
            $Manifest['icons'] = array_values($ManifestIcons);
            file_put_contents($ManifestFile,json_encode(array_filter($Manifest),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
        }
        else {
            foreach($Icons as $in=>$ic) {
                if(preg_match('/^apple-icon-.*?(\d+x\d+)/im', $in,$m)) {
                    $MetaIcons[$m[1]] = '<link rel="apple-touch-icon" type="image/'.pathinfo($ic,PATHINFO_EXTENSION).'" href="'.$ic.'" sizes="'.$m[1].'">';
                }
                elseif(preg_match('/^icon-.*?(\d+x\d+)/im', $in,$m)) {
                    $MetaIcons[$m[1]] = '<link rel="icon" type="image/'.pathinfo($ic,PATHINFO_EXTENSION).'" href="'.$ic.'" sizes="'.$m[1].'">';
                }
            }
        }
        !empty($Name) && print('<meta name="apple-mobile-web-app-title" content="'.htmlspecialchars($Name).'">'.PHP_EOL.'<meta name="apple-mobile-web-app-title" content="'.htmlspecialchars($Name).'">'.PHP_EOL.'<meta name="application-name" content="'.htmlspecialchars($Name).'">'.PHP_EOL);
        print '<meta name="apple-mobile-web-app-capable" content="yes">'.PHP_EOL.'<meta name="HandheldFriendly" content="True">'.PHP_EOL.'<meta name="MobileOptimized" content="320">'.PHP_EOL.'<meta name="format-detection" content="telephone=no">'.PHP_EOL.'<meta http-equiv="cleartype" content="on">'.PHP_EOL.'<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">'.PHP_EOL;
        print '<meta http-equiv="x-ua-compatible" content="IE=edge">'.PHP_EOL;
        !empty($ThemeColor) && print '<meta  name="theme-color" content="'.$ThemeColor.'">'.PHP_EOL;
        !empty($ThemeColor) && print '<meta  name="msapplication-TileColor" content="'.$ThemeColor.'">'.PHP_EOL;
        !empty($MetaIcons) && print implode(PHP_EOL,$MetaIcons).PHP_EOL;
        print '<link rel="manifest" href="/'.$MainfestName.'">'.PHP_EOL;
    }
}