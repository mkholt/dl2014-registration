<?php
class oversigt_controller extends Controller {
	public function __construct()
	{
		setlocale(LC_ALL, "da_DK");

		// Register style sheet and scripts
		require_once("index_controller.php");
		add_action('wp_enqueue_scripts', array("index_controller", 'registerStyles'));

		// Register header scripts
		add_action('wp_head', array("index_controller", 'registerHead'));
		add_action('wp_head', array($this, 'registerHead'));
	}

	public function registerHead()
	{
		wp_register_script('overview-script', self::get_plugin_url() . 'js/overview.js');
		wp_enqueue_script('overview-script');
	}

	public function index()
	{		
		$this->set_post_title('Forhåndstilmelding');

		if (!$this->has_access())
		{
			wp_redirect($this->get_url() . "/logon");
			exit;
		}
		elseif ($this->is_group_admin())
		{
			wp_redirect($this->get_url() . "/tilmeldinger");
			exit;
		}
		elseif ($this->is_admin())
		{
			require_once('index_model.php');
			$im = new index_model();

			$registrations = $im->get_pre_registration(get_users(array('role' => 'group_admin')));

			$this->add_var('ages'			, RegistrationConfig::$ages);
			$this->add_var('user'			, wp_get_current_user());
			$this->add_var('registrations'	, $registrations);
			$this->add_var('organizations'	, RegistrationConfig::$organizations);
			$content = $this->load_view('overview');
			$this->set_post_content($content);
		}
		else
		{
			wp_redirect(site_url() . "/logon");
			exit;
		}
	}

	private function _handle_add_update($result, $sMessage)
	{
		$status	= $result['status'];
		$data	= $result['data'];

		$message = "";
		switch($status)
		{
			case -1:
				$message = "Du skal angive det samme kodeord i begge felter.";
				break;
			case -2:
				$message = "Du skal angive et kodeord";
				break;
			case -3:
				$message = "Du skal angive et navn";
				break;
			case -4:
				$message = "Du skal angive et korps";
				break;
			case -5:
				$message = implode("<br/>", $result['errors']);
				break;
			case 0:
				$message = $sMessage;
				break;
		}

		$this->send_json(array(
			"status" => ($status == 0),
			"message" => $message,
			"data" => $data
		));
	}

	private function _error($msg)
	{
		$this->send_json(array(
			"status" => false,
			"message" => $msg,
			"data" => null
		));
	}

	public function add_user()
	{
		if (!$this->has_access() || !$this->is_admin())
		{
			$this->_error("Adgang nægtet");
		}

		require_once('index_model.php');
		$im = new index_model();

		$result = $im->add_user($_POST['name'], $_POST['email'], $_POST['organization'], $_POST['password'], $_POST['repeatPass']);
		$this->_handle_add_update($result, "Gruppen blev tilføjet.");
	}

	public function update_user()
	{
		if (!$this->has_access() || !$this->is_admin())
		{
			$this->_error("Adgang nægtet");
		}

		require_once('index_model.php');
		$im = new index_model();

		$result	= $im->update_user($_POST['id'], $_POST['name'], $_POST['email'], $_POST['organization'], $_POST['password'], $_POST['repeatPass']);
		$this->_handle_add_update($result, "Gruppen blev opdateret.");
	}

	public function delete_user()
	{
		if (!$this->has_access() || !$this->is_admin())
		{
			$this->_error("Adgang nægtet");
		}

		require_once('index_model.php');
		$im = new index_model();
		$status = $im->delete_user($_POST['id']);

		$this->send_json(array(
			"status" => $status,
			"message" => ($status) ? "Gruppen blev fjernet." : "Der skete en fejl, prøv venligst igen."
		));
	}
}
?>