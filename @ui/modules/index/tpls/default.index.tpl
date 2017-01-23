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
        <title></title>
	<link href="/css/metro.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/@js/ui.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <header>
            <button class="button"><i class="ion-android-arrow-back" style=""></i></button>
            <h1 title="adventure works project">Financial Management Implementation</h1>
        </header>
        <main>
            <menu class="ui-activemenu">
                <li data-route='\/summary\/'><a href='/summary/' >Summary</a></li>
                <li data-route='\/schedule\/' data-counter="10"><a href='/schedule/'>Schedule</a></li>
                <li data-route='\/resources\/'><a href='/resources/'>Resources</a></li>
                <li data-route='\/budget\/'><a href='/budget/'>Budget</a></li>
                <li data-route='\/inssues\/'><a href='/inssues/'>Inssues and Risks</a></li>
                <li data-route='\/documents\/'><a href='/documents/'>Documents</a></li>
            </menu>
            <div class="ui-toolbar">
                <button><i class="ion-android-funnel"></i></button>
                <button><i class="fa fa-reorder"></i></button>
                <button class="right"><i class="ion-ios-plus-outline"></i>&nbsp;Добавить</button>
            </div>
            
            <div class="ui-toolbar-flat">
                <h1>Lists, Libraries an other Apps</h1>
                <button class="right"><i class="ion-ios-plus-outline"></i>&nbsp;Добавить</button>
            </div>
            <div>
                <div class="ui-calendar">
                    <div class="m">сентябрь, 2015</div>
                    <div>
                    <div class="w">пн</div><div class="w">вт</div><div class="w">ср</div>
                    <div class="w">чт</div><div class="w">пт</div><div class="w">сб</div>
                    <div class="w">вс</div></div>
                    <div>
                    <?$i=42; do{?>
                    <div class="d"><?=$i?></div>
                    <?}while(--$i)?>
                    </div>
                    <div>
                </div>
            </div>
        </main>
        <script>
            Ui.ActiveMenu('body>main>menu');
        </script>
    </body>
</html>
