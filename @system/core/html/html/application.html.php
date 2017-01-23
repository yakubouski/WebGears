<?php
require_once 'application/page.application.php';
require_once 'application/menu.application.php';
class HtmlApplication extends \Html\Widget
{
    public function Begin() {
        
    }

    public function Complete() {
        
    }

    private function MetaPublisher() {
        $Value = $this->arg('meta-publisher', false);
        return empty($Value) ? '' : sprintf('<link rel="publisher" href="%s" />',$Value);
    }
    
    private function MetaAuthor() {
        $Value = $this->arg('meta-author', false);
        return empty($Value) ? '' : sprintf('<link rel="author" href="%s" />',$Value);
    }
    
    private function MetaBase() {
        $Value = $this->arg('base', false);
        return !empty($Value) ? sprintf('<base href="%s">','',$Value) : '';
    }


    private function FavIcon() {
        $Value = $this->arg('favicon', false);
        if(!empty($Value)) {
            return sprintf('<link rel="icon"  href="%s">',$Value);
        }
        $Value = $this->arg('favicon-href', false);
        return empty($Value) ? '' : sprintf('<link rel="icon" type="%s" href="%s" />',$this->arg('favicon-type','image/png'),$Value);
    }


    public function End($InnerHTML) {
        $Publisher = $this->MetaPublisher();
        $Author = $this->MetaAuthor();
        $Copyright = $this->arg('meta-copyright', date('Y'));
        $FavIcon = $this->FavIcon();
        $Base = $this->MetaBase();
        echo <<< "HTML"
<!DOCTYPE html>
<html>
    <head>
        {$Base}
        <meta charset="utf-8" />
        <title>{{HTML::TITLE}}</title>
        <meta name="title" content="{{HTML::TITLE}}">
        <meta name="description" content="{{HTML::DESCRIPTION}}">
        {$Author}
        {$Publisher}
        <meta name="author" content="EVENT-PRO.BY">
        <meta name="Copyright" content="Copyright {$Copyright}. All Rights Reserved.">
        <meta name="viewport" content="width=device-width,minimal-ui; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0,minimal-ui;minimal-ui" />  
        <meta http-equiv="x-ua-compatible" content="IE=10">
        {$FavIcon}
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="cleartype" content="on">
        <meta name="mobile-web-app-capable" content="yes">
        {{JS::SCRIPTS}}
        {{CSS::STYLES}}
    </head>
    <body>
        $InnerHTML
    </body>
</html>
HTML;
    }
}
