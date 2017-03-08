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


	public function parseByCodes( array $cityCodes = array() ) {
		foreach( $cityCodes as $cityCode ) {
			$this->parseRss( $cityCode );
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