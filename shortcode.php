<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of shortcode
 *
 * @author morten
 */
class ShortCode {
    public function __construct()
    {
        require_once('config.php');
        require_once('index_model.php');
        add_shortcode('groups', array($this, 'groups'));
    }
    
    public function groups()
    {
        $ret = "<ul>";
        
        /* @var $users WP_User[] */
        $users = get_users(array('role' => 'group_admin'));
        $im = new index_model();
        $registrations = $im->get_registration($users);
        
        $sortedUsers = array();
        foreach ($registrations as $group)
        {
            if (empty($group['registrations'])) continue;
            
            $org = strtolower(substr($group['user']->get('last_name'), 1, -1));
            
            if (empty($sortedUsers[$org]))
            {
                $sortedUsers[$org] = array();
            }
            $sortedUsers[$org][] = $group;
        }
       
        foreach (RegistrationConfig::$organizations as $short => $org)
        {
            if (empty($sortedUsers[$short]))
            {
                continue;
            }
            
            $ret .= "<li><strong>{$org}</strong><ul>";
            foreach ($sortedUsers[$short] as $group)
            {
                $ret .= "<li>{$group['user']->get('first_name')}</li>";
            }
            $ret .= "</ul></li>";
        }
        
        return $ret."</ul>";
    }
}
