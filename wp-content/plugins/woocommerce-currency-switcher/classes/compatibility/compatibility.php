<?php
if (!defined('ABSPATH')) {
    exit;
}

class WOOCS_compatibility {
	
	public function __construct() {
		//SUMO Subscriptions
		include_once WOOCS_PATH . 'classes/compatibility/sumo_subscriptions.php';
	}
	
}

