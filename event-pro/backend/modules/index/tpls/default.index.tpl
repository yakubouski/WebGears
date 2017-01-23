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
        <title><?=APP_NAME?></title>
        <link href="/css/event-pro/default.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/@js/ui.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
        <main>
            <aside>
                <h1 class="t-normal f-normal t-turquoise f16"><img src="/img/event-pro/logo.png" align="absmiddle"><?=APP_NAME?></h1>
                <div class="">Авторизуйтесь</div>
                <form action="/" method="post">
                    <input type="password" name="password" placeholder="Пароль">
                    <label><input type="checkbox" name="remember"> Оставаться в системе</label>
                    <button name="si" class="ui-button-blue action1">Войти</button>
                </form>
                <div style="margin: 32px 0">
                    Не можете авторизоваться? <a href="/?reset">Сбросить пароль</a>
                </div>
            </aside>
            <section>
                <div style="width: 90vw; margin: 16px auto; ">
                <h1 class="t-asbestos f-large"><?=Org::$Object->meta('org.name')['org.name']?></h1>
                Личный кабинет организатора мероприятия
            </section>
        </main>
        <script>
        </script>
    </body>
</html>
