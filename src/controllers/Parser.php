<?php

namespace Craigslist;


class Parser {

	protected $dbInstance, $config;


	public function __construct( array $config = array() ) {
		$this->config = $config;
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
			echo $msg;
			ob_flush();
		}
	}


	public function parseByCodes( array $cityCodes = array() ) {
		foreach( $cityCodes as $cityCode ) {
			set_time_limit(60*10);
			$this->debugMessage( sprintf('Parsing: <b>%s</b><br />', $cityCode) );
			$this->parseRss( $cityCode );
			$sleep = $this->sleepRandom();
			$this->debugMessage( sprintf('Slept for : <b>%s</b> seconds<br />', $sleep) );
			$this->debugMessage( sprintf('# of posts : <b>%s/%s</b><br /><br />', $this->dbInstance->city_query_count, $this->dbInstance->total_query_count) );
		}
	}


	public function parseRss( $cityCode ) {

		$url = sprintf( 'search/%s?format=rss&query=%s%s%s',
			urlencode( $this->config['category'] ),
			urlencode( $this->config['search'] ),
			$this->config['postedToday'] ? '&postedToday=1' : '',
			isset( $this->config['exclude'] ) ? '&excats=' . urlencode( $this->config['exclude'] ) : ''
		);


		$request = new \Craigslist\CraigslistRequest( array(
			'city' => $cityCode,
			'url' => $url,
			#'category' => $this->config['category'],
			#'query' => $this->config['search'],
			#'follow_links' => true,
		) );

		$api = new \Craigslist\CraigslistApi();
		$results = $api->get($request);

		// add/modify data before adding it to DB
		$newResults = array_map(function($job) {
			$cat = explode('/', parse_url( $job['link'], PHP_URL_PATH ) );
			$job['pid'] = $job['id'];
			$job['cat'] = $cat[1];
			$job['date_modified'] = $this->dbInstance->getMysqliDb()->now();
			unset($job['id']);
			return $job;
		}, $results);


		$this->dbInstance->addJobs( $newResults );


		return $newResults;

	}


}