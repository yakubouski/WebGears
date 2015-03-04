<!DOCTYPE html>
<html<?if(!empty($CACHE_MANIFEST)){?> manifest="<?=$CACHE_MANIFEST?>"<?}?>>
    <head>
	<meta charset="UTF-8">
	<?if(!empty($HTML_TITLE)){?><title><?=_h($HTML_TITLE)?></title><?}?>
	<?if(!empty($HTML_ICON)){?><link rel="shortcut icon" sizes="64x64" href="<?=$HTML_ICON?>"/><?}?>
	<?if(!empty($META_DESCRIPTION)){?><meta name="description" content=<?=_h($META_DESCRIPTION)?>/><?}?>
	<?if(!empty($META_ROBOTS)){?><meta name="robots" content=<?=_h($META_ROBOTS)?>/><?}?>
	<?if(!empty($META_KEYWORDS)){?><meta name="keywords" content=<?=_h($META_KEYWORDS)?>/><?}?>
	<?if(!empty($META_COPYRIGHT)){?><meta name="copyright" content=<?=_h($META_COPYRIGHT)?>/><?}?>
	<?if(!empty($HTML_CANONICAL)){?><link rel="canonical" href="<?=$HTML_CANONICAL?>" /><?}?>
	<?if(!empty($HTML_AUTHOR)){?><link rel="author" href="<?=$HTML_AUTHOR?>" /><?}?>
	<?if(!empty($HTML_PUBLISHER)){?><link rel="publisher" href="<?=$HTML_AUTHOR?>" /><?}?>
	<?if(!empty($FB_TITLE)){?><meta property="og:title" content="<?=$FB_TITLE?>"/><?}?>
	<?if(!empty($FB_IMAGE)){?><meta property="og:image" content="<?=$FB_IMAGE?>"/><?}?>
	<?if(!empty($FB_URL)){?><meta property="og:url" content="<?=$FB_URL?>"/><?}?>
	<?if(!empty($FB_DESCRIPTION)){?><meta property="og:description" content="<?=$FB_DESCRIPTION?>"/><?}?>
	<?if(!empty($FB_ADMINS)){?><meta property="og:admins" content="<?=$FB_ADMINS?>"/><?}?>
	<?if(!empty($TW_TITLE)){?><meta property="twitter:title" content="<?=$TW_TITLE?>"/><?}?>
	<?if(!empty($TW_IMAGE)){?><meta property="twitter:image" content="<?=$TW_IMAGE?>"/><?}?>
	<?if(!empty($TW_URL)){?><meta property="twitter:url" content="<?=$TW_URL?>"/><?}?>
	<?if(!empty($TW_DESCRIPTION)){?><meta property="twitter:description" content="<?=$TW_DESCRIPTION?>"/><?}?>
	<?if(!empty($TW_CARD)){?><meta property="twitter:card" content="<?=$TW_CARD?>"/><?}?>
	<?if(!empty($JS_SCRIPTS)){?><?=implode(PHP_EOL,$JS_SCRIPTS)?><?}?>
	<?if(!empty($CSS_SCRIPTS)){?><?=implode(PHP_EOL,$CSS_SCRIPTS)?><?}?>
	<meta name="viewport" content="width=device-width; height=device-height; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />  
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
    </head>
    <?=$HTML_BODY?>
</html>
