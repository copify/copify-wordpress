<?php
require_once(__DIR__ . '/../../basics.php');
require_once(__DIR__ . '/../../Lib/CopifyApi.php');
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
            'CopifyWPUser' => '',
		);
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('add_option', 'CopifyLoginDetails', $toSave)
			->will($this->returnValue(true));
		$this->CopifyWordpress->CopifySettings();
	}

/**
 * testCopifySettingsSaveWithUser
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySettingsSaveWithUser() {
		$_POST['CopifyEmail'] = 'hello@newemail.com';
		$_POST['CopifyApiKey'] = '876453456786';
		$_POST['CopifyLocale'] = 'au';
        $_POST['CopifyWPUser'] = 6;
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress'));
        $this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_users', [])
			->will($this->returnValue([1,2]));
        $this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue(false));
		$toSave = array(
			'CopifyEmail' => 'hello@newemail.com',
			'CopifyApiKey' => '876453456786',
			'CopifyLocale' => 'au',
            'CopifyWPUser' => 6,
		);
		$this->CopifyWordpress->expects($this->at(2))
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
			->with('get_users', [])
			->will($this->returnValue([1,2]));
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$toSave = array(
			'CopifyEmail' => 'hello@newemail.com',
			'CopifyApiKey' => '876453456786',
			'CopifyLocale' => 'au',
            'CopifyWPUser' => ''
		);
		$this->CopifyWordpress->expects($this->at(2))
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
			->with('HTTP/1.0 404 Not Found');
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
		$version = $this->CopifyWordpress->getVersion();
		$this->assertEquals('1.2.0', $version);
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->any())
			->method('outputJson')
			->with($version);
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

/**
 * testCopifyRequestAlreadyPublished
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestAlreadyPublished() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists'));
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
			->method('CopifySetApiClass');
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyJobIdExists')
			->with(62343)
			->will($this->returnValue(true));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'Order 62343 already published'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 409 Conflict');
		$_GET["copify-action"] = true;
		$_GET["id"] = 62343;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestMissingCopy
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestMissingCopy() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
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
			->method('CopifySetApiClass');
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyJobIdExists')
			->with(62343)
			->will($this->returnValue(false));
		$job = array(
			'id' => 62343,
			'name' => 'some order name',
			'copy' => '',
			'job_status_id' => 3,
		);
		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with(62343)
			->will($this->returnValue($job));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'Can not find copy for order 62343'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 404 Not Found');
		$_GET["copify-action"] = true;
		$_GET["id"] = 62343;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestNotComplete
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestNotComplete() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
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
			->method('CopifySetApiClass');
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyJobIdExists')
			->with(62343)
			->will($this->returnValue(false));
		$job = array(
			'id' => 62343,
			'name' => 'some order name',
			'copy' => 'some copy is here',
			'job_status_id' => 2,
		);
		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with(62343)
			->will($this->returnValue($job));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'Order 62343 is not yet complete or approved'));
		$this->CopifyWordpress->expects($this->once())
			->method('setheader')
			->with('HTTP/1.0 404 Not Found');
		$_GET["copify-action"] = true;
		$_GET["id"] = 62343;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyRequestOrderPublished
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyRequestOrderPublished() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));

		$authors = new stdClass();
		$authors->data = new stdClass();
		$authors->data->ID = 22;
		$authorsMock = array(0 => $authors);

		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('get_users', 'role=administrator')
			->will($this->returnValue($authorsMock));

		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetApiClass');
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyJobIdExists')
			->with(62343)
			->will($this->returnValue(false));
		$job = array(
			'id' => 62343,
			'name' => 'some order name',
			'copy' => 'some copy is here',
			'job_status_id' => 3,
		);
		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with(62343)
			->will($this->returnValue($job));
		$newPost = array(
			'post_title' => $job['name'],
			'post_content' => $job['copy'],
			'post_status' => 'publish',
			'post_type' => 'post',
			'post_author' => 22,
		);
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyAddToPosts')
			->with(62343, $newPost)
			->will($this->returnValue(521));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('success' => true, 'message' => 'Order 62343 auto-published', 'wp_post_id' => 521));
		$_GET["copify-action"] = true;
		$_GET["id"] = 62343;
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyAdminMenu
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyAdminMenu() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('add_menu_page', 'Copify Wordpress Plugin', 'Copify', 'publish_posts', 'CopifyDashboard', array($this->CopifyWordpress, 'CopifyDashboard'), 'dashicons-edit', 6);
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('add_submenu_page', 'CopifyDashboard', 'Copify Order New Content', 'Order blog post', 'publish_posts', 'CopifyOrder', array($this->CopifyWordpress, 'CopifyOrder'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('wordpress')
			->with('add_submenu_page', 'CopifyDashboard', 'Copify Wordpress Settings', 'Settings', 'publish_posts', 'CopifySettings', array($this->CopifyWordpress, 'CopifySettings'));
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('add_submenu_page', 'CopifySettings', 'Copify View Job', 'View', 'publish_posts', 'CopifyViewJob', array($this->CopifyWordpress, 'CopifyViewJob'));
		$this->CopifyWordpress->CopifyAdminMenu();
	}

/**
 * testCopifyFlatten
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyFlatten() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$multi = array(
			0 => array(
				'id' => 4,
				'name' => 'Foo',
				'slug' => 'foo'
			),
			1 => array(
				'id' => 7,
				'name' => 'Bar',
				'slug' => 'bar'
			),
		);
		$expected = array(
			4 => 'Foo',
			7 => 'Bar'
		);
		$result = $this->CopifyWordpress->CopifyFlatten($multi);
		$this->assertEquals($expected, $result);
	}

/**
 * testCopifyPostFeedbackEmptyPost
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyPostFeedbackEmptyPost() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$_POST = null;
		$this->CopifyWordpress->expects($this->never())
			->method('CopifyAddToPosts');
		$this->CopifyWordpress->expects($this->never())
			->method('CopifySetPostThumbnailFromUrl');
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array('message' => 'POST request required', 'status' => 'error', 'response' => ''));
		$this->CopifyWordpress->CopifyPostFeedback();
	}

/**
 * testCopifyPostFeedbackMain
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyPostFeedbackMain() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView', 'jobFeedback'), array('foo@bar.com', '324532452345324'));
		$_POST = array(
			'type' => 'post',
			'action' => 'CopifyPostFeedback',
			'job_id' => 4233,
			'name' => 'my great post',
			'copy' => 'amazing copy',
			'comment' => 'good ta',
			'rating' => 4,
		);

		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetApiClass');

		$job = array(
			'id' => 4233,
			'name' => 'some order name',
			'copy' => 'chips',
			'job_status_id' => 3,
		);
		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with(4233)
			->will($this->returnValue($job));

		$feedback = array(
			'job_id' => 4233,
			'comment' => 'good ta',
			'rating' => 4,
		    'name' => 'my great post',
		    'copy' => 'amazing copy',
		);

		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobFeedback')
			->with($feedback)
			->will($this->returnValue(array('status' => 'success', 'id' => 9)));

		$newPost = array(
			'post_title' => 'some order name',
			'post_content' => 'chips',
			'post_status' => 'draft',
			'post_type' => 'post'  // [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may
		);

		$this->CopifyWordpress->expects($this->once())
			->method('CopifyAddToPosts')
			->with(4233, $newPost)
			->will($this->returnValue(2));


		$this->CopifyWordpress->expects($this->never())
			->method('CopifySetPostThumbnailFromUrl');

		$response = array();
		$response['status'] = 'success';
		$response['response'] = array('status' => 'success', 'id' => 9);
		$response['message'] = 'Job Approved';

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with($response);

		$this->CopifyWordpress->CopifyPostFeedback();
	}

/**
 * testCopifyPostFeedbackImage
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyPostFeedbackImage() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView', 'jobFeedback'), array('foo@bar.com', '324532452345324'));
		$_POST = array(
			'type' => 'post',
			'action' => 'CopifyPostFeedback',
			'job_id' => 54233,
			'name' => 'my great post',
			'copy' => 'amazing copy',
			'comment' => 'good ta',
			'rating' => 4,
			'image' => 'https://some.image.com/lolcat.png',
			'image_licence' => 'Foo blah <a href="https://some.image.com/lolcat.png">Foobar</a>',
		);

		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetApiClass');

		$job = array(
			'id' => 54233,
			'name' => 'some order name',
			'copy' => 'chips',
			'job_status_id' => 3,
		);
		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with(54233)
			->will($this->returnValue($job));

		$feedback = array(
			'job_id' => 54233,
			'comment' => 'good ta',
			'rating' => 4,
		    'name' => 'my great post',
		    'copy' => 'amazing copy',
		);

		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobFeedback')
			->with($feedback)
			->will($this->returnValue(array('status' => 'success', 'id' => 9)));

		$newPost = array(
			'post_title' => 'some order name',
			'post_content' => 'chips',
			'post_status' => 'draft',
			'post_type' => 'post'  // [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may
		);

		$this->CopifyWordpress->expects($this->once())
			->method('CopifyAddToPosts')
			->with(54233, $newPost)
			->will($this->returnValue(2));

		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetPostThumbnailFromUrl')
			->with(2, 'https://some.image.com/lolcat.png', array('image_licence' => 'Foo blah <a href="https://some.image.com/lolcat.png">Foobar</a>'))
			->will($this->returnValue(2));

		$response = array();
		$response['status'] = 'success';
		$response['response'] = array('status' => 'success', 'id' => 9);
		$response['message'] = 'Job Approved';

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with($response);

		$this->CopifyWordpress->CopifyPostFeedback();
	}

/**
 * testCopifySetPostThumbnailBadHost
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Bad image host
 **/
	public function testCopifySetPostThumbnailBadHost() {
		$image = 'http://farm1.pwned.com/71/185461246_ad07aa0f2d_o.php';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailBadExt
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Bad image type
 **/
	public function testCopifySetPostThumbnailBadExt() {
		$image = 'http://farm1.copify.pwned.com/71/185461246_ad07aa0f2d_o.php';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailBadUploadsDir
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException Exception
 * @expectedExceptionMessage Unable to create directory /wordpress-3.9/wp-content/uploads. Is its parent directory writable by the server?
 **/
	public function testCopifySetPostThumbnailBadUploadsDir() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$wp_upload_dir = array(
			'error' => 'Unable to create directory /wordpress-3.9/wp-content/uploads. Is its parent directory writable by the server?'
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailCantLoadUrl
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException Exception
 * @expectedExceptionMessage Failed to fetch http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg
 **/
	public function testCopifySetPostThumbnailCantLoadUrl() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->once())
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue(false));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailUploadBitsFails
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException Exception
 * @expectedExceptionMessage Invalid file type
 **/
	public function testCopifySetPostThumbnailUploadBitsFails() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents', 'unique'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->at(1))
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue('image data'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('unique')
			->will($this->returnValue('53a2a5db214eb'));
		$wp_upload_bits = array(
			'error' => 'Invalid file type'
		);
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_upload_bits', '53a2a5db214eb.jpg', null, 'image data')
			->will($this->returnValue($wp_upload_bits));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailInsertAttachFails
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException Exception
 * @expectedExceptionMessage Failed to create attachment
 **/
	public function testCopifySetPostThumbnailInsertAttachFails() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents', 'unique'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->at(1))
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue('image data'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('unique')
			->will($this->returnValue('53a2a5db214eb'));
		$wp_upload_bits = array(
			'file' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06/53a2a5db214eb.jpg'
		);
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_upload_bits', '53a2a5db214eb.jpg', null, 'image data')
			->will($this->returnValue($wp_upload_bits));
		$wp_check_filetype = array(
			'ext' => 'jpg',
			'type' => 'image/jpeg'
		);
		$this->CopifyWordpress->expects($this->at(4))
			->method('wordpress')
			->with('wp_check_filetype', '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue($wp_check_filetype));
		$wp_insert_attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => '53a2a5db214eb', // preg_replace( '/\.[^.]+$/', '', basename($filepath)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_excerpt' => 'Image from Flickr'
		);
		$this->CopifyWordpress->expects($this->at(5))
			->method('wordpress')
			->with('wp_insert_attachment', $wp_insert_attachment, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue(0));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnailSetThumbFails
 *
 * @return void
 * @author Rob Mcvey
 * @expectedException Exception
 * @expectedExceptionMessage Failed to set post image
 **/
	public function testCopifySetPostThumbnailSetThumbFails() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents', 'unique', 'setUpdateAttachmentMeta'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->at(1))
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue('image data'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('unique')
			->will($this->returnValue('53a2a5db214eb'));
		$wp_upload_bits = array(
			'file' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06/53a2a5db214eb.jpg'
		);
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_upload_bits', '53a2a5db214eb.jpg', null, 'image data')
			->will($this->returnValue($wp_upload_bits));
		$wp_check_filetype = array(
			'ext' => 'jpg',
			'type' => 'image/jpeg'
		);
		$this->CopifyWordpress->expects($this->at(4))
			->method('wordpress')
			->with('wp_check_filetype', '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue($wp_check_filetype));
		$wp_insert_attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => '53a2a5db214eb', // preg_replace( '/\.[^.]+$/', '', basename($filepath)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_excerpt' => 'Image from Flickr'
		);
		$this->CopifyWordpress->expects($this->at(5))
			->method('wordpress')
			->with('wp_insert_attachment', $wp_insert_attachment, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue(432));
		$this->CopifyWordpress->expects($this->at(6))
			->method('setUpdateAttachmentMeta')
			->with(432, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg', array());
		$this->CopifyWordpress->expects($this->at(7))
			->method('wordpress')
			->with('set_post_thumbnail', 4, 432)
			->will($this->returnValue(false));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
	}

/**
 * testCopifySetPostThumbnail
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySetPostThumbnail() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents', 'unique', 'setUpdateAttachmentMeta'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->at(1))
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue('image data'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('unique')
			->will($this->returnValue('53a2a5db214eb'));
		$wp_upload_bits = array(
			'file' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06/53a2a5db214eb.jpg'
		);
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_upload_bits', '53a2a5db214eb.jpg', null, 'image data')
			->will($this->returnValue($wp_upload_bits));
		$wp_check_filetype = array(
			'ext' => 'jpg',
			'type' => 'image/jpeg'
		);
		$this->CopifyWordpress->expects($this->at(4))
			->method('wordpress')
			->with('wp_check_filetype', '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue($wp_check_filetype));
		$wp_insert_attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => '53a2a5db214eb', // preg_replace( '/\.[^.]+$/', '', basename($filepath)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_excerpt' => 'Image from Flickr'
		);
		$this->CopifyWordpress->expects($this->at(5))
			->method('wordpress')
			->with('wp_insert_attachment', $wp_insert_attachment, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue(432));
		$this->CopifyWordpress->expects($this->at(6))
			->method('setUpdateAttachmentMeta')
			->with(432, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg');
		$this->CopifyWordpress->expects($this->at(7))
			->method('wordpress')
			->with('set_post_thumbnail', 4, 432)
			->will($this->returnValue(211));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image);
		$this->assertEquals(211, $result);
	}

/**
 * testCopifySetPostThumbnailWithMeta
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifySetPostThumbnailWithMeta() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', '_file_get_contents', 'unique', 'setUpdateAttachmentMeta'));
		$wp_upload_dir = array(
			'path' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06',
			'subdir' => '/2014/06',
			'basedir' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads',
			'baseurl' => 'http://localhost.3dlockers.com/wp-content/uploads',
			'error' => false
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('wp_upload_dir')
			->will($this->returnValue($wp_upload_dir));
		$this->CopifyWordpress->expects($this->at(1))
			->method('_file_get_contents')
			->with('http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue('image data'));
		$this->CopifyWordpress->expects($this->at(2))
			->method('unique')
			->will($this->returnValue('53a2a5db214eb'));
		$wp_upload_bits = array(
			'file' => '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg',
			'url' => 'http://localhost.3dlockers.com/wp-content/uploads/2014/06/53a2a5db214eb.jpg'
		);
		$this->CopifyWordpress->expects($this->at(3))
			->method('wordpress')
			->with('wp_upload_bits', '53a2a5db214eb.jpg', null, 'image data')
			->will($this->returnValue($wp_upload_bits));
		$wp_check_filetype = array(
			'ext' => 'jpg',
			'type' => 'image/jpeg'
		);
		$this->CopifyWordpress->expects($this->at(4))
			->method('wordpress')
			->with('wp_check_filetype', '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue($wp_check_filetype));
		$wp_insert_attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => '53a2a5db214eb', // preg_replace( '/\.[^.]+$/', '', basename($filepath)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_excerpt' => 'Image from Flickr'
		);
		$this->CopifyWordpress->expects($this->at(5))
			->method('wordpress')
			->with('wp_insert_attachment', $wp_insert_attachment, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg')
			->will($this->returnValue(432));
		$meta = array(
			'copify_attr_photo_title' => 'Yellow-bellied Slider Turtle (Trachemys scripta scripta)',
			'copify_attr_url' => 'https://www.flickr.com/photos/bees/9968828954/',
			'copify_attr_user' => 'bees',
			'copify_attr_user_url' => 'http://www.flickr.com/photos/bees',
			'copify_attr_cc_license' => 4,
			'copify_attr_cc_license_url' => 'http://creativecommons.org/licenses/by/4.0/'
		);
		$this->CopifyWordpress->expects($this->at(6))
			->method('setUpdateAttachmentMeta')
			->with(432, '/Users/robmcvey/Projects/wordpress-3.9/wp-content/uploads/2014/06/53a2a5db214eb.jpg', $meta);
		$this->CopifyWordpress->expects($this->at(7))
			->method('wordpress')
			->with('set_post_thumbnail', 4, 432)
			->will($this->returnValue(211));
		$image = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$result = $this->CopifyWordpress->CopifySetPostThumbnailFromUrl(4, $image, $meta);
		$this->assertEquals(211, $result);
	}

/**
 * testSetImage
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testSetImage() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
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
			->method('CopifySetPostThumbnailFromUrl')
			->with(22, 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg')
			->will($this->returnValue(421));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'success' => true,
				'message' => 'Image for post 22 set to http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg',
				'set_post_thumbnail' => 421
			));
		$_GET['wp_post_id'] = 22;
		$_GET["copify-action"] = "set-image";
		$_GET["image-url"] = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testSetImageWithMeta
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testSetImageWithMeta() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$meta = array(
			'copify_attr_photo_title' => 'Yellow-bellied Slider Turtle (Trachemys scripta scripta)',
			'copify_attr_url' => 'https://www.flickr.com/photos/bees/9968828954/',
			'copify_attr_user' => 'bees',
			'copify_attr_user_url' => 'http://www.flickr.com/photos/bees',
			'copify_attr_cc_license' => 4,
			'copify_attr_cc_license_url' => 'http://creativecommons.org/licenses/by/4.0/'
		);
		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetPostThumbnailFromUrl')
			->with(22, 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg', $meta)
			->will($this->returnValue(421));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'success' => true,
				'message' => 'Image for post 22 set to http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg',
				'set_post_thumbnail' => 421
			));
		$_GET['wp_post_id'] = 22;
		$_GET["copify-action"] = "set-image";
		$_GET["copify_attr_photo_title"] = 'Yellow-bellied Slider Turtle (Trachemys scripta scripta)';
		$_GET["copify_attr_url"] = 'https://www.flickr.com/photos/bees/9968828954/';
		$_GET["copify_attr_user"] = 'bees';
		$_GET["copify_attr_user_url"] = 'http://www.flickr.com/photos/bees';
		$_GET["copify_attr_cc_license"] = 4;
		$_GET["copify_attr_cc_license_url"] = 'http://creativecommons.org/licenses/by/4.0/';
		$_GET["image-url"] = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testSetImageMissingParams
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testSetImageMissingParams() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->never())
			->method('CopifySetPostThumbnailFromUrl');
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'message' => 'Missing params wp_post_id and image-url',
			));
		$_GET["copify-action"] = "set-image";
		$_GET["image-url"] = 'http://farm1.staticflickr.com/71/185461246_ad07aa0f2d_o.jpg';
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testDeleteImageMissingParams
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testDeleteImageMissingParams() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
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
			->with(array(
				'message' => 'Missing params wp_post_id',
			));
		$_GET["copify-action"] = "delete-image";
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testDeleteImage
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testDeleteImage() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('delete_post_thumbnail', 77)
			->will($this->returnValue(true));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'success' => true,
				'message' => 'Image for post 77 was removed',
			));
		$_GET["wp_post_id"] = 77;
		$_GET["copify-action"] = "delete-image";
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testUnpublishPostMissingParams
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testUnpublishPostMissingParams() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'message' => 'Missing params wp_post_id',
			));
		$_GET["copify-action"] = "unpublish-post";
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testUnpublishPostFails
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testUnpublishPostFails() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('wp_trash_post', 77)
			->will($this->returnValue(false));
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'message' => 'Failed to trash post 77',
			));
		$_GET["wp_post_id"] = 77;
		$_GET["copify-action"] = "unpublish-post";
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testUnpublishPost
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testUnpublishPost() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl', 'CopifyBeforeDeletePost'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		$mockVal = array(
			'CopifyEmail' => 'foo@bar.com',
			'CopifyApiKey' => '324532452345324',
			'CopifyLocale' => 'uk',
		);
		$this->CopifyWordpress->expects($this->at(0))
			->method('wordpress')
			->with('get_option', 'CopifyLoginDetails', false)
			->will($this->returnValue($mockVal));
		$this->CopifyWordpress->expects($this->at(1))
			->method('wordpress')
			->with('wp_trash_post', 77)
			->will($this->returnValue(true));
		$this->CopifyWordpress->expects($this->once())
			->method('CopifyBeforeDeletePost')
			->with(77);
		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with(array(
				'success' => true,
				'message' => 'Post 77 moved to trash',
			));
		$_GET["wp_post_id"] = 77;
		$_GET["copify-action"] = "unpublish-post";
		$_GET["token"] = 'd0cf87af82e652220087e7613f0332abc1461a0f';
		$this->CopifyWordpress->CopifyRequestFilter();
	}

/**
 * testCopifyAddFlickrAttributionNoChange
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyAddFlickrAttributionNoChange() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl', 'CopifyBeforeDeletePost', '_wp_get_attachment_metadata'));
		$_wp_get_attachment_metadata = array(
		    'width' => 2896,
		    'height' => 1936,
		    'file' => '2014/06/53a2eaaf8fdd8.jpg',
		    'image_meta' => array(
				'aperture' => 0,
				'credit' => '',
				'camera' => '',
				'caption' => '',
				'created_timestamp' => 0,
				'copyright' => '',
				'focal_length' => 0,
				'iso' => 0,
				'shutter_speed' => 0,
				'title' => '',
			)
		);
		$this->CopifyWordpress->expects($this->once())
			->method('_wp_get_attachment_metadata')
			->will($this->returnValue($_wp_get_attachment_metadata));
		$result = $this->CopifyWordpress->CopifyAddFlickrAttribution('foo bar');
		$this->assertEquals('foo bar', $result);
	}

/**
 * testCopifyAddFlickrAttribution
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyAddFlickrAttribution() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl', 'CopifyBeforeDeletePost', '_wp_get_attachment_metadata'));
		$_wp_get_attachment_metadata = array(
		    'width' => 2896,
		    'height' => 1936,
		    'file' => '2014/06/53a2eaaf8fdd8.jpg',
		    'image_meta' => array(
				'aperture' => 0,
				'credit' => '',
				'camera' => '',
				'caption' => '',
				'created_timestamp' => 0,
				'copyright' => '',
				'focal_length' => 0,
				'iso' => 0,
				'shutter_speed' => 0,
				'title' => '',
			),
			'copify_attr_url' => 'https://www.flickr.com/photos/sixteenmilesofstring/8256206923/in/set-72157632200936657',
		);
		$this->CopifyWordpress->expects($this->once())
			->method('_wp_get_attachment_metadata')
			->will($this->returnValue($_wp_get_attachment_metadata));
		$result = $this->CopifyWordpress->CopifyAddFlickrAttribution('foo bar');
		$expected = 'foo bar<div style="display:block;font-size:9px;">Photo: <a target="blank" title="Creative Commons" href="https://www.flickr.com/photos/sixteenmilesofstring/8256206923/in/set-72157632200936657" rel="nofollow">Creative Commons</a></div>';
		$this->assertEquals($expected, $result);
	}

/**
 * testCopifyAddFlickrAttributionFull
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function testCopifyAddFlickrAttributionFull() {
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'setheader', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts', 'CopifySetPostThumbnailFromUrl', 'CopifyBeforeDeletePost', '_wp_get_attachment_metadata'));
		$_wp_get_attachment_metadata = array(
		    'width' => 2896,
		    'height' => 1936,
		    'file' => '2014/06/53a2eaaf8fdd8.jpg',
		    'image_meta' => array(
				'aperture' => 0,
				'credit' => '',
				'camera' => '',
				'caption' => '',
				'created_timestamp' => 0,
				'copyright' => '',
				'focal_length' => 0,
				'iso' => 0,
				'shutter_speed' => 0,
				'title' => '',
			),
			'copify_attr_photo_title' => 'Yellow-bellied Slider Turtle (Trachemys scripta scripta)',
			'copify_attr_url' => 'https://www.flickr.com/photos/bees/9968828954/',
			'copify_attr_user' => 'bees',
			'copify_attr_user_url' => 'http://www.flickr.com/photos/bees',
			'copify_attr_cc_license' => 4,
			'copify_attr_cc_license_url' => 'http://creativecommons.org/licenses/by/4.0/'
		);
		$this->CopifyWordpress->expects($this->once())
			->method('_wp_get_attachment_metadata')
			->will($this->returnValue($_wp_get_attachment_metadata));
		$result = $this->CopifyWordpress->CopifyAddFlickrAttribution('foo bar');
		$expected = 'foo bar<div style="display:block;font-size:9px;">Photo: <a target="blank" title="Yellow-bellied Slider Turtle (Trachemys scripta scripta)" href="https://www.flickr.com/photos/bees/9968828954/" rel="nofollow">Yellow-bellied Slider Turtle (Trachemys scripta scripta)</a> by <a href="http://www.flickr.com/photos/bees" target="blank" title="bees" rel="nofollow">bees</a> licensed under <a href="http://creativecommons.org/licenses/by/4.0/" target="blank" rel="nofollow">Creative commons 4</a></div>';
		$this->assertEquals($expected, $result);
	}

/**
 * Tests that CopifyMoveToDrafts does the correct stuff when the job isn't already in the database
 *
 * @author Chris Green
 **/
	public function testCopifyMoveToDraftNotExist() {
		// Mock a load of stuff
		$_POST['job_id'] = '186';
		$_POST['post_type'] = 'post';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		// Job we want to post
		$job = array(
			'id' => 186,
			'name' => 'order name',
			'copy' => 'some copy'
		);
		// The post
		$new_post = array(
			'post_title' => 'order name',
			'post_content' => 'some copy',
			'post_status' => 'draft',
			'post_type' => 'post'
		);
		// Correct response
		$response = array(
			'status' => 'success',
			'message' => 'Order moved to drafts',
			'response' => true,
		);

		$this->CopifyWordpress->expects($this->once())
			->method('CopifySetApiClass');

		$this->CopifyWordpress->Api->expects($this->once())
			->method('jobsView')
			->with($this->equalTo('186'))
			->will($this->returnValue($job));

		$this->CopifyWordpress->expects($this->once())
			->method('CopifyJobIdExists')
			->with($this->equalTo('186'))
			->will($this->returnValue(false)); // Job doesn't exist in db

		$this->CopifyWordpress->expects($this->once())
			->method('CopifyAddToPosts')
			->with($this->equalTo(186), $this->equalTo($new_post));

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with($this->equalTo($response));

		$this->CopifyWordpress->CopifyMoveToDrafts();
	}

/**
 * Tests that CopifyMoveToDrafts returns correct JSON when there is no POST data
 *
 * @author Chris Green
 **/
	public function testCopifyMoveToDraftNoPost() {
		// Mock a load of stuff
		$_POST = array();
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		// Correct response
		$response = array(
			'status' => 'error',
			'message' => 'POST request required',
			'response' => '',
		);

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with($this->equalTo($response));

		$this->CopifyWordpress->CopifyMoveToDrafts();
	}

/**
 * Tests that CopifyMoveToDrafts returns correct JSON when the post type is incorrect
 *
 * @author Chris Green
 **/
	public function testCopifyMoveToDraftWrongPostType() {
		// Mock a load of stuff
		$_POST['job_id'] = 186;
		$_POST['post_type'] = 'hello';
		$this->CopifyWordpress = $this->getMock('CopifyWordpress', array('wordpress', 'outputJson', 'CopifySetApiClass', 'CopifyJobIdExists', 'CopifyAddToPosts'));
		$this->CopifyWordpress->Api = $this->getMock('CopifyApi', array('jobsView'), array('foo@bar.com', '324532452345324'));
		// Correct response
		$response = array(
			'status' => 'error',
			'message' => 'Post type must be either post or page',
			'response' => '',
		);

		$this->CopifyWordpress->expects($this->once())
			->method('outputJson')
			->with($this->equalTo($response));

		$this->CopifyWordpress->CopifyMoveToDrafts();
	}

}
