<?php
// 
//  basics.php
//  copify-wordpress
//  
//  Created by Rob Mcvey on 2014-06-17.
//  Copyright 2014 Rob McVey. All rights reserved.
// 
if (!defined('COPIFY_DEVMODE')) { define('COPIFY_DEVMODE', true); }
if (!defined('COPIFY_DS')) { define('COPIFY_DS', DIRECTORY_SEPARATOR); }
if (!defined('COPIFY_PATH')) { define('COPIFY_PATH',  __DIR__); }
if (!defined('COPIFY_LIB')) { define('COPIFY_LIB',  COPIFY_PATH . COPIFY_DS . 'Lib' . COPIFY_DS); }
if (!defined('COPIFY_VIEWS')) { define('COPIFY_VIEWS',  COPIFY_PATH . COPIFY_DS . 'Views' . COPIFY_DS); }
