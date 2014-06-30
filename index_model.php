<?php
class index_model {
    const META_FIELD = 'preregistrations';
	const META_FINAL_FIELD = 'registrations_final';
    const META_RACE_FIELD = 'race_amount';

	public function __construct()
	{
		foreach (RegistrationConfig::$ages as &$age)
		{
			$age['key'] = $this->get_age_key($age);
		}
	}

    public function get_age_key($age)
    {
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ", '-', $age['title'])));
    }

    /**
     * @param int|null $uId
     * @return array|bool|mixed
     */
    public function get_registration($uId = null)
    {
        if (empty($uId))
        {
            $uId = get_current_user_id();
        }

        if ($uId == 0) return false;

        $ret = array();
        if (is_array($uId))
        {
            foreach ($uId as $user)
            {
                $ret[$user->ID] = array('final' => $this->get_finalized_status($user->ID), 'registrations' => $this->get_registration($user->ID), 'user' => $user);
            }
        }
        else
        {
            $ret = get_user_meta($uId, self::META_FIELD, true);
        }

        return $ret;
    }

    public function finalize_registration($uId = null)
    {
        if (empty($uId))
        {
            $uId = get_current_user_id();
        }

        if ($uId == 0) return false;

        $date = date('Y-m-d H:i:s');
        $data = array(
            'final' => true,
            'time' => $date
        );
        update_user_meta($uId, self::META_FINAL_FIELD, $data);

        return (get_user_meta($uId, self::META_FINAL_FIELD, true) == $data);
    }

    public function unfinalize_registration($uId = null)
    {
        if (empty($uId))
        {
            $uId = get_current_user_id();
        }

        if ($uId == 0) return false;

        delete_user_meta($uId, self::META_FINAL_FIELD);

        return (get_user_meta($uId, self::META_FINAL_FIELD, true) == null);
    }

    public function get_finalized_status($uId = null)
    {
        if (empty($uId))
        {
            $uId = get_current_user_id();
        }

        if ($uId == 0) return false;

        $ret = get_user_meta($uId, self::META_FINAL_FIELD, true);
		if (!empty($ret['time'])) $ret['time'] = new DateTime($ret['time']);

        return $ret;
    }

	public function set_pre_registration($data, $email, $race, $uId = null)
	{
		if (empty($uId))
		{
			$uId = get_current_user_id();
		}

		if ($uId == 0) return -1;

		$eStatus = $this->set_email($email, $uId);
		if (!$eStatus) return -2;

        update_user_meta($uId, self::META_RACE_FIELD, $race);
        if (get_user_meta($uId, self::META_RACE_FIELD, true) != $race) return -3;

		update_user_meta($uId, self::META_FIELD, $data);
		return (get_user_meta($uId, self::META_FIELD, true) == $data);
	}

	public function set_email($email, $uId = null)
	{
		if (empty($uId))
		{
			$uId = get_current_user_id();
		}

		if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) return false;
		
		return wp_update_user(array(
			'ID' => $uId,
			'user_email' => $email
		)) == $uId;
	}

    public function get_race_amount($uId = null)
    {
        if (empty($uId))
        {
            $uId = get_current_user_id();
        }

        if ($uId == 0) return false;

        return get_user_meta($uId, self::META_RACE_FIELD, true);
    }

    public function add_user($name, $email, $org, $pass, $rPass)
	{
		if (empty($name))
		{
			return array('status' => -3, 'data' => null);
		}

		if (empty($org))
		{
			return array('status' => -4, 'data' => null);
		}

		if (empty($pass))
		{
			return array('status' => -2, 'data' => null);
		}

		if ($pass != $rPass)
		{
			return array('status' => -1, 'data' => null);
		}

		$o = strtoupper($org);
		$login = strtolower(str_replace(array(" ", "æ", "ø", "å", "Æ", "Ø", "Å"), array("-", "ae", "oe", "aa", "ae", "oe", "aa"), $name)) . "-" . $org;
		$d = array(
			'user_login' => $login,
			'user_pass' => $pass,
			'first_name' => $name,
			'last_name' => "($o)",
			'display_name' => "{$name} ($o)",
			'role' => 'group_admin'
		);

		if (!empty($email))
		{
			$d['user_email'] = $email;
		}

		$id = wp_insert_user($d);
		if (is_wp_error($id))
		{
			$errors = array();
			foreach ($id->errors as $errCode)
			{
				foreach ($errCode as $e)
				{
					$errors[] = $e;
				}
			}
			return array('status' => -5, 'data' => null, 'errors' => $errors);
		}
		$d['id'] = $id;

		return array('status' => 0, 'data' => $d);
	}

	public function update_user($uId, $name, $email, $org, $pass, $rPass)
	{
		if (empty($uId))
		{
			$uId = get_current_user_id();
		}

		if ($uId == 0) return array('status' => false, 'data' => null);

		if ($pass != $rPass)
		{
			return array('status' => -1, 'data' => null);
		}

		$o = strtoupper($org);
		$d = array(
			'ID' => $uId,
			'first_name' => $name,
			'last_name' => "($o)",
			'display_name' => "{$name} ($o)");

		if (!empty($pass))
		{
			$d['user_pass'] = $pass;
		}

		if (!empty($email))
		{
			$d['user_email'] = $email;
		}

		wp_update_user($d);

		return array('status' => 0, 'data' => $d);
	}

	public function delete_user($uId)
	{
		require_once(ABSPATH.'wp-admin/includes/user.php' );
		
		if (empty($uId))
		{
			$uId = get_current_user_id();
		}

		if ($uId == 0) return false;

		return wp_delete_user($uId);
	}
}
?>