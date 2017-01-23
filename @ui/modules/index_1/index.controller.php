<?php
class indexController extends Controller {
    public function OnDefault() {
	$this->tpl('index/tpls/default.index.tpl')->Display();
    }
}
