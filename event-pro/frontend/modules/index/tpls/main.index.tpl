<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />  
	<meta http-equiv="x-ua-compatible" content="IE=10">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="cleartype" content="on">
	<meta name="mobile-web-app-capable" content="yes">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <title><?=APP_NAME?> &raquo; <?=_h($Title)?></title>
        <link href="/css/event-id.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/@js/ui.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <header>
            <button class="button"><i class="ion-android-menu" style=""></i></button>
            <h1 title="<?=_h($SubTitle)?>"><?=_h($Title)?></h1>
        </header>
        <main>
            <menu class="ui-activemenu">
                <li data-route='^\/$' data-counter="10"><a href='/' >Мероприятия</a></li>
                <li data-route='\/member\/'><a href='/member/'>Участник</a></li>
                <li data-route='\/profile\/'><a href='/profile/'>Профиль</a></li>
            </menu>
            <section>
                <?=$INNER?>
            </section>
        </main>
        <script>
            Ui.ActiveMenu('body>main>menu');
        </script>
    </body>
</html>
