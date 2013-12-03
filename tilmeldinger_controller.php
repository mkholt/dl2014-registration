<?php
class tilmeldinger_controller extends Controller {
	public function __construct()
	{
		setlocale(LC_ALL, "da_DK");

		// Register style sheet and scripts
		require_once("index_controller.php");
		add_action('wp_enqueue_scripts', array("index_controller", 'registerStyles'));

		// Register header scripts
		add_action('wp_head', array("index_controller", 'registerHead'));

		// Hide the toolbar if the current use is a group admin
		add_filter('show_admin_bar', array($this,'admin_bar'));
	}

	public function admin_bar()
	{
		return !$this->is_group_admin();
	}

	public function index()
	{
		$this->set_post_title('Forhåndstilmelding');

		if (!$this->has_access())
		{
			wp_redirect($this->get_url() . "/logon");
			exit;
		}

		require_once('index_model.php');
		$im = new index_model();

		$userId = $this->get_user_id(false);
		$user = get_user_by('id', $userId);
		$admin = ($this->is_admin()) ? wp_get_current_user() : null;
		$this->add_var('admin'			, $admin);
		$this->add_var('rates'			, RegistrationConfig::$rates);
		$this->add_var('account'		, RegistrationConfig::$account);
		$this->add_var('ages'			, RegistrationConfig::$ages);
		$this->add_var('user'			, $user);
		$this->add_var('registrations'	, $im->get_pre_registration($userId));
		$this->set_post_content($this->load_view('register'));
	}

	private function get_user_id($json = true)
	{
		$userId = 0;
		if ($this->is_admin())
		{
			if (empty($this->request) || (int)$this->request[0] <= 0)
			{
				if ($json)
					$this->send_json(array("status" => false, "Du skal angive et gruppe ID"));
				else
				{
					wp_redirect($this->get_url() . "/oversigt");
					exit;
				}
			}
			$userId = array_shift($this->request);
		}
		else
		{
			$userId = wp_get_current_user()->get('id');
		}

		if ($userId == 0 || !appthemes_check_user_role('group_admin', $userId))
		{
			if ($json)
				$this->send_json(array("status" => false, "Du skal angive et gruppe ID"));
			else
			{
				wp_redirect($this->get_url());
				exit;
			}
		}

		return $userId;
	}

	public function save()
	{
		if (!$this->has_access())
		{
			$this->send_json(array(
				"status" => false,
				"message" => "Adgang nægtet"
			));
		}

		require_once('index_model.php');
		$im = new index_model();

		$userId = $this->get_user_id();
		$status = $im->set_pre_registration($_POST['registrations'], $_POST['email'], $userId);
		$this->send_json(array(
			"status" => $status,
			"message" => ($status === true) ? "Din forhåndstilmelding blev opdateret." : "Der skete en fejl, prøv venligst igen.",
		));
	}
}
?>