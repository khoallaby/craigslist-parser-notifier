<?php

namespace Craigslist;


class WebUI {

	protected $dbInstance, $config;
	static $timeStart;


	public function __construct( ) {
		$this->dbInstance = \Craigslist\Database::getInstance();
		if( empty( $this->dbInstance ) )
			$this->dbInstance = new \Craigslist\Database();
		$this->index();
	}

	public static function header() {
		self::$timeStart = microtime(true);
		require dirname(__FILE__) . '/../inc/header.php';
		require dirname(__FILE__) . '/../inc/menu.php';
	}

	public static function footer() {
		require dirname(__FILE__) . '/../inc/footer.php';
	}


	public function index() {
		$params     = explode( '/', $_SERVER['REQUEST_URI'] );
		$page       = $params[1];
		$safe_pages = array( 'cron', 'api' );
		if(in_array($page, $safe_pages)) {
			if( $page == 'cron' ) {
				require dirname( __FILE__ ) . '/../inc/cron.php';
			} elseif( $page == 'api' ) {
				\Craigslist\Api::init();


			} else {
				require dirname( __FILE__ ) . '/../inc/home.php';
			}
				#require $page . '.php';
		} else {
			require dirname(__FILE__) . '/../inc/home.php';

		}

	}


	public static function filterContent( $job ) {
		# adds links to urls
		$job->description = preg_replace( '/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0" target="_blank">$0</a>', $job->description);
		# adds permalink to [...]
		$job->description = str_replace( '[...]', '<a href=' . $job->link . ' target="_blank">[...]</a>', $job->description );
		return $job;
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