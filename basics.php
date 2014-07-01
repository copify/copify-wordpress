<?php
//
//  basics.php
//  copify-wordpress
//
//  Created by Rob Mcvey on 2014-06-17.
//  Copyright 2014 Rob McVey. All rights reserved.
//

// Dev mode
if (!defined('COPIFY_DEVMODE')) {
	define('COPIFY_DEVMODE', false); 
}

// Directory seperator for this system
if (!defined('COPIFY_DS')) {
	define('COPIFY_DS', DIRECTORY_SEPARATOR);
}

// Path to plugin
if (!defined('COPIFY_PATH')) {
	define('COPIFY_PATH',  __DIR__);
}

// Lib path
if (!defined('COPIFY_LIB')) {
	define('COPIFY_LIB',  COPIFY_PATH . COPIFY_DS . 'Lib' . COPIFY_DS);
}

// View path
if (!defined('COPIFY_VIEWS')) {
	define('COPIFY_VIEWS',  COPIFY_PATH . COPIFY_DS . 'Views' . COPIFY_DS);
}
