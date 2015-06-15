<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
	<link href="/css/reset.css" rel="stylesheet">
	<link href="/css/patterns.css" rel="stylesheet">
	<script src="/js/jquery/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
	<section style="position: fixed; left: 0; width: 100%; height: 100%; top: 0;">
	    <aside style="left: 0; top: 0; height: 100%; width: 200px; overflow-y: scroll; position: absolute;">
		<?foreach ($Folders as $f=>$i){?>
		<a class="menu" name="<?=$f?>" href="#" style=""><?=$f?>&nbsp;(<?=count($i)?>)</a>
		<?}?>
	    </aside>
	    <section style="left: 200px; top: 0; right: 0; height: 100%; position: absolute;">
		<section style="top: 0; right: 0; left: 0; height: 60%; overflow-y: scroll; position: absolute;" id="icons-view">
		    <?foreach($Folders as $f=>$ims){?>
		    <ul id="<?=$f?>" class="ilist">
			<?foreach($ims as $i){?>
			<li><div class="svg-image" svg="<?=$f?>/svg/<?=$i?>"></div><label><?=$i?></label></li>
			<?}?>
		    </ul>
		    <?}?>
		</section>
		<section style="top: 60%; right: 50%; left: 0; bottom: 0; overflow-y: scroll; position: absolute;">
		    <ul class="ilist" style="display: block !important;" id="selected-ilist">
		    </ul>
		</section>
		<section style="top: 60%; right: 0; left: 50%; bottom: 0; overflow-y: scroll; position: absolute;" id="svg-xml-ilist">
		    derfe
		</section>
	    </section>
	</section>
	<script>
	    function genIconList() {
		var svgs = [];
		$('#selected-ilist>li>div').each(function(){
		    svgs[svgs.length] = $(this).attr('svg');
		});
		$.post('/ui.php/svg/iconlist/',{svg: svgs}).success(function(r){
		    $('#svg-xml-ilist').html(r);
		});
	    }
	    
	    $('#icons-view ul.ilist>li').click(function(){
		$(this).clone().appendTo($('#selected-ilist'));
		genIconList();
	    });
	    $('a.menu').click(function(e){
		e.preventDefault();
		$('#icons-view ul.ilist').hide();
		$('#'+$(this).attr('name')).show();
		$('#'+$(this).attr('name')).find('div.svg-image').each(function(){
		    var el = $(this);
		    if(el.prop('loaded') !== 1) {
			el.prop('loaded',1);
			$.get('/ui.php/svg/image/',{image: $(this).attr('svg')}).success(function(r){
			    el.html(r);
			});
		    }
		});
	    });
	    $(document).on('click','#selected-ilist>li',function(){
		$(this).hide(100,function(){
		    $(this).remove();
		    genIconList();
		});
	    });
	    genIconList();
	</script>
    </body>
</html>
