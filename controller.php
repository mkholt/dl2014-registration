<?php
class Controller {
	protected $postTitle;
	protected $postContent;
	protected $vars = array();
	protected $request;
	protected $raw_request;
	protected $page;

	protected function has_access()
	{
		return (is_user_logged_in() &&
			(appthemes_check_user_role('group_admin') ||
				appthemes_check_user_role('administrator') || appthemes_check_user_role('editor')));
	}

	protected function is_group_admin()
	{
		return appthemes_check_user_role('group_admin');
	}

	protected function is_admin()
	{
		return appthemes_check_user_role('administrator') || appthemes_check_user_role('editor');
	}

	public static function get_plugin_url()
	{
		return plugins_url().'/registration/';
	}

	public static function get_url()
	{
		$home = get_home_url();

		if(defined('REGISTRATION_PAGE_CONTROLLER_NAME'))
		{
			return $home."/".REGISTRATION_PAGE_CONTROLLER_NAME;
		}

		return $home;
	}

	public function set_request($request, $raw_request)
	{
		$this->request = $request;
		$this->raw_request = $raw_request;
		if(count($request)) {
			$this->page = $request[0];
		}
	}

	public function get_post_title()
	{
		return $this->postTitle;
	}

	public function set_post_title($title)
	{
		$this->postTitle = $title;
	}

	public function get_post_content()
	{
		return $this->postContent;
	}

	public function set_post_content($content)
	{
		$this->postContent = $content;
	}

	public function add_var($k, $v)
	{
		$this->vars[$k] = $v;
	}

	public function send_json($ret)
	{
		header("HTTP/1.1 200 OK");
		header("Content-type: application/json");
		echo json_encode($ret);
		exit;
	}

	public function dispatch()
	{
		// Let's clean it up the request a bit
		array_shift($this->request);
		array_shift($this->request);

		if(!$this->request || !is_array($this->request) OR !$this->request[0])
		{
			$this->index();
		}
		else {
			$method = $this->request[0];
			
			if(method_exists($this, $method) && $method[0] !== '_') {
				// check it isn't private
				$m = new \ReflectionMethod($this, $method);
				if($m->isPublic()) {
					array_shift($this->request);
					$this->$method($this->request);
				}
				else
				{
					$this->index($this->request);
				}
			}
			else {
				$this->index($this->request);
			}
		}
	}

	public function load_view($view)
	{
		$vars = &$this->vars;
		$view_filename = dirname(__FILE__).'/views/'.$view.'.php';
		$post_content = $this->parse_view_file($view_filename, $vars);
		if(!$post_content) {
			// if the view data is a string, we'll just output it...
			if( ! $view_filename && is_string($vars)) {
				$post_content .= $vars;
			}
			else {	            
				error("Can't find view file <pre>".$view_filename."</pre>\r\n", $suppress_esc=TRUE);
			}
		}
		return $post_content;
	}

	private function parse_view_file( $f, &$V) {
		if(!file_exists($f)) {
			return FALSE;
		}
		else {
			if(!defined('REGISTRATION_LOAD_VIEW') ) define('REGISTRATION_LOAD_VIEW',true);
			ob_start();
			echo "<!--// VIEW FILE START: $f //-->\r\n";
			if( ! empty( $V ) ) {
				if( ! is_array($V) ) {
					$V = get_object_vars($V);
				}
				extract($V);
			}
			include($f);
			echo "<!--// VIEW FILE END: $f //-->\r\n";
			return ob_get_clean();
		}
	}
}
?>