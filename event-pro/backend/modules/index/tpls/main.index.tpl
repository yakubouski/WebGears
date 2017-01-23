<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" >  
	<meta http-equiv="x-ua-compatible" content="IE=10">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="cleartype" content="on">
	<meta name="mobile-web-app-capable" content="yes">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <title>ByNET#ID &raquo; <?=_h($Title)?></title>
        <link href="/css/event-pro.css" rel="stylesheet">
        <link href="/css/event-pro/controls.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="/js/event-pro.js" type="text/javascript" charset="utf-8"></script>
	<script src="/@js/ui.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <header>
            <?if(!empty($Back)){?>
            <a class="button" href="<?=$Back?>"><i class="ion-android-arrow-back" style=""></i></a>
            <?}else{?>
            <button class="button"><i class="ion-android-menu" style=""></i></button>
            <?}?>
            <div style="float: right; margin: 0 4px; line-height: normal;" class="f-x-small"><i class="ion-ios-person f-x-large t-wet-asphalt" style="float: left; margin: 0 8px;"></i><?=_h(Org::$FullName)?><div class="t-asbestos"><?=_h(Org::$Group)?></div></div>
            <h1 title="<?=_h(Org::$Object->meta('org.name')['org.name'])?>"><?=_h(empty($Title)?'Личный кабинет':$Title)?></h1>
        </header>
        <main>
            <?$this->display('index/tpls/menu.main.index.tpl')?>
            <section>
                <?=!empty($INNER) ? $INNER : $this->fetch('event/tpls/default.event.tpl');?>
            </section>
        </main>
    </body>
</html>
