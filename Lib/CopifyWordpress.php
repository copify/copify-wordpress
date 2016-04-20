<?php
//
//  CopifyWordpress.php
//  copify-wordpress
//
//  Created by Rob Mcvey on 2014-06-17.
//  Copyright 2014 Rob McVey. All rights reserved.
//
class CopifyWordpress {

/**
 * Plugin version
 */
	protected $version = '1.2.0';

/**
 * Instance of Copify library
 */
	public $Api = null;

/**
 * Plugin dir name
 */
	public $copifyDirName = 'copify';

/**
 * Add our CSS and JS to wordpress
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyCssAndScripts() {
		// JS - our own
		$js_url = $this->wordpress('plugins_url', $this->copifyDirName . COPIFY_DS . 'js' . COPIFY_DS . 'Copify.js');
		$this->wordpress('wp_enqueue_script', 'copify', $js_url, array('jquery'));
		// JS - bootsrap modal
		$bootstrap_url = $this->wordpress('plugins_url', $this->copifyDirName . COPIFY_DS . 'js' . COPIFY_DS . 'bootstrap-modal.js');
		$this->wordpress('wp_enqueue_script', 'bootstrap-modal' , $bootstrap_url, array('jquery'));
		// JS - jquery validate
		$jquery_validate = $this->wordpress('plugins_url', $this->copifyDirName . COPIFY_DS . 'js' . COPIFY_DS . 'jquery.validate.js');
		$this->wordpress('wp_enqueue_script', 'jquery.validate' , $jquery_validate, array('jquery'));
		// CSS
		$css_url = $this->wordpress('plugins_url', $this->copifyDirName . COPIFY_DS . 'css' . COPIFY_DS . 'Copify.css');
		$this->wordpress('wp_enqueue_style', 'copify', $css_url);
	}

/**
 * Settings page
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifySettings() {

		try {
            // All available locales
			$CopifyAvailableLocales = array(
				'uk' => 'UK',
				'us' => 'USA',
				'au' => 'Australia',
			);
            $wp_users = $this->wordpress('get_users', []);
			// Get API credentials
			$CopifyLoginDetails = $this->wordpress('get_option', 'CopifyLoginDetails' , false);
			// API crendtials form submitted
			if (!empty($_POST) && isset($_POST['CopifyEmail'])) {
				// The form input
				$CopifyEmail = $_POST['CopifyEmail'];
				$CopifyApiKey = $_POST['CopifyApiKey'];
				$CopifyLocale = $_POST['CopifyLocale'];
                $CopifyWPUser = (isset($_POST['CopifyWPUser'])) ? $_POST['CopifyWPUser'] : '';
				// Array to save
				$toSave = array(
					'CopifyEmail' => $CopifyEmail,
					'CopifyApiKey' => $CopifyApiKey,
					'CopifyLocale' => $CopifyLocale,
                    'CopifyWPUser' => $CopifyWPUser
				);
				// Update or Add?
				if ($CopifyLoginDetails) {
					$this->wordpress('update_option', 'CopifyLoginDetails', $toSave);
				} else {
					$this->wordpress('add_option', 'CopifyLoginDetails', $toSave, null, 'no');
				}
				// Clear any options cached (maybe they switched locales? So this is impoartant)
				$this->CopifyClearCache();
				$success = "Settings updated!";
			}
			// form not submitted but we have details already
			elseif ($CopifyLoginDetails) {
				$CopifyEmail = $CopifyLoginDetails['CopifyEmail'];
				$CopifyApiKey = $CopifyLoginDetails['CopifyApiKey'];
				$CopifyLocale = $CopifyLoginDetails['CopifyLocale'];
                $CopifyWPUser = (isset($CopifyLoginDetails['CopifyWPUser'])) ? $CopifyLoginDetails['CopifyWPUser'] : '';
			} else {
				$CopifyEmail = '';
				$CopifyApiKey = '';
                $CopifyLocale = '';
                $CopifyWPUser = '';
			}
			// Flash message of some kind?
			if (isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
		include_once(COPIFY_VIEWS . 'CopifySettings.php');
	}

/**
 * Initialise the Copify API class with the credentials from the options table
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifySetApiClass() {
		$CopifyLoginDetails = $this->wordpress('get_option', 'CopifyLoginDetails' , false);
		// Check login stored
		if (!$CopifyLoginDetails) {
			$this->wordpress('wp_die', '<pre>To connect to Copify you must enter your API key on the <a href="admin.php?page=CopifySettings">Settings page</a></pre>');
		}
		// Requires cURL
		if (!function_exists('curl_init')) {
			$this->wordpress('wp_die' , '<pre>This Plugin requires cURL to be installed</pre>');
		}
		// Initialise the Copify API helper class
		if (!$this->Api) {
			$this->Api = new CopifyApi($CopifyLoginDetails['CopifyEmail'], $CopifyLoginDetails['CopifyApiKey']);
		}
		// Set the correct end point for the API
		if (defined('COPIFY_DEVMODE') && COPIFY_DEVMODE == true) {
			$this->Api->basePath = COPIFY_DEV_URL;
		} else {
			$this->Api->basePath = sprintf('https://%s.copify.com/api', $CopifyLoginDetails['CopifyLocale']);
		}
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
			if (isset($_GET['page']) && !empty($_GET['page'])) {
				$page = $_GET['page'];
			}
			// Page params?
			$pageNumber = 1;
			if (isset($_GET['pageNumber']) && !empty($_GET['pageNumber'])) {
				$pageNumber = $_GET['pageNumber'];
			}
			// Sort params?
			$sort = 'id';
			if (isset($_GET['sort']) && !empty($_GET['sort'])) {
				$sort = $_GET['sort'];
			}
			// Direction params?
			$direction = 'desc';
			if (isset($_GET['direction']) && !empty($_GET['direction'])) {
				$direction = $_GET['direction'];
			}
			// Flash message of some kind?
			if (isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}
			// Get the jobs resource from API
			$CopifyJobs = $this->Api->jobsIndex(false , $pageNumber , $sort , $direction);
			// Get the total amount of jobs
			$total = $CopifyJobs['total'];
			// Current page
			$paginateNumber = $CopifyJobs['page'];
			// Total Pages
			$totalPages = ceil($total / 20);
			// Prev page
			$prevPage = 1;
			if ($paginateNumber > 1) {
				$prevPage = $paginateNumber - 1;
			}
			// Next page
			$nextPage = $totalPages;
			if ($paginateNumber < $totalPages) {
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
		catch (Exception $e) {
			$error = $e->getMessage();
			// Bad API creectials?
			if (preg_match('/user-agent/i' , $error)) {
				$error .= '. <a href="?page=CopifySettings" >Check settings</a>';
			}
		}
		include_once(COPIFY_VIEWS . 'CopifyDashboard.php');
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
		catch (Exception $e) {
			$error = $e->getMessage();
			// Is this a balance exception? Link to "add more funds"
			if (preg_match('/funds/i' , $error)) {
				$CopifyLoginDetails = $this->wordpress('get_option', 'CopifyLoginDetails' , false);
				$error .= sprintf('. <a href="https://%s.copify.com/payments/add" target="blank" >Make a payment</a>' , $CopifyLoginDetails['CopifyLocale']);
			}
			// Bad API creectials?
			if (preg_match('/user-agent/i' , $error)) {
				$error .= '. <a href="?page=CopifySettings" >Check settings</a>';
			}
		}
		include_once(COPIFY_VIEWS . 'CopifyOrder.php');
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
			if (empty($_POST)) {
				throw new Exception('POST request required');
			}
			// Initialise Copify API class
			$this->CopifySetApiClass();
			// Parse the post args
			parse_str($_POST['job'], $newJob);
			// Add the url as reference
			if (isset($_SERVER['HTTP_HOST'])) {
				$newJob['brief'] .= "\n\nThis blog will be posted on " . $_SERVER['HTTP_HOST'];
			}
			// parse_str will add slashes if magic quotes on
			$newJob = array_map("stripslashes", $newJob);
			$response['response'] = $this->Api->jobsAdd($newJob);
			$response['message'] = 'New job added';
			$response['status'] = 'success';
			return $this->outputJson($response);
		}
		catch (Exception $e) {
			$response['message'] = $e->getMessage();
			return $this->outputJson($response);
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
			$job = $this->Api->jobsView($jobId); // Maybe cache this if already approved?
			// Get category, budget and status resources from API
			$CopifyCategories = $this->CopifyGetJobCategories();
			$CopifyBudgets = $this->CopifyGetJobBudgets();
			$CopifyStatuses = $this->CopifyGetJobStatuses();
			// Create a plain array of categories with the ID as the key
			$categoryList = $this->CopifyFlatten($CopifyCategories);
			$budgetList = $this->CopifyFlatten($CopifyBudgets);
			$statusList = $this->CopifyFlatten($CopifyStatuses);
			// Get the writer profile if we have one...
			if ($job['job_status_id'] == 3 && !empty($job['writer'])) {
				try {
					$CopifyWriter = $this->CopifyGetUserProfile($job['writer']);
				} catch (Exception $e) {
					if (preg_match("/publically/i", $e->getMessage())) {
						$CopifyWriter = $this->CopifyGetUserProfile(2);
					}
				}
			}
			// Set flag if this job is already in Wordpress as a post
			$CopifyJobIsPostAlready = $this->CopifyJobIdExists($job['id']);
			// Flash message of some kind?
			if (isset($_GET['flashMessage']) && !empty($_GET['flashMessage'])) {
				$message = $_GET['flashMessage'];
			}
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
		include_once(COPIFY_VIEWS . 'CopifyViewJob.php');
	}

/**
 * Handle ajax feedback form post. The user is approving a job and leaving feedback
 * Outputs a JSON response.
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
			// Can only post to this method
			if (empty($_POST)) {
				throw new Exception('POST request required');
			}
			// Initialise Copify API class
			$this->CopifySetApiClass();
			// Get the post data
			$feedback = $_POST;
			// Get the type (post||page)
			if (isset($feedback['type'])) {
				$post_type = $feedback['type'];
			} else {
				$post_type = 'post';
			}
			// Get the job record from API
			$job = $this->Api->jobsView($feedback['job_id']);
			// Check it is not already in the database, if not pop it in
			if (!$this->CopifyJobIdExists($feedback['job_id'])) {
				// If we have a title, and it's not the same as the order name (suggests blog package) we prepend the copy
				$finishedCopy = '';
				if (isset($job['title']) && !empty($job['title']) && $job['title'] != $job['name']) {
					$finishedCopy .= $job['title'] . "\n\n";
				}
				$finishedCopy .= $job['copy'];
				$newPost = array(
					'post_title' => $job['name'],
					'post_content' => $finishedCopy,
					'post_status' => 'draft',
					'post_type' => $post_type  // [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ] //You may
				);
				// Insert the post
				$wp_post_id = $this->CopifyAddToPosts($feedback['job_id'], $newPost);
				// Do we have an image selected?
				if (isset($feedback['image']) && isset($feedback['image_licence'])) {
					$meta = array('image_licence' => $feedback['image_licence']);
					$this->CopifySetPostThumbnailFromUrl($wp_post_id, $feedback['image'], $meta);
				}
			}
			// Remove the unwanted keys as we cant post these to API without an error
			unset($feedback['action']);
			unset($feedback['type']);
			unset($feedback['image_licence']);
			unset($feedback['image']);
			// Submit feedback via API
			$result = $this->Api->jobFeedback($feedback);
			// Build the success response
			$response['status'] = 'success';
			$response['response'] = $result;
			$response['message'] = 'Job Approved';
			return $this->outputJson($response);
		}
		catch (Exception $e) {
			$response['message'] = $e->getMessage();
			return $this->outputJson($response);
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
			if (empty($_POST)) {
				throw new Exception('POST request required');
			}
			// Initialise Copify API class
			$this->CopifySetApiClass();
			// Get the job id from the post data
			$job_id = $_POST['job_id'];
			$post_type = $_POST['post_type'];
			if (!in_array($post_type, array('post', 'page'))) {
				throw new Exception('Post type must be either post or page');
			}
			// Get the job record from API
			$job = $this->Api->jobsView($job_id);
			// Check it is not already in the database, if not pop it in
			if (!$this->CopifyJobIdExists($job_id)) {
				$newPost = array(
					'post_title' => $job['name'],
					'post_content' => $job['copy'],
					'post_status' => 'draft',
					'post_type' => $post_type,
				);
				$this->CopifyAddToPosts($job_id, $newPost);
			}
			// Build the success response
			$response['status'] = 'success';
			$response['response'] = true;
			$response['message'] = 'Order moved to drafts';
			return $this->outputJson($response);
		}
		catch (Exception $e) {
			$response['message'] = $e->getMessage();
			return $this->outputJson($response);
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
			if (empty($_POST)) {
				throw new Exception('POST request required');
			}
			// Get the thingy
			$job_budget_id = $_POST['job_budget_id'];
			$words = $_POST['words'];
			// Initialise Copify API class
			$this->CopifySetApiClass();
			// Fetch a quote via API
			$result = $this->Api->jobBudgetsView($job_budget_id, $words);
			// Build the success response
			$response['status'] = 'success';
			$response['response'] = $result;
			$response['message'] = sprintf('Quote for %s', $words);
			return $this->outputJson($response);
		}
		catch (Exception $e) {
			$response['message'] = $e->getMessage();
			return $this->outputJson($response);
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
	public function CopifyAddToPosts($job_id, $newPost) {
		// Create the post
		$new_wp_id = $this->wordpress('wp_insert_post', $newPost, true);
		// Check for errors
		if ($this->wordpress('is_wp_error', $new_wp_id)) {
			$errorMessage = $new_wp_id->get_error_message();
			throw new Exception($errorMessage);
		}
		// Pop in an option for the Copify Job ID
		$this->wordpress('add_option', 'CopifyJobIdExists' . $job_id, $new_wp_id, null, 'no');
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
			$wpdb->prepare($query, $wp_post_id, 'CopifyJobIdExists%')
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
			$wpdb->prepare($query, 'CopifyJobIdExists%')
		);
	}

/**
 * Remove any bits and bobs we've cached
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyClearCache() {
		$this->wordpress('delete_transient', 'CopifyBudgets');
		$this->wordpress('delete_transient', 'CopifyCategories');
		$this->wordpress('delete_transient', 'CopifyStatuses');
	}

/**
 * Checks if a Copify ID is already in database, returns the WP id of the post
 *
 * @return int The Wordpress post ID of our Copify job (or false if not present)
 * @param int The Copify job ID
 * @author Rob Mcvey
 **/
	public function CopifyJobIdExists($job_id = null) {
		return $this->wordpress('get_option', 'CopifyJobIdExists' . $job_id , false);
	}

/**
 * Get job categories from API or cache
 *
 * @return array An array of Copify categories from either API or cache
 * @author Rob Mcvey
 **/
	public function CopifyGetJobCategories() {
		$CopifyCategories = get_transient('CopifyCategories');
		if ($CopifyCategories !== false) {
			return $CopifyCategories;
		}
		$CopifyCategories = $this->Api->jobCategories();
		$this->wordpress('set_transient', 'CopifyCategories', $CopifyCategories['job_categories'], 86400);
		return $CopifyCategories['job_categories'];
	}

/**
 * Get job budgets from API or cache
 *
 * @return array An array of Copify budgets (standard or pro) from either API or cache
 * @author Rob Mcvey
 **/
	public function CopifyGetJobBudgets() {
		$CopifyBudgets = $this->wordpress('get_transient', 'CopifyBudgets');
		if ($CopifyBudgets !== false) {
			return $CopifyBudgets;
		}
		$CopifyBudgets = $this->Api->jobBudgets();
		$this->wordpress('set_transient', 'CopifyBudgets', $CopifyBudgets['job_budgets'], 86400);
		return $CopifyBudgets['job_budgets'];
	}

/**
 * Get job statuses from API or cache
 *
 * @return array An array of Copify job statuses (open, in progress, complete etc.) from either API or cache
 * @author Rob Mcvey
 **/
	public function CopifyGetJobStatuses() {
		$CopifyStatuses = $this->wordpress('get_transient', 'CopifyStatuses');
		if ($CopifyStatuses !== false) {
			return $CopifyStatuses;
		}
		$CopifyStatuses = $this->Api->jobStatuses();
		$this->wordpress('set_transient', 'CopifyStatuses', $CopifyStatuses['job_statuses'], 86400);
		return $CopifyStatuses['job_statuses'];
	}

/**
 * Get a user profile from API or cache
 *
 * @return array An array of a Copify writers public details from API or cache
 * @author Rob Mcvey
 **/
	public function CopifyGetUserProfile($id = 1) {
		$CopifyGetUserProfile = $this->wordpress('get_transient', 'CopifyGetUserProfile'.$id);
		if ($CopifyGetUserProfile !== false) {
			return $CopifyGetUserProfile;
		}
		$CopifyGetUserProfile = $this->Api->usersView($id);
		$this->wordpress('set_transient', 'CopifyGetUserProfile' . $id, $CopifyGetUserProfile, 604800);
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
		if (is_array($multiArray) && !empty($multiArray)) {
			foreach ($multiArray as $k => $inner) {
				if (array_key_exists('id', $inner) && array_key_exists('name', $inner)) {
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
		return nl2br($this->wordpress('esc_attr', $str));
	}

/**
 * Format the copy with new lines -> <br>
 *
 * @return string The formatted copy
 * @param string The unformatted copy
 * @author Rob Mcvey
 **/
	public function CopifyFormatCopy($str) {
		return nl2br($this->wordpress('esc_attr', $str));
	}

/**
 * Admin menu hooks, at out links to the Wordpress menu
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyAdminMenu() {
		// Add main menu and icon
		$this->wordpress('add_menu_page', 'Copify Wordpress Plugin', 'Copify', 'publish_posts', 'CopifyDashboard', array($this, 'CopifyDashboard'), 'dashicons-edit' , 6);
		// Add sub menus
		$this->wordpress('add_submenu_page', 'CopifyDashboard', 'Copify Order New Content', 'Order blog post', 'publish_posts', 'CopifyOrder', array($this, 'CopifyOrder'));
		$this->wordpress('add_submenu_page', 'CopifyDashboard', 'Copify Wordpress Settings', 'Settings', 'publish_posts', 'CopifySettings', array($this, 'CopifySettings'));
		$this->wordpress('add_submenu_page', 'CopifySettings', 'Copify View Job', 'View', 'publish_posts', 'CopifyViewJob', array($this, 'CopifyViewJob'));
	}

/**
 * We can modifiy the content of the post here. When a post is autopublished, with an image, we require
 * image attribution.
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyAddFlickrAttribution($content) {
		// Get the thumbnail meta, and check for custom copify attributes
		$featured_image_meta = $this->_wp_get_attachment_metadata();
		if (empty($featured_image_meta)) {
			return $content;
		}
		if (isset($featured_image_meta['image_licence']) && !empty($featured_image_meta['image_licence'])) {
			$attribution = '<div style="display:block;font-size:9px;">Photo: ';
			$attribution .= $featured_image_meta['image_licence'];
			$attribution .= '</div>';
			$content .= $attribution;
			return $content;
		}
		if (!isset($featured_image_meta['copify_attr_url'])) {
			return $content;
		}
		$attribution = '<div style="display:block;font-size:9px;">Photo: ';
		// Check for title
		if (isset($featured_image_meta['copify_attr_photo_title'])) {
			$title = $featured_image_meta['copify_attr_photo_title'];
		} else {
			$title = 'Creative Commons';
		}
		$attribution .= sprintf('<a target="blank" title="%s" href="%s" rel="nofollow">', $title, $featured_image_meta['copify_attr_url']);
		$attribution .= $title;
		$attribution .= '</a>';
		// Username
		if (isset($featured_image_meta['copify_attr_user']) && isset($featured_image_meta['copify_attr_user_url'])) {
			$attribution .= sprintf(' by <a href="%s" target="blank" title="%s" rel="nofollow">%s</a>',
				$featured_image_meta['copify_attr_user_url'],
				$featured_image_meta['copify_attr_user'],
				$featured_image_meta['copify_attr_user']
			);
		}
		// Licence
		if (isset($featured_image_meta['copify_attr_cc_license']) && isset($featured_image_meta['copify_attr_cc_license_url'])) {
			$attribution .= sprintf(' licensed under <a href="%s" target="blank" rel="nofollow">Creative commons %s</a>',
				$featured_image_meta['copify_attr_cc_license_url'],
				$featured_image_meta['copify_attr_cc_license']
			);
		}
		$attribution .= '</div>';
		$content .= $attribution;
		return $content;
	}

/**
 * We can modify the HTML of the featued image here
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyThumbnailHtml($html) {
		return $html;
	}

/**
 * We can check through all requests in this method for things
 * like autoapprove post backs.
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifyRequestFilter() {
		try {
			// NO request URI
			if (!isset($_GET["copify-action"])) {
				return;
			}
			// Token
			if (!isset($_GET["token"])) {
				throw new Exception('Must include auth token', 400);
			}
			$token = $_GET["token"];
			// Check valid login
			$CopifyLoginDetails = $this->wordpress('get_option', 'CopifyLoginDetails' , false);
			// Not valid account email
			if ($CopifyLoginDetails == false || !isset($CopifyLoginDetails['CopifyEmail']) || !isset($CopifyLoginDetails['CopifyApiKey'])) {
				throw new Exception('Copify plugin not conigured', 404);
			}
			// Copify will send a hash of email/api key
			$expectedToken = sha1($CopifyLoginDetails['CopifyEmail'] . $CopifyLoginDetails['CopifyApiKey']);
			if ($expectedToken != $token) {
				throw new Exception('Permission denied', 403);
			}
			// Version check only
			if (isset($_GET["check"]) && $_GET["check"] == 'version') {
				return $this->outputJson($this->version);
			}
			$this->handleRequestFilter();
		} catch (Exception $e) {
			$message = $e->getMessage();
			$code = $e->getCode();
			if ($code == 403) {
				$this->setheader("HTTP/1.0 403 Forbidden");
			}
			elseif ($code == 400) {
				$this->setheader("HTTP/1.0 400 Bad Request");
			}
			elseif ($code == 409) {
				$this->setheader("HTTP/1.0 409 Conflict");
			}
			else {
				$this->setheader("HTTP/1.0 404 Not Found");
			}
			$json = array('message' => $message);
			return $this->outputJson($json);
		}
	}

/**
 * Handle incoming copify request. Defaults to auto-publishing the ID in the get params
 * but can perform other actions like moving a post back to drafts, setting a featured image,
 * removing an image etc.
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function handleRequestFilter() {
		$action = $_GET["copify-action"];
		if ($action === "set-image") {
			$this->setImage();
		}
		elseif ($action === "delete-image") {
			$this->deleteImage();
		}
		elseif ($action === "unpublish-post") {
			$this->unpublishPost();
		}
		else {
			$this->autoPublishOrder();
		}
	}

/**
 * Parse photo attribution values from GET params
 *
 * @return array
 * @author Rob Mcvey
 **/
	protected function parseImageAttributionMeta() {
		if (empty($_GET)) {
			return;
		}
		$attributionValues = array();
		foreach ($_GET as $key => $value) {
			if (preg_match("/^copify_attr_/", $key)) {
				$attributionValues[$key] = $value;
			}
		}
		return $attributionValues;
	}

/**
 * Checks GET params for wp_post_id and the image url and sets featured image
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function setImage() {
		if (!isset($_GET['wp_post_id']) || !isset($_GET['image-url'])) {
			throw new Exception('Missing params wp_post_id and image-url', 400);
		}
		$meta = $this->parseImageAttributionMeta();
		$set_post_thumbnail = $this->CopifySetPostThumbnailFromUrl($_GET['wp_post_id'], $_GET['image-url'], $meta);
		$message = sprintf('Image for post %s set to %s', $_GET['wp_post_id'], $_GET['image-url']);
		$json = array('success' => true, 'message' => $message, 'set_post_thumbnail' => $set_post_thumbnail);
		return $this->outputJson($json);
	}

/**
 * Remove featured image from post
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function deleteImage() {
		if (!isset($_GET['wp_post_id'])) {
			throw new Exception('Missing params wp_post_id', 400);
		}
		$result = $this->wordpress('delete_post_thumbnail', $_GET['wp_post_id']);
		$message = sprintf('Image for post %s was removed', $_GET['wp_post_id']);
		$json = array('success' => true, 'message' => $message);
		return $this->outputJson($json);
	}

/**
 * Trash a post
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function unpublishPost() {
		if (!isset($_GET['wp_post_id'])) {
			throw new Exception('Missing params wp_post_id', 400);
		}
		$result = $this->wordpress('wp_trash_post', $_GET['wp_post_id']);
		if (!$result) {
			throw new Exception(sprintf('Failed to trash post %s', $_GET['wp_post_id']));
		}
		$this->CopifyBeforeDeletePost($_GET['wp_post_id']);
		$message = sprintf('Post %s moved to trash', $_GET['wp_post_id']);
		$json = array('success' => true, 'message' => $message);
		return $this->outputJson($json);
	}

/**
 * Fetch an order from Copify API and publish
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function autoPublishOrder() {
		// Order ID
		if (!isset($_GET["id"])) {
			throw new Exception('Must include order ID', 400);
		}
		// Get the ID from the get params
		$id = $_GET["id"];
		// Initialise Copify API class
		$this->CopifySetApiClass();
		// Check it's not already published
		if ($this->CopifyJobIdExists($id)) {
			throw new Exception(sprintf('Order %s already published', $id), 409);
		}
		// Get the job record from the API
		$job = $this->Api->jobsView($id);
		// Public orders won't have copy field
		if (!isset($job['copy']) || empty($job['copy'])) {
			throw new Exception(sprintf('Can not find copy for order %s', $id));
		}
		// Is order marked as complete?
		if (!in_array($job['job_status_id'], array(3, 4))) {
			throw new Exception(sprintf('Order %s is not yet complete or approved', $id));
		}
		$newPost = array(
			'post_title' => $job['name'],
			'post_content' => $job['copy'],
			'post_status' => 'publish',
			'post_type' => 'post' // [ 'post' | 'page' | 'link' | 'nav_menu_item' | 'custom_post_type' ]
		);
		// Do we have an a selected user to post under? OR an admin ID we can set as post author?
		$admins = $this->wordpress('get_users', 'role=administrator');
        // Get API credentials
        $CopifyLoginDetails = $this->wordpress('get_option', 'CopifyLoginDetails' , false);
        if (isset($CopifyLoginDetails['CopifyWPUser']) && is_numeric($CopifyLoginDetails['CopifyWPUser'])) {
            $newPost['post_author'] = $CopifyLoginDetails['CopifyWPUser'];
        } else if (isset($admins[0]) && is_object($admins[0]) && property_exists($admins[0], 'data') && property_exists($admins[0]->data, 'ID')) {
			$newPost['post_author'] = $admins[0]->data->ID;
		}
		$wp_post_id = $this->CopifyAddToPosts($id, $newPost);
		$message = sprintf('Order %s auto-published', $id);
		$json = array('success' => true, 'message' => $message, 'wp_post_id' => $wp_post_id);
		return $this->outputJson($json);
	}

/**
 * Set a posts thumbnail using set_post_thumbnail() from a remote image URL
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function CopifySetPostThumbnailFromUrl($post_id, $url, $meta = array()) {
		// Validate the host
		$this->CopifyCheckImageHost($url);
		// Validate the extension
		$filenameparts = explode('.', basename($url));
		$ext = strtolower(array_pop($filenameparts));
		$this->CopifyCheckThumbnailExtension($ext);
		// Get the uploads dir info
		$wp_upload_dir = $this->wordpress('wp_upload_dir', null);
		if (!empty($wp_upload_dir['error'])) {
			throw new Exception($wp_upload_dir['error']);
		}
		// Path to uploaded
		$path = $wp_upload_dir['path'];
		// Fetch our image
		$image = $this->_file_get_contents($url);
		if ($image == false) {
			throw new Exception(sprintf("Failed to fetch %s", $url));
		}
		// Create a unique filename
		$imageName = $this->unique() . '.' . $ext;
		// Upload the image to the uploads dir
		$wp_upload_bits = $this->wordpress('wp_upload_bits', $imageName, null, $image);
		if (!empty($wp_upload_bits['error'])) {
			throw new Exception($wp_upload_bits['error']);
		}
		// $filename should be the path to a file in the upload directory.
		$filepath = $wp_upload_bits['file'];
		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$filetype = $this->wordpress('wp_check_filetype', $filepath);
		// Prepare an array of post data for the attachment.
		$attachment = array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($filepath)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_excerpt' => 'Image from Flickr'
		);
		// Insert the attachment.
		$attach_id = $this->wordpress('wp_insert_attachment', $attachment, $filepath);
		if (!is_numeric($attach_id) || $attach_id == 0) {
			throw new Exception('Failed to create attachment');
		}
		// Set and update the meta for the image
		$this->setUpdateAttachmentMeta($attach_id, $filepath, $meta);
		// Set the thumbnail of our post
		$set_post_thumbnail = $this->wordpress('set_post_thumbnail', $post_id, $attach_id);
		if (!$set_post_thumbnail) {
			throw new Exception('Failed to set post image');
		}
		// Returns the thumbnail ID
		return $set_post_thumbnail;
	}

/**
 * Return a UUID
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function unique() {
		return uniqid();
	}

/**
 * Validate the mime of image url
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function CopifyCheckThumbnailExtension($ext) {
		if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'bmp'))) {
			throw new InvalidArgumentException('Bad image type');
		}
	}

/**
 * Calls wp_generate_attachment_metadata and wp_update_attachment_metadata for attachment
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function setUpdateAttachmentMeta($attach_id, $filepath, $meta = array()) {
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
		if (!is_array($attach_data) && !empty($meta)) {
			return;
		}
		$attach_data = array_merge($attach_data, $meta);
		wp_update_attachment_metadata($attach_id, $attach_data);
	}

/**
 * file_get_contents
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function _file_get_contents($path) {
		return file_get_contents($path);
	}

/**
 * Check an image URL is from a valid host
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function CopifyCheckImageHost($url) {
		$parts = parse_url($url);
		if (!preg_match("/flickr|copify/", $parts['host'])) {
			throw new InvalidArgumentException('Bad image host');
		}
	}

/**
 * Get attachment for current post, and return its meta data
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function _wp_get_attachment_metadata() {
		$attach_id = get_post_thumbnail_id();
		return wp_get_attachment_metadata($attach_id);
	}

/**
 * Set an output header
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function setheader($header) {
		header($header);
	}

/**
 * Spit out an array as JSON
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function outputJson($json) {
		echo json_encode($json);
		die();
	}

/**
 * Wordpress functions. Utility method to allow us to mock out the wordpress functions.
 *
 * @return void
 * @author Rob Mcvey
 **/
	protected function wordpress($method, $mixed = null) {
		$args = func_get_args();
		if (!isset($args[0])) {
			return false;
		}
		$method = array_shift($args);
		return call_user_func_array($method, $args);
	}

/**
 * Does what it says on the tin
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function getVersion() {
		return $this->version;
	}

}
