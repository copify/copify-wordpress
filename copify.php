<?php
/*
Plugin Name: Copify
Plugin URI: https://github.com/copify/copify-wordpress
Description: Order quality blog posts from Copify's network of professional writers
Version: 1.0.4
Author: Rob McVey
Author URI: http://uk.copify.com/
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
require_once('basics.php');
require_once(COPIFY_PATH . COPIFY_DS . 'Lib/Api.php');
require_once(COPIFY_PATH . COPIFY_DS . 'Lib/CopifyWordpress.php');

// Initialise the Copify Wordpress class
$CopifyWordpress = new CopifyWordpress();

// Add our js and css
add_action('admin_init', array($CopifyWordpress, 'CopifyCssAndScripts'));

// Run requests through our custom method
add_action('parse_request', array($CopifyWordpress, 'CopifyRequestFilter'));

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
