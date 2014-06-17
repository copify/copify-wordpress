<?php 
require_once(__DIR__ . '/../../basics.php');
require_once(__DIR__ . '/../../Lib/Api.php');
require_once(__DIR__ . '/../../Lib/CopifyWordpress.php');
// 
//  CopifyWordpressTest.php
//  copify-wordpress
//  
//  Created by Rob Mcvey on 2014-06-17.
//  Copyright 2014 Rob McVey. All rights reserved.
//
class CopifyWordpressTest extends PHPUnit_Framework_TestCase {

/**
 * testCopifySetApiClass
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySetApiClass() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
			
		$this->CopifyWordpress->CopifySetApiClass();
		$this->assertEquals('https://uk.copify.com/api', $this->CopifyWordpress->Api->basePath);
	}
	
/**
 * testCopifyCssAndScripts
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyCssAndScripts() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress'));
		// Our js
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('plugins_url', 'copify/js/Copify.js')
			->will($this->returnValue('http://localhost.3dlockers.com/wp-content/plugins/copify/js/Copify.js'));
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('wp_enqueue_script', 'copify', 'http://localhost.3dlockers.com/wp-content/plugins/copify/js/Copify.js', array('jquery'));
		// Modal JS
		$this->CopifyWordpress->expects($this->at(2))
			->method('wordpress')
			->with('plugins_url', 'copify/js/bootstrap-modal.js')
			->will($this->returnValue('http://localhost.3dlockers.com/wp-content/plugins/copify/js/bootstrap-modal.js'));
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_enqueue_script', 'bootstrap-modal', 'http://localhost.3dlockers.com/wp-content/plugins/copify/js/bootstrap-modal.js', array('jquery'));
		// jquery.validate
		$this->CopifyWordpress->expects($this->at(4))
			->method('wordpress')
			->with('plugins_url', 'copify/js/jquery.validate.js')
			->will($this->returnValue('http://localhost.3dlockers.com/wp-content/plugins/copify/js/jquery.validate.js'));
		$this->CopifyWordpress->expects($this->at(5))
			->method('wordpress')
			->with('wp_enqueue_script', 'jquery.validate', 'http://localhost.3dlockers.com/wp-content/plugins/copify/js/jquery.validate.js', array('jquery'));
		// Our css
		$this->CopifyWordpress->expects($this->at(6))
			->method('wordpress')
			->with('plugins_url', 'copify/css/Copify.css')
			->will($this->returnValue('http://localhost.3dlockers.com/wp-content/plugins/copify/css/Copify.css'));
		$this->CopifyWordpress->expects($this->at(7))
			->method('wordpress')
			->with('wp_enqueue_style', 'copify', 'http://localhost.3dlockers.com/wp-content/plugins/copify/css/Copify.css');
		$this->CopifyWordpress->CopifyCssAndScripts();
	}
	
/**
 * testCopifySettings
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySettings() {
		// $CopifyEmail = $_POST['CopifyEmail'];
		// $CopifyApiKey = $_POST['CopifyApiKey'];
		// $CopifyLocale = $_POST['CopifyLocale'];
		$this->markTestSkipped();
	}
	
}
