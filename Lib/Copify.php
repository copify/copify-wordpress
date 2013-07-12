<?php
// 
//  Copify.php
//  php
//  
//  Created by Rob Mcvey on 2012-05-11.
//  Copyright 2012 Rob Mcvey. All rights reserved.
// 
class Copify {
	
/**
 * The API URL
 */
	public $basePath = 'https://sandbox.copify.com/api';
		
/**
 * The API version
 */
	public $apiVersion = 'v1';
		
/**
 * live / sandbox
 */
	public $mode = 'live';
	
/**
 * Which country API to use. uk or us
 */
	public $country = 'uk';
	
/**
 * Resource path e.g. jobs
 */
	public $resource;	

/**
 * Accept format
 */
	public $format = 'json';	

/**
 * The full URL to the resource e.g. https://www.copify.com/jobs/123.json
 */
	public $fullUrl;	

/**
 * URL parameters to append
 */
	public $params = '';	

/**
 * The HTTP method to use
 */
	public $httpMethod = 'GET';	

/**
 * An array of headers to use during the HTTP request
 */
	public $headers = array();
	
/**
 * Start up
 *
 * @return void
 * @author Rob Mcvey
 * @param string $apiEmail 
 * @param string $apiKey 
 **/
	public function __construct($apiEmail = null , $apiKey = null) {
		// If we are not on sandbox, set the country sub domain
		if($this->mode == 'live') {
			$this->basePath = sprintf('https://%s.copify.com/api' , $this->country);
		} 

		if(empty($apiKey) || empty($apiEmail)) {
			throw new InvalidArgumentException('Please set the values for your API key and your email e.g: <pre>$Copify = new Copify("API KEY" , "API EMAIL");</pre>');
		} 

		// Set the default headers for Authorization and User-Agent
		$this->headers['Authorization'] = 'Bearer ' . $apiKey;
		$this->headers['User-Agent'] = $apiEmail;
		
	}
		
/**
 * Sets the full URL
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function setfullUrl() {
		$this->fullUrl = $this->basePath.'/'.$this->apiVersion.'/'.$this->resource.'.'.$this->format.$this->params;
	}
		
/**
 * Get a list of job records (20 per page)
 *
 * @return array
 * @author Rob Mcvey
 * @param bool $public Whether or not to get all public jobs too
 * @param int $page Which page
 * @param string $sort Sort by a field
 * @param string $directionsort Sort by direction, asc (ascending) or desc (descending)
 **/
	public function jobsIndex($public = false , $page = 1, $sort = 'id' , $direction = 'asc') {
		if($public) {
			$this->params = '?public=true';
		}
		$this->resource = "jobs/page:$page/sort:$sort/direction:$direction";
		return $this->makeRequest();
	}

/**
 * View a single job record
 *
 * @return array
 * @author Rob Mcvey
 * @param int $id 
 **/
	public function jobsView($id = null) {
		if(!$id || !is_numeric($id)) {
			throw new InvalidArgumentException('Invalid job ID');
		}
		$this->resource = 'jobs/'.$id;
		return $this->makeRequest();
	}
	
/**
 * Create a new job through the API
 *
 * @return mixed array of the new job record
 * @author Rob Mcvey
 * @param array $data array of new job fields...name, words, brief, category etc.
 **/
	public function jobsAdd($data = null) {
		if(empty($data)) {
			throw new InvalidArgumentException('You must pass an array of job details');
		}
		$this->httpMethod = 'POST';
		$this->resource = 'jobs';
		return $this->makeRequest($data);
	}	

/**
 * Post feedback for a completed job
 *
 * @return void
 * @author Rob Mcvey
 **/
	public function jobFeedback($feedback = null) {
		if(empty($feedback)) {
			throw new InvalidArgumentException('You must pass an array of feedback details');
		}
		$this->httpMethod = 'POST';
		$this->resource = 'feedback';
		return $this->makeRequest($feedback);
	}	

/**
 * Get an array of availble job categories
 *
 * @return array
 * @author Rob Mcvey
 **/
	public function jobCategories() {
		$this->resource = 'job_categories';
		return $this->makeRequest();
	}
		
/**
 * Get an array of availble job budgets/pricing
 *
 * @return array
 * @author Rob Mcvey
 **/
	public function jobBudgets() {
		$this->resource = 'job_budgets';
		return $this->makeRequest();
	}	

/**
 * View a single job budget
 *
 * @return array
 * @author Rob Mcvey
 * @param int $id of the job budget to fetch
 * @param int $words Optional, pass a value of words for a quote
 **/
	public function jobBudgetsView($id = null, $words = null) {
		if(!$id) {
			throw new InvalidArgumentException('You must pass an a budget ID to this method');
		}
		$this->resource = "job_budgets/".$id;
		if($words) {
			$this->params = "?words=$words";
		}
		return $this->makeRequest();
	}	

/**
 * Get an array of availble job budgets/pricing
 *
 * @return array
 * @author Rob Mcvey
 **/
	public function jobStatuses() {
		$this->resource = 'job_statuses';
		return $this->makeRequest();
	}	

/**
 * Get an array of availble job types
 *
 * @return array
 * @author Rob Mcvey
 **/
	public function jobTypes() {
		$this->resource = 'job_types';
		return $this->makeRequest();
	}
	
/**
 * Get a list of copywriters (20 per page)
 *
 * @return array
 * @author Rob Mcvey
 * @param int $page Which page
 * @param string $sort Sort by a field
 * @param string $directionsort Sort by direction, asc (ascending) or desc (descending)
 **/
	public function usersIndex($page = 1, $sort = 'id' , $direction = 'asc') {
		$this->resource = "users/page:$page/sort:$sort/direction:$direction";
		return $this->makeRequest();
	}
	
/**
 * View a single copywriter record
 *
 * @return array
 * @author Rob Mcvey
 * @param int $id 
 **/
	public function usersView($id =null) {
		if(!$id || !is_numeric($id)) {
			throw new InvalidArgumentException('Invalid copywriter ID');
		}
		$this->resource = 'users/'.$id;
		return $this->makeRequest();
	}	

/**
 * Makes a request and returns the result as an array
 *
 * @return mixed
 * @author Rob Mcvey
 * @param mixed $data the body of the request (usually JSON)
 **/
	public function makeRequest($data = null) {
		
		if(!function_exists('curl_init')) {
			throw new Exception('This Plugin requires cURL');
		}
		
		// Build the URL
		$this->setfullUrl();
		
		// create a curly wurly resource
		$curlyWurly = curl_init($this->fullUrl);
		
		// Return the response don't just splurt it out on screen
		curl_setopt($curlyWurly , CURLOPT_RETURNTRANSFER, true);
				
		// Return the headers from Copify
		curl_setopt($curlyWurly , CURLOPT_HEADER, false);
		
		// Don't hang around forever
		curl_setopt($curlyWurly , CURLOPT_TIMEOUT, 20);
		
		// Set the HTTP method (make uppercase just in case)
		$method = strtoupper($this->httpMethod);
		curl_setopt($curlyWurly , CURLOPT_CUSTOMREQUEST, $method);

		// Set the POST data if we are posting or putting
		if($method == 'POST' || $method == 'PUT') {
			curl_setopt($curlyWurly, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		
		// Set the Authorization and User-Agent headers
		foreach($this->headers as $key => $value) {
			$headers[] = $key.': '.$value;
		}
		curl_setopt($curlyWurly , CURLOPT_HTTPHEADER, $headers);

		// Some users have issues with CA certs so encrypt but dont auth
		curl_setopt($curlyWurly, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlyWurly, CURLOPT_SSL_VERIFYHOST, false);
		
		if(!$response = curl_exec($curlyWurly)) {
			throw new Exception(curl_error($curlyWurly));
		} 
				
		// Bye bye curly wurly
		curl_close($curlyWurly);

		return $this->parseFormat($response);
	}
		
/**
 * Takes the raw response, returns an array or throws a huma readble exception
 *
 * @return array
 * @author Rob Mcvey
 * @param mixed $raw
 **/
	public function parseFormat($raw) {
		if($this->format == 'json') {
			$result = json_decode($raw , true);
			if($result['status'] == 'success') {
				return $result['response'];
			} else {
				throw new InvalidArgumentException($result['message']);
			}
		} else {
			throw new InvalidArgumentException('Only JSON is supported in this version of the SDK');
		}
	}

}
