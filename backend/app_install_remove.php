<?php
function save_error(){
	update_option('plugin_error',  ob_get_contents());
}

class AppInstallRemove
{
	private $roles = array('group_admin' => 'Gruppe administrator');

	public function install()
	{
		add_action('activated_plugin','save_error');
		
		require_once(dirname(__FILE__).'/../config.php');

		foreach (RegistrationConfig::$pages as $p)
		{
			$the_page = get_page_by_path($p['page_name']);

			if (empty($the_page))
			{
				// Create post object
				$_p = array();
				$_p['post_title'] = $p['page_title'];
				
				$_p['post_parent'] = 0;                
				$_p['post_name'] = $p['page_name'];
				$_p['post_content'] = "";
				$_p['post_status'] = $p['page_status'];
				$_p['post_password'] = (!empty($p['page_password'])) ? $p['page_password'] : '';
				$_p['post_type'] = 'page';
				$_p['menu_order'] = 0;
				$_p['comment_status'] = 'closed';
				$_p['comment_count '] = 0;
				$_p['ping_status'] = 'closed';
				$_p['post_category'] = array(1);
				
				// Insert the post into the database
				$the_page_id = wp_insert_post($_p);
			}
			else {
				$the_page_id = $the_page->ID;
				
				// make sure the page is not trashed...
				$the_page->post_status = $p['page_status'];
				$the_page_id = wp_update_post($the_page);
			}
		}

		/**
		* Create custom roles
		*/
		$this->createRoles();

		/**
		* Create users for the groups
		*/
		$this->createGroupAdmins();
	}

	public function remove()
	{
		/**
		* Remove custom roles
		*/
		//$this->removeRoles();
	}

	private function createRoles()
	{
		global $wp_roles;

		foreach ($this->roles as $role => $title)
		{
			if (!$wp_roles->get_role($role))
			{
				$wp_roles->add_role($role, $title, false);
			}
		}
	}

	private function removeRoles()
	{
		global $wp_roles;
		
		foreach ($this->roles as $role => $title)
		{
			if ($wp_roles->get_role($role))
			{
				$wp_roles->remove_role($role);
			}
		}
	}

	private function createGroupAdmins()
	{
		$first = true;
		$file = dirname(__FILE__)."/users.csv";
		if (!is_file($file))
		{
			trigger_error("File does not exist: {$file}", E_USER_ERROR);
		}

		$fh = fopen($file, "r");
		if (!$fh)
		{
			trigger_error("Could not load file: {$file}", E_USER_ERROR);
		}

		$groups = array();
		$k = null;
		while (($data = fgetcsv($fh)) !== false)
		{
			if ($first)
			{
				$first = false;
				$k = $data;
				continue;
			}

			$g = array('role' => 'group_admin');
			for ($i = 0; $i < count($k); $i++)
			{
				$g[$k[$i]] = $data[$i];
			}
			wp_insert_user($g);
		}
		// foreach (RegistrationConfig::$groups as $group)
		// {
		// 	wp_insert_user(array_merge(array('role' => 'group_admin'), $group));
		// }
	}
}
?>