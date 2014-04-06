<?php
class logon_controller extends Controller {
	public function __construct()
	{
		setlocale(LC_ALL, "da_DK");

		// Register style sheet and scripts
		require_once("index_controller.php");
		add_action('wp_enqueue_scripts', array("index_controller", 'registerStyles'));
	}

	public function index()
	{
		if ($this->has_access() || $this->doLogin())
		{
			wp_redirect($this->get_url());
			exit;
		}
	}

	private function doLogin()
	{
		if (!empty($_POST['log']))
		{
			$u = wp_signon(null, false);
			if (!is_wp_error($u))
			{
				return true;
			}

			$this->add_var("error", __('Incorrect username or password.'));
		}
		
		$this->set_post_title('Tilmelding');
		$this->set_post_content( $this->load_view('login'));
		return false;
	}
}
?>