<?php
class HtmlLayout extends \Html\Widget {
    static public $MAIN;
    
    private $layoutSideBar,$layoutContent,$layoutHeader;
    
    public function HeaderEnd($Args,$InnerHtml) {
	empty($this->layoutHeader) ? $this->layoutHeader = ['Args'=>$Args,'Html'=>$InnerHtml] : 
	    $this->layoutHeader = ['Args'=>array_merge($this->layoutHeader['Args'],$Args),'Html'=>$InnerHtml];
    }
    public function HeaderComplete($Args) {
	$this->HeaderEnd($Args, '');
    }
    
    public function SideBarEnd($Args,$InnerHtml) {
	$this->layoutSideBar = ['Args'=>$Args,'Html'=>$InnerHtml];
    }
    
    public function ContentEnd($Args,$InnerHtml) {
	$this->layoutContent = ['Args'=>$Args,'Html'=>$InnerHtml];
    }
    
    public function End($innerHtml) {
?>
<body class="<?=($this->layoutHeader ? 'hh':'')?>">
    <?if(!empty($this->layoutHeader)){
	$Args =& $this->layoutHeader['Args'];
	$MenuButton = $this->arg('button-menu', 'true', $Args) == 'true';
	$BackButton = $this->arg('button-back', '', $Args);
	$Image = $this->arg('image', '', $Args);
	$Icon = $this->arg('icon', '', $Args);
	$Title = $this->arg('title', '', $Args);
	$Class = $this->arg('class', '', $Args);
    ?>
    <header <?=$Class?>>
	<?if(!empty($BackButton)){?><a class="ui-button-link" href="<?=($BackButton === 'true' ? $_SERVER['HTTP_REFERER'] : $BackButton )?>"><i class="icon-arrow-left7"></i></a><?}elseif($MenuButton){?><button class="ui-button-link" id="nav-sidebar-ui"><i class="icon-list2"></i></button><?}?>
	<?if(!empty($Image)){?><img src="<?=$Image?>"/><?}?>
	<?if(!empty($Icon)){?><i class="<?=$Icon?>"></i><?}?>
	<span style="display: inline-block; text-transform: uppercase;"><?=$Title?></span>
	<span style="display: inline-block; float: right;">
	    <?=$this->layoutHeader['Html']?>
	</span>
    </header>
    <?}?>
<?if(!empty($this->layoutSideBar)){
    $Args =& $this->layoutSideBar['Args'];
    $Class = $this->arg('class', '', $Args);
?>
    <aside <?=$Class?>><?=$this->layoutSideBar['Html']?></aside>
<?}?>
<?if(!empty($this->layoutContent)){
    $Args =& $this->layoutContent['Args'];
    $Class = $this->arg('class', '', $Args);
?>    
    <section class="<?=$Class?> hf hh">
	<?=$this->layoutContent['Html']?>
    </section>
<?}?>    
    <?=$innerHtml?>
    <script>    
    <?if(!empty($this->layoutHeader) && !empty($this->layoutSideBar)){?>
	$('#nav-sidebar-ui').click(function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    $('body>section').toggleClass('toggle');
	});    
    <?}?>
	!$('body>section>header').length && $('body>section').removeClass('hh').show();
	!$('body>section>footer').length && $('body>section').removeClass('hf').show();
	
	UI.OnResize(function(){
	    if($('body>section>footer').length) {
		var items = $('body>section>footer>button');
		items.length && $(items).width(Math.ceil(($('body>section>footer').innerWidth()-(5*items.length))/items.length));
	    }
	});
    </script>
</body>
<?php
    }

    public function Begin() {	self::$MAIN = $this; }
    public function Complete() {}
}