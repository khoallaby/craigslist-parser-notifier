<?php

namespace Craigslist;

use MysqliDb, mysqli;


class Database {

	protected static $_instance;
	protected $email, $db_host, $db_user, $db_pass, $db_database, $db, $mysqliLink;
	public $prefix = 'cl_';
	public $total_query_count = 0,
		   $city_query_count  = 0;

	public function __construct() {
		if( !self::getInstance() ) {

			$config = parse_ini_file( dirname(__FILE__) . '/../../db.ini', true );
			$this->email = $config['settings']['email'];
			$this->db_host = $config['database']['host'];
			$this->db_user = $config['database']['user'];
			$this->db_pass = $config['database']['password'];
			$this->db_database = $config['database']['database'];

			$this->mysqliLink = new mysqli( $this->db_host, $this->db_user, $this->db_pass, $this->db_database );
			$this->db = new MysqliDb( $this->mysqliLink );
			$this->db->setPrefix( $this->prefix );

			self::$_instance = $this;
		}


		return $this->db;

	}

	public static function getInstance() {
		return self::$_instance;
	}

	public function getMysqli() {
		return $this->mysqliLink;
	}

	public function getMysqliDb() {
		return $this->db;
	}




	/**
	 * CRUD functions
	 */

	public function insert( $table, array $data ) {
		return $this->db->insert( $table, $data );
	}


	public function get( $table, $data = '*', $where = array(), $compare = array(), $limit = null ) {
		if( !empty( $where ) )
			$this->where( $where, $compare );
		return $this->db->get( $table, $limit, $data );
	}


	public function getOne( $table, $data = '*', $where = array(), $compare = array() ) {
		if( !empty( $where ) )
			$this->where( $where, $compare );
		return $this->db->objectBuilder()->getOne( $table, $data );
	}


	public function update( $table, $data, $where = array(), $compare = array(), $limit = null ) {
		$this->where( $where, $compare );
		return $this->db->update( $table, $data, $limit );
	}


	public function delete( $table, $data, $where = array(), $compare = array(), $limit = null ) {
		$this->where( $where, $compare );
		return $this->db->delete( $table, $data, $limit );
	}


	/**
	 * Builds the where() query using optional comparison operators
	 * @todo: add support for OR where, numerical indices for $compare
	 * @param array $where
	 * @param array $compare
	 */
	private function where( array $where = array(), array $compare = array(), $cond = 'AND' ) {
		if( !empty($where) ) {
			foreach ( $where as $v => $k ) {
				if ( isset( $compare[ $v ] ) )
					$this->db->where( $v, $k, $compare[ $v ], $cond );
				else
					$this->db->where( $v, $k );
			}
		}
	}


	public function addJobs( array $results ) {
		$this->city_query_count = 0;
		foreach( $results as $pid => $job ) {
			if( $this->addJob( $job ) ) {
				$this->city_query_count++;
				$this->total_query_count++;
			}
		}
	}


	public function addJob( array $job ) {

		#$this->db->onDuplicate( $job );
		#return $this->db->insert( 'jobs', $job );
		return $this->db->setQueryOption( Array('LOW_PRIORITY', 'IGNORE') )->insert( 'jobs', $job );

	}










	/**
	 * Data retrieval functions
	 */

	public function getCityCodes( $where = array(), $key = null, $rand = false ) {
		$this->db->join( 'states s', 's.region_id = r.region_id', 'LEFT' );
		$this->db->join( 'cities c', 'c.state_id = s.state_id', 'LEFT' );
		$this->where( $where );
		if( $rand )
			$this->db->orderBy("RAND ()");

		if( !$key )
			$cityCodes = $this->db->map( 'city_id' )->objectBuilder()->get( 'regions r', null, array('c.city_id', 'c.city_code') );
		else
			$cityCodes = $this->db->map( $key )->objectBuilder()->get( 'regions r');

		return $cityCodes;
	}



	public function getCityCodesByCountry( $country ) {
		return $this->getCityCodes( array( 'r.name' => $country ) );
	}


	public function getCityCodesByState( $state ) {
		return $this->getCityCodes( array( 's.state_code' => $state ) );
	}

	// retrieves city codes by their an array of city IDs [ id => code ]
	public function getCityCodesByIds( $codes = array() ) {
		$this->where(
			array('city_id' => $codes ),
			array('city_id' => 'IN' )
		);
		$cityCodes = $this->db->map( 'city_id' )->objectBuilder()->get( 'cities' );
		$cityCodes2 = [];
		foreach( $cityCodes as $k =>$cc ) {
			$cityCodes2[$k] = $cc->city_code;
		}
		return $cityCodes2;
	}






	public function getJobs( $where = array(), $compare = array(), $limit = 50 ) {
		$this->db->join( 'cities c', 'j.city_id = c.city_id', 'LEFT' );
		$this->where( $where, $compare, 'OR' );
		$this->db->orderBy( 'date', 'DESC' );
		$jobs = $this->db->withTotalCount()->objectBuilder()->get(
			'jobs j',
			$limit,
			'j.*, c.city_code, c.name as city_name'
		);
        var_dump($this->db->getLastQuery());
		return $jobs;
	}


	public function getJobsByCountry( ) {

	}


	public function getJobsByState( ) {

	}


	public function getJobsByCity( ) {

	}





}