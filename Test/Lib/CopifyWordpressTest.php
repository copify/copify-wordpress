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
 * Setup method
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function setUp() {
		parent::setUp();
		ob_start();
	}

/**
 * Tear down
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function tearDown() {
		parent::tearDown();
		ob_get_clean();
	}

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
 * testCopifySettingsSave
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySettingsSave() {
		$_POST['CopifyEmail'] = 'hello@newemail.com';
		$_POST['CopifyApiKey'] = '876453456786';
		$_POST['CopifyLocale'] = 'au';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress'));
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue(false));
		$toSave = array(
			'CopifyEmail' => 'hello@newemail.com',
			'CopifyApiKey' => '876453456786',
			'CopifyLocale' => 'au',
		);
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('add_option', 'CopifyLoginDetails', $toSave)
			->will($this->returnValue(true));	
		$this->CopifyWordpress->CopifySettings();
	}

/**
 * testCopifySettingsUpdate
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySettingsUpdate() {
		$_POST['CopifyEmail'] = 'hello@newemail.com';
		$_POST['CopifyApiKey'] = '876453456786';
		$_POST['CopifyLocale'] = 'au';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$toSave = array(
			'CopifyEmail' => 'hello@newemail.com',
			'CopifyApiKey' => '876453456786',
			'CopifyLocale' => 'au',
		);
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('update_option', 'CopifyLoginDetails', $toSave)
			->will($this->returnValue(true));	
		$this->CopifyWordpress->CopifySettings();
	}

/**
 * testCopifyRequestFilterBadToken
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestFilterBadToken() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader'));
		$this->CopifyWordpress->expects($this->never())
			->method('wordpress');
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'Must include auth token'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 400 Bad Request');	
		$_GET["copify-action"] = true;	
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestFilterBadApiDetails
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestFilterBadApiDetails() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader'));
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress');
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'Copify plugin not conigured'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 400 Bad Request');	
		$_GET["copify-action"] = true;
		$_GET["token"] = 'blah';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestFilterTokenMisMatch
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestFilterTokenMisMatch() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->once())
				->method('outputJson')
				->with(array('message' => 'Permission denied'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 403 Forbidden');	
		$_GET["copify-action"] = true;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestFilterCheckToken
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestFilterCheckToken() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->once())
				->method('outputJson')
				->with('1.0.4');
		$_GET["copify-action"] = true;
		$_GET["check"] = 'version';
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestMissingId
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestMissingId() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->once())
				->method('outputJson')
				->with(array('message' => 'Must include order ID'));
		$_GET["copify-action"] = true;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

}
