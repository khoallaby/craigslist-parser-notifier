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

		return $this->db;

	}

	public static function getInstance() {
		return self::$_instance;
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

}