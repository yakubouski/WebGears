<?php
class WidgetPagination extends \Html\Widget {
    
    public function Begin() {}
    public function Complete() {
	$Total = $this->arg('total',0);
	$PageNo = $this->arg('page',1);
	$Limit = $this->arg('limit',25);
	$Href =  $this->arg('href',$_SERVER['PATH_INFO']);
	$Title = $this->arg('title','');
	
	$pages_count = ceil($Total / $Limit);
	//$PageNo += 1;
	$pages = array(1 => 1);
	    if($pages_count) {
	    (($PageNo - 2) > 1) && $pages[$PageNo - 2] = 1;
	    (($PageNo - 1) > 1) && $pages[$PageNo - 1] = 1;
	    (($PageNo) > 1) && $pages[$PageNo] = 1;
	    (($PageNo + 1) > 1 && ($PageNo + 1) < $pages_count) && $pages[$PageNo + 1] = 1;
	    (($PageNo + 2) > 1 && ($PageNo + 2) < $pages_count) && $pages[$PageNo + 2] = 1;
	    $pages[$pages_count] = 1;
	}
	$pages = array_keys($pages);
?>
<span class="ui-pagination" >
    <?=$Title?>
    <? if(count($pages) > 1) { for ($i = 0; $i < count($pages); $i++) { $url = $pages[$i] == 1 ? $Href : ($Href."?page={$pages[$i]}");?>
	<a href="<?=$url?>" class="page <?=($pages[$i] ==($PageNo) ? 's' : '')?>"><?= $pages[$i] ?></a>
	   <? if (isset($pages[$i + 1]) && ($pages[$i + 1] - $pages[$i]) >1) echo '...';
    } }?>
</span>
<?
    }
    public function End($InnerHTML) {$this->Complete();}
}
