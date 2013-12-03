<?php
class index_controller extends Controller
{
	public function __construct() {
		setlocale(LC_ALL, "da_DK");

		// Register style sheet and scripts
		add_action('wp_enqueue_scripts', array($this, 'registerStyles'));

		// Register header scripts
		add_action('wp_head', array($this, 'registerHead'));
	}

	public static function registerStyles() {
		wp_register_style('index-style', self::get_plugin_url() .'css/index.css');
		wp_register_style('jquery-ui', self::get_plugin_url() . 'css/smoothness/jquery-ui-1.10.3.custom.css');
		
		wp_register_script('prereg-script', self::get_plugin_url() . 'js/script.js');
		wp_register_script('blockui', self::get_plugin_url() . 'js/jquery.blockUI.js');
		wp_register_script('jquery-ui', self::get_plugin_url() . 'js/jquery-ui-1.10.3.custom.js');
		wp_register_script('jquery-ui-da', self::get_plugin_url() . 'js/jquery.ui.datepicker-da.js');
		wp_register_script('moment', self::get_plugin_url() . 'js/moment.min.js');

		wp_enqueue_style('index-style');
		wp_enqueue_style('jquery-ui');

		wp_enqueue_script('blockui');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-da');
		wp_enqueue_script('moment');
		wp_enqueue_script('prereg-script');
	}

	public static function registerHead() {
		$nAges = array();
		foreach (RegistrationConfig::$ages as $a) {
			$nAges[$a['key']] = $a;
		}
		$json = json_encode($nAges);
		$url = self::get_url() ;

		echo <<<END
			<script type="text/javascript">
				var page_url = '{$url}';
				var ages = {$json};
			</script>
END;
	}

	public function index() {
		$this->set_post_title('Forhåndstilmelding');

		$loggedIn = (is_user_logged_in() && (appthemes_check_user_role('group_admin') || appthemes_check_user_role('administrator') || appthemes_check_user_role('editor')));
		if (!$loggedIn)
		{
			wp_redirect($this->get_url() . "/logon");
			exit;
		}
		elseif (appthemes_check_user_role('group_admin'))
		{
			wp_redirect($this->get_url() . "/tilmeldinger");
			exit;
		}
		elseif (appthemes_check_user_role('administrator') || appthemes_check_user_role('editor'))
		{
			wp_redirect($this->get_url() . "/oversigt");
			exit;
		}
	}
}
?>