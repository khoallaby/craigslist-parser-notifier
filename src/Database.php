<?php

namespace Craigslist;

use MysqliDb, dbObject;


class Database {

	protected static $_instance;
	protected $email, $config, $db_host, $db_user, $db_pass, $db_database, $db;
	protected $jobs, $cities;
	protected $prefix = 'cl_';

	public function __construct() {

		$config = parse_ini_file( dirname(__FILE__) . '/../db.ini', true );
		$this->config = $config;
		$this->email = $config['settings']['email'];
		$this->db_host = $config['database']['host'];
		$this->db_user = $config['database']['user'];
		$this->db_pass = $config['database']['password'];
		$this->db_database = $config['database']['database'];

		#$this->db = DB::getInstance( $this->db_host, $this->db_user, $this->db_pass, $this->db_database );
		$this->db = new MysqliDb( Array (
			'host' => $this->db_host,
			'username' => $this->db_user,
			'password' => $this->db_pass,
			'db'=> $this->db_database,
			'prefix' => $this->prefix
		) );


		$this->jobs = dbObject::table( 'jobs' );
		$this->cities = dbObject::table( 'cities' );

		self::$_instance = $this;

		return $this->db;

	}

	public static function getInstance() {
		return self::$_instance;
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


	public function get( $table, $data, $where = array(), $compare = array(), $limit = null ) {
		$this->where( $where, $compare );
		return $this->db->get( $table, $data, $limit );
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
	private function where( array $where = array(), array $compare = array() ) {
		if( !empty($where) ) {
			foreach ( $where as $v => $k ) {
				if ( isset( $compare[ $v ] ) )
					$this->db->where( $v, $k, $compare[ $v ] );
				else
					$this->db->where( $v, $k );
			}
		}
	}



	public function getCityCodes( $where = array() ) {
		$this->db->join( 'states s', 's.region_id = r.region_id', 'LEFT' );
		$this->db->join( 'cities c', 'c.state_id = s.state_id', 'LEFT' );
		$this->where( $where );
		$cityCodes = $this->db->getValue( 'regions r', 'c.code', null );

		return $cityCodes;
	}



	public function getCityCodesByCountry( $country ) {
		return $this->getCityCodes( array( 'r.name' => $country ) );
	}


	public function getCityCodesByState( $state ) {
		return $this->getCityCodes( array( 's.state_code' => $state ) );
	}




	public function addJobs( array $results ) {
		foreach( $results as $pid => $job )
			$this->addJob( $job );
	}


	public function addJob( array $job ) {

		#$this->db->onDuplicate( $job );
		#return $this->db->insert( 'jobs', $job );
		return $this->db->setQueryOption( 'IGNORE' )->insert( 'jobs', $job );

	}








}