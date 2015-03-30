<?php
define(DEBUG, 1);
include_once ('./@system/core/system.php');

Application::Virtual('mobile.bynetweek.by');


Application::Run(TRUE,NULL,NULL,'.session/');