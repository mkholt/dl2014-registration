<?php
/**
 * Plugin Name: DL2014 Registrations
 * Version: 1.1
 * Author: Morten Holt (thawk@t-hawk.com)
 * License: GPL2
 */

 /*  Copyright 2013 Morten Holt (email : thawk@t-hawk.com)

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

require_once('config.php');
require_once('controller.php');

class Registration {
	protected $_pages = null;

	public function __construct()
	{
		add_filter( 'parse_query', array($this,'wp_query_checker') );
		add_filter( 'the_posts', array($this,'posts_filter') );

		if (is_admin()) {
			register_activation_hook(__FILE__, array($this, 'plugin_install'));
		}
	}

	public function plugin_install($upgrading = FALSE)
	{
		require_once(dirname(__FILE__).'/backend/app_install_remove.php');
		$ir = new AppInstallRemove();
		$ir->install();
	}

	protected function getPages()
	{
	  if (empty($this->_pages)) {		
		$pages = array();
		foreach (RegistrationConfig::$pages as $page) {
		  $pages[$page['page_name']] = $page;
		}

		$this->_pages = $pages;
	  }

	  return $this->_pages;
	}

	/**
	 * Checks the $wp_query object for a call to a controller
	 * @param object $q wp_query object
	 * @return object wp_query
	 */
	public function wp_query_checker($q) {
		
		if(! $q->is_main_query()) {
			return $q;
		}
		
		/**
		 * Used to prevent multiple runs of this controller (for example if a widget or shortcode calls it)
		 *
		 * It is set TRUE for a page request and set FALSE when the request is complete in the posts_filter()
		 * function
		 *
		 */
		global $registration_page_request_active;

		$pages = $this->getPages();

		if(isset($_SERVER['REQUEST_URI']))
		{
			/**
			 * If there is no trailing slash, add it.
			 */
			if(substr($_SERVER['REQUEST_URI'], -1) !== '/' ) {
				$_SERVER['REQUEST_URI'] .= '/';
			}
			
			if ((array_key_exists(($registration_request=ltrim($_SERVER['REQUEST_URI'],'/')), $pages)) ||
				(($_sl_pos = strpos($registration_request, '/')) && (array_key_exists(($this_controller_name = substr($registration_request, 0, $_sl_pos)), $pages))))
			{
				// lose any GET vars from the page request
				if(($_q = strpos($registration_request, '?')) !== FALSE ) {
					$registration_request = substr($registration_request, 0, $_q);
				}
				
				/**
				 * Using permalinks with or without the full page path
				 * i.e. sniffed from $_SERVER['REQUEST_URI']
				 */
				if(!isset( $this_controller_name)) {
					$this_controller_name = substr($registration_request, 0, strpos($registration_request, '/'));
				}
				
				if(!defined('REGISTRATION_PAGE_CONTROLLER_NAME') ) {
					define('REGISTRATION_PAGE_CONTROLLER_NAME',$this_controller_name);
				}
				
				$registration_page_request_active = TRUE;
				$registration_request = rtrim( $registration_request, '/' );
				$q->set('registration_request', $registration_request);

				return $q;	
			}
			
		}
		elseif ( ( array_key_exists( $q->query_vars['pagename'], $pages ) ) ||
			( ( $_sl_pos = strpos( $q->query_vars['pagename'], '/' ) ) && ( array_key_exists( ( $this_controller_name = substr( $q->query_vars['pagename'], 0, $_sl_pos ) ), $pages ) ) ) )
		{
			/**
			 * Using permalinks and $_SERVER['REQUEST_URI'] is not set
			 * i.e. sniffed from $q->query_vars['pagename']
			 */
			if(!isset($this_controller_name)) {
				$this_controller_name = $q->query_vars['pagename'];
			}
			
			define('REGISTRATION_PAGE_CONTROLLER_NAME',$this_controller_name);
			$registration_page_request_active = TRUE;
			
			$registration_request = $q->query_vars['pagename'];
			$registration_request = rtrim( $registration_request, '/' );
			$q->set('registration_request', $registration_request);

			return $q;
			
		}
		else {
			$q->set('registration_request', FALSE);
			
			return $q;
		}
		
	}

	/**
	 * Posts filter to detect a call to our controller and to pass control to it
	 *
	 * @param   array $posts A single element array of posts.
	 * @return  array $posts
	 */
	function posts_filter($posts) {
		global $wp_query;
		
		/**
		 * Detect if we already have an active request.
		 *
		 * A page request can only be run once per request. Shortcodes and Widgets can run
		 * multiple times per request
		 *
		 * @see $this->wp_query_checker()
		 */
		global $registration_page_request_active;
		
		if ($registration_page_request_active)
		{
			$rawRequest = $wp_query->get('registration_request');
			$request = str_replace('-', '_', $rawRequest);
			$request = explode('/', $request);

			// Check if this is a simple redirect
			$pages = $this->getPages();
			if (empty($request[0]) || empty($pages[$request[0]])) {
				error("Ugyldig henvendelse");
			}
			$page = $pages[$request[0]];
			if (!empty($page['redirect'])) {
			  header("Location: {$page['redirect']}/".implode('/', array_splice($request, 1)));
			  exit;
			}
		  
			/**
			 * Prevent WP from "cleaning-up" views
			 */
			remove_filter('the_content', 'wpautop');
			remove_filter('the_content', 'wptexturize');

			/**
			 * Prevent canonical redirects
			 */
			remove_action('template_redirect', 'redirect_canonical');
			
			/**
			 * $posts will be empty for a controller/subcontroller/whatever call. If so this generates one
			 */
			if (!$posts) {
				if(defined('REGISTRATION_PAGE_CONTROLLER_NAME')) {
					$posts = array(get_page_by_path($_id=REGISTRATION_PAGE_CONTROLLER_NAME));
				}
			}

			// in case we have a permalink based on %postname% only...
			// query will be on e.g. /controller/method... controller and method don't exist as
			// wp pages and the wp_query object will be set up as a 404
			$wp_query->query_vars['error'] = FALSE;
			$wp_query->is_404 = FALSE;

			if (! $page = $request[0])
			{
				error("Ugyldig henvendelse");
			}
			else
			{
				if( isset($request[1]) ) {
					$controller = $request[1];
				}
				else {
					$controller = $request[1] = 'index';
				}
			}

			$controller_filename = $controller.'_controller.php';
			if (!class_exists($_c = $controller.'_controller'))
			{
				if (!file_exists(dirname(realpath(__FILE__)) . '/' . $controller_filename))
				{
					error("Controller file not found: '{$controller_filename}'");
				}
				include($controller_filename);
			}
			if (class_exists($_c))
			{
				$controller = new $_c();
				$controller->set_request($request, $rawRequest);
			}
			else
			{
				error("Class '$controller' is not defined in '$controller_filename'.");
			}

			if (isset($controller))
			{
				$controller->dispatch();
				$posts[0]->post_title = $controller->get_post_title();
				$posts[0]->post_content = $controller->get_post_content();
			}
			else
			{
				$wp_query->is_404 = true;
				$wp_query->post_count = 0;
			}
		}
		
		return $posts;
		
	}
}

