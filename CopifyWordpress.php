<?php
/*
Plugin Name: Copify
Plugin URI: https://github.com/copify/copify-wordpress
Description: Publish content sourced through Copify to your WordPress blog
Version: 0.9.4
Author: Rob McVey
Author URI: http://www.copify.com/
License: GPL2

Copyright 2012  Rob McVey  (email:rob@copify.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require('Lib/Copify.php');

if(!defined('DS')) {
	define('DS' , DIRECTORY_SEPARATOR);
}

class CopifyWordpress {

	public $Copify;
	
	public $copifyDirName = 'copify';
	
				
	/**
	 * cssAndScripts
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyCssAndScripts() {
		
		// JS - our own
		$js_url = plugins_url($this->copifyDirName.DS.'js'.DS.'Copify.js');
		wp_enqueue_script('copify' , $js_url, array('jquery'));
		
		// JS - bootsrap modal
		$bootstrap_url = plugins_url($this->copifyDirName.DS.'js'.DS.'bootstrap-modal.js');
		wp_enqueue_script('bootstrap-modal' , $bootstrap_url, array('jquery'));
		
		// JS - jquery validate
		$jquery_validate = plugins_url($this->copifyDirName.DS.'js'.DS.'jquery.validate.js');
		wp_enqueue_script('jquery.validate' , $jquery_validate, array('jquery'));
		
		// CSS
		$css_url = plugins_url($this->copifyDirName.DS.'css'.DS.'Copify.css');
		wp_enqueue_style('copify' , $css_url); 
		
	}
	
	
	/**
	 * Settings page
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifySettings() {
		
		try {
			
			// Get API credentials
			$CopifyLoginDetails = get_option('CopifyLoginDetails' , false);
			
			// API crendtials form submitted
			if(!empty($_POST) && isset($_POST['CopifyEmail'])) {

				// The form input
				$CopifyEmail = $_POST['CopifyEmail'];
				$CopifyApiKey = $_POST['CopifyApiKey'];
				$CopifyLocale = $_POST['CopifyLocale'];
				
				// Array to save
				$toSave = array(
					'CopifyEmail' => $CopifyEmail,
					'CopifyApiKey' => $CopifyApiKey,
					'CopifyLocale' => $CopifyLocale
				);
				
				// Update or Add?
				if($CopifyLoginDetails) {
					update_option('CopifyLoginDetails', $toSave);
				} else {
					add_option('CopifyLoginDetails', $toSave, null, 'no');
				}
				
				// Clear any options cached (maybe they switched locales? So this is impoartant)
				$this->CopifyClearCache();
				
				$success = "Settings updated!";
				
			} 
			// form not submitted but we have details already
			elseif($CopifyLoginDetails) {
				$CopifyEmail = $CopifyLoginDetails['CopifyEmail'];
				$CopifyApiKey = $CopifyLoginDetails['CopifyApiKey'];
				$CopifyLocale = $CopifyLoginDetails['CopifyLocale'];
			} 
			
			// All available locales
			$CopifyAvailableLocales = array(
				'uk' => 'UK',
				'us' => 'USA',
			);
			
			// Flash message of some kind?
			if(isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}
			
			
		} 
		catch(Exception $e) {
			$error = $e->getMessage();
		}
		
		require('Views/CopifySettings.php');
	}
	
	
	/**
	 * Initialise the Copify API class with the credentials from the options table
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifySetApiClass() {
		
		$CopifyLoginDetails = get_option('CopifyLoginDetails' , false);

		// Check login stored
		if(!$CopifyLoginDetails) {
			wp_die('<pre>To connect to Copify you must enter your API key on the <a href="admin.php?page=CopifySettings">Settings page</a></pre>');
		} 
		
		// Requires cURL
		if(!function_exists('curl_init')) {
			wp_die('<pre>This Plugin requires cURL to be installed</pre>');
		}

		// Initialise the Copify API helper class
		$this->Copify = new Copify($CopifyLoginDetails['CopifyEmail'] , $CopifyLoginDetails['CopifyApiKey']);
		
		// Set the correct end point for the API
		$this->Copify->basePath = sprintf('https://%s.copify.com/api' , $CopifyLoginDetails['CopifyLocale']);

	}
	
	
	/**
	 * Main Copify page renders a table of all jobs
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyDashboard() {
		
		try {
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
								
			// WP Admin slug
			$page = 'CopifyDashboard';
			if(isset($_GET['page']) && !empty($_GET['page'])) {
				$page = $_GET['page'];
			}
				
			// Page params?
			$pageNumber = 1;
			if(isset($_GET['pageNumber']) && !empty($_GET['pageNumber'])) {
				$pageNumber = $_GET['pageNumber'];
			}
				
			// Sort params?
			$sort = 'id';
			if(isset($_GET['sort']) && !empty($_GET['sort'])) {
				$sort = $_GET['sort'];
			}
				
			// Direction params?
			$direction = 'desc';
			if(isset($_GET['direction']) && !empty($_GET['direction'])) {
				$direction = $_GET['direction'];
			}
			
			// Flash message of some kind?
			if(isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}

			// Get the jobs resource from API
			$CopifyJobs = $this->Copify->jobsIndex(false , $pageNumber , $sort , $direction);
				
			// Get the total amount of jobs
			$total = $CopifyJobs['total'];
				
			// Current page
			$paginateNumber = $CopifyJobs['page'];
				
			// Total Pages
			$totalPages = ceil($total / 20);
				
			// Prev page
			$prevPage = 1;
			if($paginateNumber > 1) {
				$prevPage = $paginateNumber - 1;
			}
				
			// Next page
			$nextPage = $totalPages;
			if($paginateNumber < $totalPages) {
				$nextPage = $paginateNumber + 1;
			}

			// Get category, budget and status resources from API
			$CopifyCategories = $this->CopifyGetJobCategories();
			$CopifyBudgets = $this->CopifyGetJobBudgets();
			$CopifyStatuses = $this->CopifyGetJobStatuses();
				
			// Create a plain array of categories with the ID as the key
			$categoryList = $this->CopifyFlatten($CopifyCategories);
			$budgetList = $this->CopifyFlatten($CopifyBudgets);
			$statusList = $this->CopifyFlatten($CopifyStatuses);
			
			// An array of Copify job IDs which are saved in wordpress as posts
			$CopifyPostIds = $this->CopifyGetCopifyPostIds();

		}
		catch(Exception $e) {
			
			$error = $e->getMessage();
			
			// Bad API creectials?
			if(preg_match('/user-agent/i' , $error)) {
				$error .= '. <a href="?page=CopifySettings" >Check settings</a>';
			}
			
			
		}
		
		require('Views/CopifyDashboard.php');
	}
	
	
	/**
	 * Page to place a new order
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyOrder() {

		try {
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
		
			// Get category, budget and status resources from API
			$CopifyCategories = $this->CopifyGetJobCategories();
			$CopifyBudgets = $this->CopifyGetJobBudgets();
			$CopifyStatuses = $this->CopifyGetJobStatuses();
				
			// Create a plain array of categories with the ID as the key
			$categoryList = $this->CopifyFlatten($CopifyCategories);
			$budgetList = $this->CopifyFlatten($CopifyBudgets);
			$statusList = $this->CopifyFlatten($CopifyStatuses);
			
			// Sort categories alphabetacly
			asort($categoryList);

		} 
		catch(Exception $e) {	

			$error = $e->getMessage();	
			
			// Is this a balance exception? Link to "add more funds"
			if(preg_match('/funds/i' , $error)) {
				$error .= '. <a href="http://www.copify.com/payments/add" target="blank" >Make a payment</a>';
			}
			
			// Bad API creectials?
			if(preg_match('/user-agent/i' , $error)) {
				$error .= '. <a href="?page=CopifySettings" >Check settings</a>';
			}
			

		}

		require('Views/CopifyOrder.php');
		
	}
	
	
	/**
	 * Ajax method to place and order via the API
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyAjaxOrder() {

		// A default response
		$response = array(
			'status' => 'error',
			'message' => 'Broke',
			'response' => ''
		);
		
		try {
						
			if(empty($_POST)) {
				throw new Exceptioon('POST request required');
			}
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
			
			parse_str($_POST['job'], $newJob);
			
			$response['response'] = $this->Copify->jobsAdd($newJob);
			$response['message'] = 'New job added';
			$response['status'] = 'success';
			
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.

		} 
		catch(Exception $e) {
			$response['message'] = $e->getMessage();
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		}

	}
	
	
	/**
	 * View a single job via the API
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyViewJob() {
		
		try {
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
			
			// The job ID	
			$jobId = $_GET['id'];
			
			// Get the job record from the API	
			$job = $this->Copify->jobsView($jobId); // Maybe cache this if already approved?
				
			// Get category, budget and status resources from API
			$CopifyCategories = $this->CopifyGetJobCategories();
			$CopifyBudgets = $this->CopifyGetJobBudgets();
			$CopifyStatuses = $this->CopifyGetJobStatuses();
				
			// Create a plain array of categories with the ID as the key
			$categoryList = $this->CopifyFlatten($CopifyCategories);
			$budgetList = $this->CopifyFlatten($CopifyBudgets);
			$statusList = $this->CopifyFlatten($CopifyStatuses);
				
			// Get the writer profile if we have one...
			if($job['job_status_id'] == 3 && !empty($job['writer'])) {
				try {
					$CopifyWriter = $this->CopifyGetUserProfile($job['writer']);
				} catch(Exception $e) {
					if(preg_match("/publically/i" , $e->getMessage())) {
						$CopifyWriter = $this->CopifyGetUserProfile(2);
					}
				}
			}
			
			// Set flag if this job is already in Wordpress as a post
			$CopifyJobIsPostAlready = $this->CopifyJobIdExists($job['id']);

			// Flash message of some kind?
			if(isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}

		}
		catch(Exception $e) {
			$error = $e->getMessage();
		}
		
		require('Views/CopifyViewJob.php');
		
	}
	
	
	/**
	 * Handle ajax feedback form post. The user is approving a job and leaving feedback
	 * Outputs a JSON response
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyPostFeedback() {
		
		// A default response
		$response = array(
			'status' => 'error',
			'message' => 'Broke',
			'response' => ''
		);
		
		try {
			
			if(empty($_POST)) {
				throw new Exceptioon('POST request required');
			}
			
			// Initialise Copify API class
			$this->CopifySetApiClass();

			// Get the post data
			$feedback = $_POST;
			
			// Remove the action key
			unset($feedback['action']);
			
			// Check nothing added to array. (The API handles validation anyway but meh)
			if(count($feedback) != 5) {
				throw new Exception('Feedback post data invalid format');
			}
			
			// Get the job record from API
			$job = $this->Copify->jobsView($feedback['job_id']);
			
			// Submit feedback via API
			$result = $this->Copify->jobFeedback($feedback);
			
			// Check it is not already in the database, not pop it in
			if(!$this->CopifyJobIdExists($feedback['job_id'])) {
				
				$newPost = array(
					'post_title' => $job['name'],
					'post_content' => $job['copy'],
					'post_status' => 'draft',
				);

				$this->CopifyAddToDrafts($feedback['job_id'] , $newPost);
				
			}	

			// Build the success response
			$response['status'] = 'success';
			$response['response'] = $result;
			$response['message'] = 'Job Approved';
			
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.

		} 
		catch(Exception $e) {
			$response['message'] = $e->getMessage();
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		}
	}
	
	
	/**
	 * Handle ajax request to move a job to drafts
	 * Outputs a JSON response
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyMoveToDrafts() {

		// A default response
		$response = array(
			'status' => 'error',
			'message' => 'Broke',
			'response' => ''
		);
		
		try {
			
			if(empty($_POST)) {
				throw new Exceptioon('POST request required');
			}
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
			
			// Get the job id from the post data
			$job_id = $_POST['job_id'];
			
			// Get the job record from API
			$job = $this->Copify->jobsView($job_id);
			
			// Check it is not already in the database, not pop it in
			if(!$this->CopifyJobIdExists($job_id)) {
				
				$newPost = array(
					'post_title' => $job['name'],
					'post_content' => $job['copy'],
					'post_status' => 'draft',
				);

				$this->CopifyAddToDrafts($job_id , $newPost);
				
			}	

			// Build the success response
			$response['status'] = 'success';
			$response['response'] = $result;
			$response['message'] = 'Job Moved to drafts';
			
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		
		}	
		catch(Exception $e) {
			$response['message'] = $e->getMessage();
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		}
		
	}
	
	
	/**
	 * Ajax request to retrieve a quote for a word count
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyQuoteWords() {
		
		// A default response
		$response = array(
			'status' => 'error',
			'message' => 'Broke',
			'response' => ''
		);
		
		try {
			
			if(empty($_POST)) {
				throw new Exceptioon('POST request required');
			}
			
			// Get the thingy
			$job_budget_id = $_POST['job_budget_id'];
			$words = $_POST['words'];
			
			// Initialise Copify API class
			$this->CopifySetApiClass();
			
			// Fetch a quote via API
			$result = $this->Copify->jobBudgetsView($job_budget_id,$words);
			
			// Build the success response
			$response['status'] = 'success';
			$response['response'] = $result;
			$response['message'] = sprintf('Quote for %s' , $words);
			
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		
		}	
		catch(Exception $e) {
			$response['message'] = $e->getMessage();
			echo json_encode($response);
			die(); // urgh. Wordpress u so silly.
		}
		
	}
	
	
	/**
	 * Adds a post to wordpress db as a draft and saves an option with the ID ref
	 *
	 * @return int $new_wp_id The new WordPress post id
	 * @author Rob Mcvey
	 * @param int $job_id The Copify job ID
	 * @param array $newpost An array (name, copy etc.) to save in WP database
	 * Should be in format :
	 * $newPost = array(
	 *		'post_title' => 'Blog title',
	 *		'post_content' => 'The copy of the blog blah blah...',
	 *		'post_status' => 'draft',
	 * );
	 **/
	public function CopifyAddToDrafts($job_id, $newPost) {
		
		// Create the post
		$new_wp_id = wp_insert_post($newPost , $wp_error);
			
		// Check for errors
		if(is_wp_error($new_wp_id)) {
			$errorMessage = $new_wp_id->get_error_message();
			throw new Exception($errorMessage);
		}
				
		// Pop in an option for the Copify Job ID
		add_option('CopifyJobIdExists'.$job_id, $new_wp_id, null, 'no');
		
		return $new_wp_id;
	}
	
	
	/**
	 * Remove the option flag when a post is deleted from wordpress by its ID.
	 * If we don't do this they can delete a post and not have the option to add it to drafts again
	 *
	 * @return void
	 * @author Rob Mcvey
	 * @param int $wp_post_id The wordpress post ID we want to remove our flag for
	 **/
	public function CopifyBeforeDeletePost($wp_post_id) {
		global $wpdb;
		$query = "DELETE FROM $wpdb->options WHERE `option_value` = %d AND `option_name` LIKE %s ";
		$wpdb->query( 
			$wpdb->prepare($query , $wp_post_id , 'CopifyJobIdExists%')
		);
	}
	
	
	/**
	 * Returns an array of all Copify job ID's already saved as a post
	 *
	 * @return array $ids An array of Copify IDs which are saved in wordpress db
	 * @author Rob Mcvey
	 **/
	public function CopifyGetCopifyPostIds() {
		global $wpdb;
		$query = "SELECT REPLACE(`option_name` , 'CopifyJobIdExists' , '') as `option_name` FROM $wpdb->options WHERE `option_name` LIKE %s ";
		return $wpdb->get_col( 
			$wpdb->prepare($query , 'CopifyJobIdExists%')
		);
	}
	
	
	/**
	 * Remove any bits and bobs we've cached
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyClearCache() {
		delete_transient('CopifyBudgets');
		delete_transient('CopifyCategories');
		delete_transient('CopifyStatuses');
	}
	
	
	/**
	 * Checks if a Copify ID is already in database, returns the WP id of the post
	 *
	 * @return int The Wordpress post ID of our Copify job (or false if not present)
	 * @param int The Copify job ID
	 * @author Rob Mcvey
	 **/
	public function CopifyJobIdExists($job_id = null) {
		return get_option('CopifyJobIdExists'.$job_id , false);
	}
	
	
	/**
	 * Get job categories from API or cache
	 *
	 * @return array An array of Copify categories from either API or cache
	 * @author Rob Mcvey
	 **/
	public function CopifyGetJobCategories() {
		$CopifyCategories = get_transient('CopifyCategories');
		if($CopifyCategories !== false) {
			return $CopifyCategories;
		}
		$CopifyCategories = $this->Copify->jobCategories();
		set_transient('CopifyCategories' , $CopifyCategories['job_categories'], 86400);
		return $CopifyCategories['job_categories'];		
	}
	
	
	/**
	 * Get job budgets from API or cache
	 *
	 * @return array An array of Copify budgets (standard or pro) from either API or cache
	 * @author Rob Mcvey
	 **/
	public function CopifyGetJobBudgets() {
		$CopifyBudgets = get_transient('CopifyBudgets');
		if($CopifyBudgets !== false) {
			return $CopifyBudgets;
		}
		$CopifyBudgets = $this->Copify->jobBudgets();
		set_transient('CopifyBudgets' , $CopifyBudgets['job_budgets'], 86400);
		return $CopifyBudgets['job_budgets'];
	}
	
	
	/**
	 * Get job statuses from API or cache
	 *
	 * @return array An array of Copify job statuses (open, in progress, complete etc.) from either API or cache
	 * @author Rob Mcvey
	 **/
	public function CopifyGetJobStatuses() {
		$CopifyStatuses = get_transient('CopifyStatuses');
		if($CopifyStatuses !== false) {
			return $CopifyStatuses;
		}
		$CopifyStatuses = $this->Copify->jobStatuses();
		set_transient('CopifyStatuses' , $CopifyStatuses['job_statuses'], 86400);
		return $CopifyStatuses['job_statuses'];
	}
	
	
	/**
	 * Get a user profile from API or cache
	 *
	 * @return array An array of a Copify writers public details from API or cache
	 * @author Rob Mcvey
	 **/
	public function CopifyGetUserProfile($id = 1) {
		$CopifyGetUserProfile = get_transient('CopifyGetUserProfile'.$id);
		if($CopifyGetUserProfile !== false) {
			return $CopifyGetUserProfile;
		}
		$CopifyGetUserProfile = $this->Copify->usersView($id);
		set_transient('CopifyGetUserProfile'.$id , $CopifyGetUserProfile, 604800);
		return $CopifyGetUserProfile;
	}
	
	
	/**
	 * Takes multi-dimensional array and returns a plain array with ID's as key
	 *
	 * @return array $flattened An array in the format array('id' => 1 , 'name' => 'foo' , 'id' => 2 , 'name' => 'bar')
	 * @param array $multiArray A multi-dimensional array of stuff with 'id' and 'name' keys
	 * @author Rob Mcvey
	 **/
	public function CopifyFlatten($multiArray = array()) {
		$flattened = array();
		if(is_array($multiArray) && !empty($multiArray)) {
			foreach($multiArray as $k => $inner) {
				if(array_key_exists('id',$inner) && array_key_exists('name',$inner)) {
					$flattened[$inner['id']] = $inner['name'];
				}				
			}
			return $flattened;
		} else {
			throw new Exception('Expects array');
		}
	}
	
	
	/**
	 * Format the brief with new lines -> <br>
	 *
	 * @return string The formatted brief
	 * @param str The unformatted breif
	 * @author Rob Mcvey
	 **/
	public function CopifyFormatBrief($str) {
		return nl2br($str);
	}
	
	
	/**
	 * Format the copy with new lines -> <br>
	 *
	 * @return string The formatted copy
	 * @param string The unformatted copy
	 * @author Rob Mcvey
	 **/
	public function CopifyFormatCopy($str) {
		return nl2br($str);
	}
	
	
	/**
	 * Admin menu hooks, at out links to the Wordpress menu
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function CopifyAdminMenu() {
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position )
		$icon = plugin_dir_url(null).$this->copifyDirName.DS.'img'.DS.'icon16.png';
		add_menu_page('Copify Wordpress Plugin', 'Copify', 'publish_posts', 'CopifyDashboard', array($this, 'CopifyDashboard'), $icon , 6); 
		
		//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page('CopifyDashboard', 'Copify Order New Content', 'Order Content', 'publish_posts', 'CopifyOrder', array($this, 'CopifyOrder'));
		add_submenu_page('CopifyDashboard', 'Copify Wordpress Settings', 'Settings', 'publish_posts', 'CopifySettings', array($this, 'CopifySettings'));
		add_submenu_page('CopifySettings', 'Copify View Job', 'View', 'publish_posts', 'CopifyViewJob', array($this, 'CopifyViewJob'));
	}
	

}

// Initialise the Copify Wordpress class
$CopifyWordpress = new CopifyWordpress();

// Add our js and css
add_action('admin_init', array($CopifyWordpress, 'CopifyCssAndScripts'));

// Add our admin menu 
add_action('admin_menu', array($CopifyWordpress, 'CopifyAdminMenu'));

// When a post is deleted, remove the flag in options so we can re-add to drafts if needed
add_action('before_delete_post', array($CopifyWordpress, 'CopifyBeforeDeletePost'));

// Ajax action for feedback and draft (for jobs complete)
add_action('wp_ajax_CopifyPostFeedback', array($CopifyWordpress, 'CopifyPostFeedback'));

// Ajax action for moving an already approved job to drafts
add_action('wp_ajax_CopifyMoveToDrafts', array($CopifyWordpress, 'CopifyMoveToDrafts'));

// Ajax method to get a quote for words
add_action('wp_ajax_CopifyQuoteWords', array($CopifyWordpress, 'CopifyQuoteWords'));

// Ajax method to post a new job
add_action('wp_ajax_CopifyAjaxOrder', array($CopifyWordpress, 'CopifyAjaxOrder'));