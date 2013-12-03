<?php
require_once('config.php');

$words = array(
	"mandag", "tirsdag", "onsdag", "torsdag", "fredag", "lørdag", "søndag",
	"januar", "februar", "marts", "april", "maj", "juni", "juli", "august", "september", "oktober", "november", "december"
);

$fh = fopen(dirname(__FILE__).'/../../uploads/users.csv', 'w');
fputcsv($fh, array("name", "organization", "user", "pass"), ';');

$groups = array();
foreach (RegistrationConfig::$groups as $group)
{
	$login = strtolower(str_replace(array(" ", "æ", "ø", "å", "Æ", "Ø", "Å"), array("-", "ae", "oe", "aa", "ae", "oe", "aa"), $group['name'])) . "-" . $group['organization'];
	$pass = ucfirst($words[rand(0,count($words)-1)]).rand(1000,9999);
	$o = strtoupper($group['organization']);
	
	fputcsv($fh, array($group['name'], $o, $login, $pass), ';');

	$groups[] = array_merge($group, array('user_login' => $login, 'user_pass' => $pass, 'last_name' => "({$o})", 'display_name' => "{$group['name']} ($o)"));

	// wp_insert_user(array(
	// 	'user_login' => $login,
	// 	'user_pass' => $pass,
	// 	'first_name' => $group['name'],
	// 	'last_name' => "($o)",
	// 	'display_name' => "{$group['name']} ($o)",
	// 	'role' => 'group_admin'
	// ));
}
fclose($fh);

echo var_export($groups);
?>