function esc_html_recursive( $data = FALSE ) {
	if( ! $data ) return FALSE;

	if( is_array($data) OR is_object($data) ) {
	    foreach( $data AS $key => & $value ) {
	        $key = esc_html( $key );
	        $value = esc_html_recursive( $value );
	    }	    
	}
	else {
	    $data = htmlentities($data,ENT_QUOTES);
	}

	return $data;
}

// Error handling function, courtesy of Tina MVC
function error($msg)
{
	$backtrace = debug_backtrace();
	$base_folder = ABSPATH;
	
	$error  = "<h2>Registration Error</h2>\r\n";
	$error .= "<p><strong>{$msg}</strong></p>\r\n";
	$error .= "<p><strong>Backtrace:</strong><br><em>NB: file paths are relative to '".esc_html_recursive($base_folder)."/wp-content/plugins/registration'</em></p>";
	
	$bt_out  = '';
	
	foreach( $backtrace AS $i => & $b ) {
		
		// tiwen at rpgame dot de comment in http://ie2.php.net/manual/en/function.debug-backtrace.php#65433
		if (!isset($b['file'])) $b['file'] = '[PHP Kernel]';
		if (!isset($b['line'])) {
			$b['line'] = 'n/a';   
		}
		else {
			$b['line'] = vsprintf('%s',$b['line']);
		}
		
		$b['function'] = isset($b['function']) ? esc_html_recursive( $b['function'] ) : '';
		$b['class'] = isset($b['class'])  ? esc_html_recursive( $b['class'] ) : '';
		$b['object'] = isset($b['object']) ? esc_html_recursive( $b['object'] ) : '';
		$b['type'] = isset($b['type']) ? esc_html_recursive( $b['type'] ) : '';
		$b['file'] = isset($b['file']) ? esc_html_recursive(str_replace( $base_folder, '', $b['file'])) : '';
		
		if( !empty($b['args']) ) {
			$args = '';
			foreach ($b['args'] as $j => $a) {
				if (!empty($args)) {
					$args .= "<br>";
				}
				$args .= ' - Arg['.vsprintf('%s',$j).']: ('.gettype($a) . ') '
					  .'<span style="white-space: pre">'.esc_html_recursive(print_r($a,1)).'</span>';
			}
			
			$b['args'] = $args;
		}
		
		$bt_out .= '<strong>['.vsprintf('%s',$i).']: '.$b['file'].' ('.$b['line'].'):</strong><br>';
		$bt_out .= ' - Function: '.$b['function'].'<br>';
		$bt_out .= ' - Class: '.$b['class'].'<br>';
		$bt_out .= ' - Type: '.print_r($b['type'],1).'<br>';
		$bt_out .= ' - Object: '.print_r($b['type'],1).'<br>';
		$bt_out .= $b['args'].'<hr>';
		$bt_out .= "\r\n";
	}
	
	$error .= '<div style="font-size: 70%;">'.$bt_out."</div>\r\n";
			  
	wp_die( $error );
	exit();
}

$registration = new Registration;
?>