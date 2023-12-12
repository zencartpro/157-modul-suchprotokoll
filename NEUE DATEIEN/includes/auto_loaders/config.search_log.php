<?php

if (!defined('IS_ADMIN_FLAG')) {
 die('Illegal Access');
}
$autoLoadConfig[190][] = array('autoType'=>'class', 
'loadFile'=>'observers/class.search_log.php');
$autoLoadConfig[190][] = array('autoType'=>'classInstantiate',
'className'=>'search_log','objectName'=>'search_log');
 