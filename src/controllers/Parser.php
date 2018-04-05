<?php

namespace Craigslist;

//use Sunra\PhpSimple\HtmlDomParser;

class Parser {

	protected $dbInstance, $config, $cityId, $proxies;


	public function __construct( array $config = array() ) {
		$this->config = $config;

		// set timeout limits
		$timeout = 60*60*4; // 4 hours
		ini_set( 'mysqli.reconnect', 1 );
		ini_set( 'mysql.connect_timeout', $timeout );
		ini_set( 'default_socket_timeout', $timeout );
		set_time_limit( $timeout );

        ini_set('max_execution_time', $timeout );
        set_time_limit( $timeout );

		$this->dbInstance = \Craigslist\Database::getInstance();
		if( empty( $this->dbInstance ) )
			$this->dbInstance = new \Craigslist\Database();

	}

	public function editConfig( array $config = array() ) {
		$this->config = $config;
	}

	private function sleepRandom( $start = 1, $stop = 120, $seed = null ) {
		if( isset($this->config['sleep']) && $this->config['sleep'] ) {
			// allows sleep to be set via config
			if( is_numeric($this->config['sleep']) ) {
				$stop = $this->config['sleep'];
			} elseif( is_array($this->config['sleep']) ) {
				$start = $this->config['sleep'][0];
				$stop = $this->config['sleep'][1];
			}

			if ( !$seed )
				$seed =( mt_rand() / mt_getrandmax() );
			$stop = $stop * $seed;
			$sleep = mt_rand( $start, round( $stop ) + $start );
			sleep( $sleep );
			return $sleep;
		}
		return 0;
	}

	private function debugMessage( $msg = '' ) {
		if( isset($this->config['debug']) && $this->config['debug'] ) {
			echo $msg ."\n";
			ob_flush();
		}
	}








	public function getUrlByCity( $cityCode, $config = array() ) {
		if( empty($config) )
			$config = $this->config;

		$url =  'https://' . $cityCode . '.craigslist.org/search/';
		$url .= sprintf( '%s?format=rss&query=%s%s%s',
			urlencode( $this->config['category'] ),
			urlencode( $this->config['search'] ),
			$this->config['postedToday'] ? '&postedToday=1' : '',
			isset( $this->config['exclude'] ) ? '&excats=' . urlencode( $this->config['exclude'] ) : ''
		);

		return $url;
	}


	public function parseByCodes( array $cityCodes = array() ) {
		global $timeStart;
		foreach( $cityCodes as $cityId => $cityCode ) {
			$this->cityId = $cityId;
			$ping = mysqli_ping( $this->dbInstance->getMysqli() );
			$this->debugMessage( sprintf('Parsing: <b>%s</b><br />', $cityCode) );
			$this->debugMessage( sprintf('Mysqli ping: <b>%s</b><br />', $ping ? 'true' : 'false') );
			if( $timeStart ) {
				$time = microtime(true) - $timeStart;
				$this->debugMessage( sprintf('Time elapsed: <b>%s</b><br />', $time ) );
			}
			$this->parseByCity( $cityCode );
			$sleep = $this->sleepRandom();
			$this->debugMessage( sprintf('Slept for : <b>%s</b> seconds<br />', $sleep) );
			$this->debugMessage( sprintf('# of posts : <b>%s/%s</b><br /><br />', $this->dbInstance->city_query_count, $this->dbInstance->total_query_count) );
		}
	}


	public function parseByCity( $cityCode ) {

		$url = sprintf( 'search/%s?format=rss&query=%s%s%s',
			urlencode( $this->config['category'] ),
			urlencode( $this->config['search'] ),
			$this->config['postedToday'] ? '&postedToday=1' : '',
			isset( $this->config['exclude'] ) ? '&excats=' . urlencode( $this->config['exclude'] ) : ''
		);

		$results = $this->parseRss( $cityCode );

		// add/modify data before adding it to DB
		$newResults = array_map(function($job) {
			$cat = explode('/', parse_url( $job['link'], PHP_URL_PATH ) );
			$job['pid'] = $job['id'];
			$job['city_id'] = $this->cityId;
			$job['cat'] = $cat[1];
			$job['date_modified'] = $this->dbInstance->getMysqliDb()->now();
			unset($job['id']);
			return $job;
		}, $results);


		$this->dbInstance->addJobs( $newResults );


		return $newResults;

	}


	public function parseRss( $cityCode ) {
		$url = $this->getUrlByCity( $cityCode );

		if( isset($this->config['proxy']) ) {
			$contents = $this->getProxyContents( $url );
		} else {
			$contents = file_get_contents( $url );
		}

		$posts = simplexml_load_string(utf8_encode($contents));

		$results = array();
		foreach( $posts as $post ) {
			$url = explode( '.', basename( $post->link ) );
			$pid = $url[0];
			$children = $post->children("dc", true);

			$results[$pid] = array(
				'id'          => $pid,
				'link'        => (string) $post->link,
				'title'       => (string) $post->title,
				'description' => (string) $post->description,
				'date'        => (string) $children->date
			);
		}

		return $results;
	}







	private function getProxies() {
		$file = dirname(__FILE__) . '/../inc/proxies.json';
		$file = file_get_contents( $file );
		return json_decode($file);
	}



	public function getProxyContents( $url ) {
		if( !$this->proxies )
			$this->proxies = $this->getProxies();

		ob_implicit_flush(true);
		ob_start();
		// get a random proxy
		$proxy = $this->proxies[mt_rand(0, count($this->proxies) - 1)];
		$proxyUrl = $proxy->ip . ':' . $proxy->port;

		// create curl resource
		$ch = curl_init();

		// set options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // read more about HTTPS http://stackoverflow.com/questions/31162706/how-to-scrape-a-ssl-or-https-url/31164409#31164409
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);

		$posts = simplexml_load_string(utf8_encode($output));

		return $output;
	}


}