<?php

namespace Craigslist;


class WebUI {

	protected $dbInstance, $config;


	public function __construct( ) {
		require_once dirname(__FILE__) . '/../../vendor/autoload.php';
		$this->dbInstance = \Craigslist\Database::getInstance();
		if( empty( $this->dbInstance ) )
			$this->dbInstance = new \Craigslist\Database();

		$this->index();
	}



	public function index() {
		$params     = explode( '/', $_SERVER['REQUEST_URI'] );
		$page       = $params[1];
		$safe_pages = array( 'cron', 'api' );
		if(in_array($page, $safe_pages)) {
			if( $page == 'cron' )
				require dirname(__FILE__) . '/../inc/cron.php';
			else
				require $page . '.php';
		} else {
			require dirname(__FILE__) . '/../inc/home.php';

		}

	}

}