<?PHP
class RegistrationConfig
{
	public static $pages = array(
		array(
			'page_title'=>'Forhåndstilmelding',
			'page_name'=>'forhaandstilmelding',
			'page_status'=>'published',
			'page_password'=>'xxxxxxxx'
		)
	);
	public static $rates = array("2014-02-14", "2014-03-16", "2014-06-15");
	public static $account = "xxxx xxxxxxx";

	public static $ages = array(
		array(
			"title" => "Lederbarn (2-4 år)",
			"age" => array(2, 4),
			"default" => "full",
			"price" => array(
				"half" => 250,
				"full" => 550
			),
			"rate" => array(
				"half" => array(250, 0, 0),
				"full" => array(250, 300, 0)
			)
		),
		array(
			"title" => "5-9 år",	
			"age" => array(5, 9),
			"default" => "half",
			"price" => array(
				"half" => 500,
				"full" => 1100
			),
			"rate" => array(
				"half" => array(250, 250, 0),
				"full" => array(250, 425, 425)
			)
		),
		array(
			"title" => "10-16 år",
			"age" => array(10, 16),
			"default" => "full",
			"price" => array(
				"half" => 500,
				"full" => 1100
			),
			"rate" => array(
				"half" => array(250, 250, 0),
				"full" => array(250, 425, 425)
			)
		),
		array(
			"title" => "Senior/Rover/Ranger",
			"age" => array(17, 20),
			"default" => "full",
			"price" => array(
				"half" => 500,
				"full" => 1100
			),
			"rate" => array(
				"half" => array(250, 250, 0),
				"full" => array(250, 425, 425)
			)
		),
		array(
			"title" => "Ledere",		
			"age" => array(21, null),
			"default" => "full",
			"price" => array(
				"half" => 500,
				"full" => 1100
			),
			"rate" => array(
				"half" => array(250, 250, 0),
				"full" => array(250, 425, 425)
			)
		)
	);

	public static $organizations = array(
		'hm'	=> 'Hummelbierne',
		'kfum'	=> 'KFUM-spejderne i Danmark',
		'dds'	=> 'Det Danske Spejderkorps',
		'dgp'	=> 'De grønne pigespejdere',
		'fdf'	=> 'FDF',
		'sgg'	=> 'Sct. Georgs Gilderne i Danmark'
	);
}
?>