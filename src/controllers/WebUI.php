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



	function getNiceDuration($durationInSeconds) {

		$duration = '';
		$days = floor($durationInSeconds / 86400);
		$durationInSeconds -= $days * 86400;
		$hours = floor($durationInSeconds / 3600);
		$durationInSeconds -= $hours * 3600;
		$minutes = floor($durationInSeconds / 60);
		$seconds = $durationInSeconds - $minutes * 60;

		if($days > 0) {
			$duration .= $days . ' days';
		}
		if($hours > 0) {
			$duration .= ' ' . $hours . ' hours';
		}
		if($minutes > 0) {
			$duration .= ' ' . $minutes . ' minutes';
		}
		if($seconds > 0) {
			$duration .= ' ' . $seconds . ' seconds';
		}
		return $duration;
	}


}